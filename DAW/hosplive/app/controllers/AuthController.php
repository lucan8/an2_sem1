<?php
    require_once "app/controllers/AbstractController.php";
    require_once "app/models/Users.php";
    require_once "app/services/DocumentService.php";
    require_once "app/services/RecaptchaService.php";
    require_once "config/config.php";
    require_once 'deps/GoogleAuthenticator-master/PHPGangsta/GoogleAuthenticator.php';
    use const Config\config_email;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    enum UserRegistrationStatus: string{
        case inexistent = "inexistent";
        case unverified = "unverified";
        case unspecialized = "unspecialized";
        case registered = "registered";
    };

    enum SessionCreatedFrom: string{
        case register = "register";
        case login = "login";
    };
    
    class AuthController implements AbstractController{
        private static PHPGangsta_GoogleAuthenticator $ga;

        const VERIF_CODE_DURATION = 60 * 2; //2 minutes
        const SESSION_ABSOLUTE_LIFE = 60 * 60 * 1; //1 hour
        const MAX_SENT_VERIF_CODES = 3;
        const MAX_TRIED_VERIF_CODES = 3;
        
        public static function index(){
            //Redirecting user to the index page if they are logged in
            self::checkLoggedAuth();

            //Accepting only GET requests
            if ($_SERVER["REQUEST_METHOD"] != "GET"){
                $res["error"] = "Invalid request method";
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }
            //Rendering the view
            require_once "app/views/auth/index.php"; 
        }

        public static function add(){
            //Redirecting user to the index page if they are logged in
            self::checkLoggedAuth();

            if ($_SERVER["REQUEST_METHOD"] == "GET") {
                 //Loading neccesary data
                 require_once "app/models/Genders.php";
                 require_once "app/models/Roles.php";

                 $genders = Genders :: getAll();
                 $roles = Roles :: getAll();

                require_once "app/views/auth/register.php";
            }
            else if ($_SERVER["REQUEST_METHOD"] == "POST"){
                $res = ["ok" => true];
                //Get neccesay parameters for the registration form
                $neccesary_params = get_class_vars('UsersData');
                $neccesary_params["recaptcha_input"] = null;

                //Getting the unset parameters
                $unset_parameters = array_filter($neccesary_params, function($def_val, $col){
                    $v1 = ($def_val === null);
                    $v2 = !isset($_POST[$col]) || $_POST[$col] == "";
                return $v1 && $v2;
                }, ARRAY_FILTER_USE_BOTH);

                //If there are unset parameters, return an error with the unset parameters
                if (count($unset_parameters) != 0){
                    $res["error"] = "Unset parameters: " . implode(", ", array_keys($unset_parameters));
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }

                //Checking for bots
                $grec_err = RecaptchaService::validateRecaptchaResp($_POST["recaptcha_input"], "register");
                if ($grec_err){
                    $res["error"] = $grec_err;
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }

                //Checking if the email is valid
                $err_msg = self::validateEmail($_POST["email"]);
                if ($err_msg){
                    $res["error"] = $err_msg;
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }

                //Getting the user status
                $user = Users :: getByEmail($_POST["email"]);
                $user_status = self::getUserRegStatus($user);

                //If user exists already, we return an error and send redirect them to the appropriate page
                //We also start the user's session as logged off because the session data is needed for verification/specialization
                if ($user_status != UserRegistrationStatus :: inexistent){
                    self::createSession($user, SessionCreatedFrom :: register);
                    $res["error"] = self::getErrorMsg($user_status);
                    $res["ok"] = false;
                    $res["redirect"] = self::getRedirectPath($user_status);
                    echo json_encode($res);
                    return;
                }

                //Generating secret and verification code
                $secret = self::$ga->createSecret();
                $code = self::$ga->getCode($secret, time() / self::VERIF_CODE_DURATION);
                $hashed_password = password_hash($_POST["password"], PASSWORD_DEFAULT);

                //Populate the user object
                $user = new UsersData(null, $_POST["birth_date"], $_POST["gender_id"], $_POST["first_name"],
                                      $_POST["last_name"], $_POST["phone_number"], $_POST["user_name"],
                                      $hashed_password, $_POST["email"], $_POST["role_id"],
                                      $secret, $code);
                //Insert user in database in unverified state
                try{
                    Users :: insert($user);
                } catch(Exception $e){
                    $res["error"] = $e->getMessage();
                    $res["ok"] = false;
                    echo json_encode($res);
                }

                //TODO: Find better way to retrieve user id after inserting them
                //Getting the user from the database(we need the user_id)
                $user = Users :: getByEmail($user->email);

                //Setting up the session
                self::createSession($user, SessionCreatedFrom :: register);

                self::_sendVerifCode($user->email, $user->secret);
                
                //Set verification route for redirect
                $res["redirect"] = "/hosplive/auth/verify_user";
                echo json_encode($res);
            }
        }

        public static function login(){
            //Redirecting user to the index page if they are logged in
            self::checkLoggedAuth();
    
            if ($_SERVER["REQUEST_METHOD"] == "GET") {
                require_once "app/views/auth/login.php";
            }
            else{
                $res = ["ok" => true];
                //Getting the unset parameters
                $unset_parameters = array_filter(["email", "password", "recaptcha_input"], function($param){
                    return !isset($_POST[$param]) || $_POST[$param] == "";
                });
    
                //If there are unset parameters, return an error with the unset parameters
                if (count($unset_parameters) != 0){
                    $res["error"] = "Unset parameters: " . implode(", ", $unset_parameters);
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }
                //Checking for bots
                $grec_err = RecaptchaService::validateRecaptchaResp($_POST["recaptcha_input"], "login");
                if ($grec_err){
                    $res["error"] = $grec_err;
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }

                //Getting the user status
                $user = Users :: getByEmail($_POST["email"]);
                $user_status = self::getUserRegStatus($user);

                //Checking if the user exists separately because I don't want to redirect them to the register page
                if ($user_status == UserRegistrationStatus :: inexistent){
                    $res["error"] = "Wrong email";
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }

                self::createSession($user, SessionCreatedFrom :: login);
                //If the user exists but is not registered we return an error and redirect them to the appropriate page
                if ($user_status != UserRegistrationStatus :: registered){
                    $res["error"] = self::getErrorMsg($user_status);
                    $res["ok"] = false;
                    $res["redirect"] = self::getRedirectPath($user_status);
                    echo json_encode($res);
                    return;
                }
    
                //Checking if inputed password and the password from database match
                if (!password_verify($_POST["password"], $user->password)){
                    $res["error"] = "Incorrect password";
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }
    
                self::_sendVerifCode($user->email, $user->secret, $user->user_id);
    
                //Redirecting to verify page
                $res["redirect"] = "/hosplive/auth/verify_user";
                echo json_encode($res);
            }
        }

        public static function verifyUser(){
            //Redirecting user to the index page if they are logged in
            self::checkLoggedAuth();

            //This does not occur normally as the user is redirected to this page from the register or login page
            //TODO: Make functon to check the session parameters, this also needs to check 
            //for number of tries and number of sends
            if (!isset($_SESSION["from"])){
                header("Location: /hosplive/auth/index");
                exit();
            }

            $remaining_tries = self::MAX_TRIED_VERIF_CODES - $_SESSION["verif_code_tries"];
            $remaining_resends = self::MAX_SENT_VERIF_CODES - $_SESSION["verif_code_sends"];

            if ($_SERVER["REQUEST_METHOD"] == "GET"){
                require_once "app/views/auth/verify_user.php";
            }
            else if ($_SERVER["REQUEST_METHOD"] == "POST"){
                switch($_SESSION["from"]){
                    case SessionCreatedFrom :: register:
                        self::verifyUserReg($remaining_tries);
                        break;
                    case SessionCreatedFrom :: login:
                        self::verifyUserLogin($remaining_tries);
                        break;
                    default:
                        echo "How did you get here?";
                        return;
                }
            }
    }

    public static function specializeUser(){
        //Redirecting user to the index page if they are logged in
        self::checkLoggedAuth();

        //This does not occur normally as the user is redirected to this page from the verify page
        if (!isset($_SESSION["user_role"]) || $_SESSION["user_id"] == ""){
            header("Location: /hosplive/auth/add_user");
            exit();
        }

        //Render view based on role
        require_once "app/models/Roles.php";
        if ($_SERVER["REQUEST_METHOD"] == "GET"){
            $view_info = Roles :: getChosenView($_SESSION["user_role"]);
            $data = $view_info["data"];
            require_once $view_info["route"];
        }
        else if ($_SERVER["REQUEST_METHOD"] == "POST"){
            require_once "app/models/Hospitals.php";
            require_once "app/models/Patients.php";
            require_once "app/models/Medics.php";

            $res = ["ok" => true];

            //Getting the chosen model
            $chosen_model = Roles :: getChosenModel($_SESSION["user_role"]);
            
            //Getting the neccesary rows for the chosen model
            $neccesary_rows = $chosen_model :: getNeccesaryRows();
            //Adding the recaptcha_input to the neccesary parameters
            $neccesary_params = array_merge($neccesary_rows, array("recaptcha_input"));

            //Getting the unset parameters
            $unset_parameters = array_filter($neccesary_params, function($param){
                return !isset($_POST[$param]) || $_POST[$param] == "";
            });

            //If there are unset parameters, return an error with the unset parameters
            if (count($unset_parameters) != 0){
                $res["error"] = "Unset parameters: " . implode(", ", $unset_parameters);
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            //Checking for bots
            $grec_err = RecaptchaService::validateRecaptchaResp($_POST["recaptcha_input"], "specialize");
            if ($grec_err){
                $res["error"] = $grec_err;
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }
            //Creating the spcialized user object and populating it
            $spec_user = new ($chosen_model . 'Data')();
            
            foreach($neccesary_rows as $row)
                $spec_user->$row = $_POST[$row];

            //Inserting the specialized user in the database
            try{
                $chosen_model :: insert($spec_user);
            } catch(Exception $e){
                $res["error"] = $e->getMessage();
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            //TODO: Move this in a medics handler or something
            //Medics also need to upload their CV
            if ($_SESSION["user_role"] == "medic"){
                $err = DocumentService :: storeMedicCV("medic_cv", $_SESSION["user_id"]);
                if ($err){
                    $res["error"] = $err;
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }
            }

            //Setting the session to logged
            $_SESSION["logged"] = true;

            //Redirecting to index
            $res["redirect"] = "/hosplive/index";
            echo json_encode($res);
        }

    }

    private static function verifyUserLogin($remaining_tries){
        $res = ["ok" => true, "remaining_tries" => $remaining_tries];
        //Getting the unset parameters
        $unset_parameters = array_filter(["verif_code"], function($param){
            return !isset($_POST[$param]) || $_POST[$param] == "";
        });

        //If there are unset parameters, return an error with the unset parameters
        if (count($unset_parameters) != 0){
            $res["error"] = "Unset parameters: " . implode(", ", $unset_parameters);
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }

        $user = Users :: getById($_SESSION["user_id"]);
        
        //If verification code is invalid, determine error and return it
        $err = self::validateVerificationCode($user, $remaining_tries);
        if ($err){
            $res["error"] = $err;
            $res["ok"] = false;
            $res["remaining_tries"]--;
            echo json_encode($res);
            return;
        }

        //Setting the session to logged
        $_SESSION["logged"] = true;

        //Redirecting to index
        $res["redirect"] = "/hosplive/index";
        echo json_encode($res);
    }

    private static function verifyUserReg($remaining_tries){
        $res = ["ok" => true, "remaining_tries" => $remaining_tries];
        //Getting the unset parameters
        $unset_parameters = array_filter(["verif_code"], function($param){
            return !isset($_POST[$param]) || $_POST[$param] == "";
        });

        //If there are unset parameters, return an error with the unset parameters
        if (count($unset_parameters) != 0){
            $res["error"] = "Unset parameters: " . implode(", ", $unset_parameters);
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }
        
        $user = Users :: getById($_SESSION["user_id"]);

        //Checking if the user is already verified to avoid double verification
        if ($user->verified){
            $res["redirect"] = "/hosplive/auth/specialize_user";
            echo json_encode($res);
            return;
        }
    
        
        //If verification code is invalid, determine error and return it
        $err = self::validateVerificationCode($user, $remaining_tries);
        if ($err){
            $res["error"] = $err;
            $res["ok"] = false;
            $res["remaining_tries"]--;
            echo json_encode($res);
            return;
        }

        //Trying to verify the user
        try{
            Users :: verifyUser($user->user_id);
        } catch(Exception $e){
            $res["error"] = $e->getMessage();
            $res["ok"] = false;
            echo json_encode($res);
        }

        //Redirecting to specialized user page
        $res["redirect"] = "/hosplive/auth/specialize_user";
        echo json_encode($res);
    }
    

    //Destroys session and redirects user to login page
    public static function logout(){
        session_start();
        
        session_destroy();
        session_unset();

        header("Location: /hosplive/auth/login");
        exit();
    }
    
    //Validates email and returns error message if invalid, null otherwise
    private static function validateEmail(string $email):string|null{
        $valid_format = filter_var($email, FILTER_VALIDATE_EMAIL);
        //If the email is not in a valid format, return false
        if (!$valid_format){
            return "Invalid email format";
        }
        //Check if domain has MX and A(ipv4) records
        $domain_name = substr($email, strpos($email, '@') + 1);
        $valid_domain = checkdnsrr($domain_name, 'MX') || checkdnsrr($domain_name, 'A') || checkdnsrr($domain_name, 'AAAA');
        if (!$valid_domain)
            return "Domain does not have MX, ipv4 or ipv6 records";
        return null;
    }

    //Validates verification code and returns error message if invalid, null otherwise
    private static function validateVerificationCode(UsersData $user, int $remaining_tries): string|null{
        if ($remaining_tries <= 0) 
            return "You have reached the maximum number of tries";
        //Verification code is valid so we unset the verification related session data and return null
        if (self::$ga->verifyCode($user->secret, $_POST["verif_code"], 1, time() / self::VERIF_CODE_DURATION)){
            self::unsetSessionVerifData();
            return null;
        }
        //Incrementing the number of tries
        $_SESSION["verif_code_tries"]++;

        //Determining which error to return
        if (self::isCodeExpired($user))
            return "Verification code expired";
        return "Invalid code";
        
    }

    //Resends the verification code to the user
    //TODO: Allow user to resent more verification codes when registering but give them a timeout
    //Or remove the registered data from database and ask them to register again
    public static function resendVerifCode(){
        //Making sure the user is not looged in
        self::checkLoggedAuth();
        $res = ["ok" => true];

        //Accepting only POST requests
        if ($_SERVER["REQUEST_METHOD"] != "POST"){
            $res["error"] = "Invalid request method";
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }

        //This would not happen normally as the user gets here from a page which sets the session(or has it already set)
        if (!isset($_SESSION["user_id"]) || !isset($_SESSION["mail"]) || !isset($_SESSION["verif_code_sends"])){
            header("Location: /hosplive/auth/login");
            exit();
        }

        $res["remaining_resends"] = self::MAX_SENT_VERIF_CODES - $_SESSION["verif_code_sends"];
        //Checking if the user has reached the maximum number of verification codes sent
        //If so, redirect them to logout
        if ($res["remaining_resends"] <= 0){
            $res["error"] = "You have reached the maximum number of verification codes sent, login again";
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }

        //Getting the user from the database
        $user = Users :: getById($_SESSION["user_id"]);

        //Checking if the verification code is actually expired
        if (!self::isCodeExpired($user)){
            $res["error"] = "Verification code is not expired";
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }

        self::_sendVerifCode($user->email, $user->secret, $user->user_id);
        $res["remaining_resends"]--;
        echo json_encode($res);
    }

    //Sends verification email to the user and updates(if user_id is not null) the verification code in the database
    private static function _sendVerifCode(string $user_email, string $user_secret, int|null $user_id = null){
        //Generate verification code
        $verif_code = self::$ga->getCode($user_secret, time() / self::VERIF_CODE_DURATION);

        //Update verification code if desired
        if ($user_id)
            Users :: updateVerificationCode($user_id, $verif_code);

        self::sendVerificationEmail($user_email, $verif_code);

        $_SESSION["verif_code_sends"]++;
    }

    //Sends verification email to the user and updates the verification code in the database
    private static function sendVerificationEmail(string $email, string $verif_code){
        require_once 'deps/PHPMailer-master/src/PHPMailer.php';
        require_once 'deps/PHPMailer-master/src/Exception.php';
        require_once 'deps/PHPMailer-master/src/SMTP.php';
        require_once 'app/models/Users.php';
        
        $mail = new PHPMailer(true);
        //More verbose debug messages
        //$mail->SMTPDebug = 2;
        // Server settings
        $mail->isSMTP();                                      
        $mail->Host       = config_email["smtp"];              
        $mail->SMTPAuth   = true;                                 
        $mail->Username   = config_email["username"];               
        $mail->Password   = config_email["password"];               
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       
        $mail->Port       = config_email["port"];                              
        
        //Setting the data about the transmitter and the receptor
        $mail->setFrom(config_email["sender"], config_email["name"]);      
        $mail->addAddress($email); 
    
        // Content
        $mail->isHTML(true);                                     
        $mail->Subject = 'Authentication verification code';
        $mail->Body    = 'This is the <h1>verification code: ' . $verif_code . '</h1> for your registration' . 
                        '<h1>Expires in: ' . self::VERIF_CODE_DURATION / 60 . ' minutes </h1>';
        $mail->AltBody = 'This is the verification code: ' . $verif_code . ' for your registration';
        
        $mail->send();
    }

    private static function getUserRegStatus(UsersData|false $user): UserRegistrationStatus{
        if (!$user) 
            return UserRegistrationStatus :: inexistent;
        if (!$user->verified) 
            return UserRegistrationStatus :: unverified;
        if (!self::isUserSpecialized($user)) 
            return UserRegistrationStatus :: unspecialized;
        return UserRegistrationStatus :: registered;
    }

    private static function isUserSpecialized(UsersData $user): bool{
        require_once "app/models/Roles.php";
        //Looking if the user has an entry in one of the specialized tables
        $user_role = (Roles :: getById($user->role_id))->role_name;
        $chosen_model = Roles :: getChosenModel($user_role);
        $spec_user = $chosen_model :: getById($user->user_id);

        return $spec_user != false;
    }

    private static function isCodeExpired(UsersData $user): bool{
        $curr_time = time();
        $creation_time = strtotime($user->active_code_date);
        return $curr_time - $creation_time > self::VERIF_CODE_DURATION;
    }

    private static function isSessionExpired(): bool{
        return time() > $_SESSION["abs_exp_time"];
    }

    //Checks whether the session has expired and if so redirects user to logout
    public static function checkExpiredSession(){
        if (self::isSessionExpired()){
            header("Location: /hosplive/auth/logout");
            exit();
        }
    }

    //Checks if the user is logged in and redirects them to the login page if not
    public static function checkLogged(){
        //Starting session if not already started
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        $logged = isset($_SESSION["logged"]) && $_SESSION["logged"];
        $redirect = "/hosplive/auth/login";

        if (!$logged){
            header("Location: $redirect");
            exit();
        }
        self::checkExpiredSession();
    }

    //Checks if user is logged in and redirects them to the index page if they are
    //Private because it should only be used on auth routes(except logout)
    private static function checkLoggedAuth(){
        session_start();
        $logged = isset($_SESSION["logged"]) && $_SESSION["logged"];
        $redirect = "/hosplive/index";

        if ($logged){
            header("Location: $redirect");
            exit();
        }
    }

    private static function createSession(UsersData $user, SessionCreatedFrom $from){
        require_once "app/models/Roles.php";
        //Setting up the session
        session_regenerate_id(true);
        $_SESSION["user_id"] = $user->user_id;
        $_SESSION["user_role"] = Roles :: getById($user->role_id)->role_name;
        $_SESSION["mail"] = $user->email; //Needed for sending verification code
        $_SESSION["abs_exp_time"] = time() + self::SESSION_ABSOLUTE_LIFE; 
        $_SESSION["verif_code_sends"] = 0;
        $_SESSION["verif_code_tries"] = 0;
        $_SESSION["from"] = $from; //Used for verifyUser as diff functions are called based on this
        $_SESSION["logged"] = false;
    }

    public static function setAuth(){
        if (!isset(self::$ga))
            self::$ga = new PHPGangsta_GoogleAuthenticator();
    }

    //Unsets the data that was needed for user verification
    private static function unsetSessionVerifData(){
        unset($_SESSION["from"]);
        unset($_SESSION["verif_code_tries"]);
        unset($_SESSION["verif_code_sends"]);
    }

    //Returns the path to which the user should be redirected based on their registration status
    private static function getRedirectPath(UserRegistrationStatus $status): string{
        switch($status){
            case UserRegistrationStatus :: inexistent:
                return "/hosplive/auth/add_user";
            case UserRegistrationStatus :: unverified:
                return "/hosplive/auth/verify_user";
            case UserRegistrationStatus :: unspecialized:
                return "/hosplive/auth/specialize_user";
            case UserRegistrationStatus :: registered:
                return "/hosplive/auth/login";
        }
    }
    //Returns the error message based on the user registration status
    private static function getErrorMsg(UserRegistrationStatus $status): string{
        switch($status){
            case UserRegistrationStatus :: inexistent:
                return "User does not exist in database";
            case UserRegistrationStatus :: unverified:
                return "User exists but is not verified";
            case UserRegistrationStatus :: unspecialized:
                return "User is verified but not specialized";
            case UserRegistrationStatus :: registered:
                return "User is already registered";
        }
    }
    //TO DO
    public static function remove(){}

    //TO DO
    public static function get(){}

    //TO DO
    public static function edit(){}

    //TO DO
    public static function getConstants(){}

    }
?>