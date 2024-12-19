<?php
    require_once "app/controllers/AbstractController.php";
    require_once "app/models/Users.php";
    require_once 'deps/GoogleAuthenticator-master/PHPGangsta/GoogleAuthenticator.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    class AuthController implements AbstractController{
        public static function index(){
            //Redirecting user to the index page if they are logged in
            self::checkLoggedAuth();

            if ($_SERVER["REQUEST_METHOD"] == "GET") {
                //Loading neccesary data
                require_once "app/models/Genders.php";
                require_once "app/models/Roles.php";
                $genders = Genders :: getAll();
                $roles = Roles :: getAll();

                //Rendering the view
                require_once "app/views/register.php";
            }
        }

        public static function add(){
            //Redirecting user to the index page if they are logged in
            self::checkLoggedAuth();
            $res = ["ok" => true];

            //Accepting only POST requests
            if ($_SERVER["REQUEST_METHOD"] != "POST"){
                $res["error"] = "Invalid request method";
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }
            //Getting the unset parameters
            $unset_parameters = array_filter(get_class_vars('UsersData'), function($def_val, $col){
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

            //Checking if the email is valid
            $err_msg = self::validateEmail($_POST["email"]);
            if ($err_msg){
                $res["error"] = $err_msg;
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            //Generate secret and code
            $ga = new PHPGangsta_GoogleAuthenticator();
            $secret = $ga->createSecret();
            $code = $ga->getCode($secret);

            //Populate the user object
            $user = new UsersData();
            $user->set($_POST["birth_date"], $_POST["gender_id"], $_POST["first_name"],
                        $_POST["last_name"], $_POST["phone_number"], $_POST["user_name"],
                        password_hash($_POST["password"], PASSWORD_DEFAULT), $_POST["email"], $_POST["role_id"],
                        $secret, $code);

            //Send verification email
            self::sendVerificationEmail($user->email, $user->active_code);
            //Insert user in database in unverified state
            try{
                Users :: insert($user);
            } catch(Exception $e){
                $res["error"] = $e->getMessage();
                $res["ok"] = false;
                echo json_encode($res);
            }

            //Set verification route for redirect
            $res["redirect"] = "/hosplive/auth/verify_user?user_email=$user->email";
            echo json_encode($res);
        }

        public static function login(){
            //Redirecting user to the index page if they are logged in
            self::checkLoggedAuth();

            if ($_SERVER["REQUEST_METHOD"] == "GET") {
                require_once "app/views/login.php";
            }
            else{
                $res = ["ok" => true];
                //Getting the unset parameters
                $unset_parameters = array_filter(["email", "password"], function($param){
                    return !isset($_POST[$param]) || $_POST[$param] == "";
                });

                //If there are unset parameters, return an error with the unset parameters
                if (count($unset_parameters) != 0){
                    $res["error"] = "Unset parameters: " . implode(", ", $unset_parameters);
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }

                $user = Users :: getByEmail($_POST["email"]);
                //If user is not in database we send error
                if (!$user){
                    $res["error"] = "User with email " . $_POST["email"] . "does not exist";
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }

                //If user is not verified we send error
                if (!$user->verified){
                    $res["error"] = "User not verified";
                    $res["ok"] = false;
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

                //Starting the session and setting up relevant information
                //session_start();
                $_SESSION["user_id"] = $user->user_id;
                $_SESSION["role_id"] = $user->role_id;

                //Redirecting to index
                $res["redirect"] = "/hosplive/index";
                echo json_encode($res);
            }
        }

        //Destroys session and redirects user to login page
        public static function logout(){
            //Making sure user is actually logged in
            self::checkLogged();

            //Unsetting and destroying session
            session_unset();
            session_destroy();
            header("Location: /hosplive/auth/login");
        }

        public static function specializeUser(){
            $roles = Roles :: getAll();
        }
        public static function verifyUser(){
            //Redirecting user to the index page if they are logged in
            self::checkLoggedAuth();

            if ($_SERVER["REQUEST_METHOD"] == "GET"){
                //Checking if the user_id is set, if not should give a forbidden page
                if (!isset($_GET["user_email"]) || $_GET["user_email"] == ""){
                    echo "Unset parameters: email";
                }
                $user_email = $_GET["user_email"];
                require_once "app/views/verify_user.php";
            }
            else if ($_SERVER["REQUEST_METHOD"] == "POST"){
                $res = ["ok" => true];
                //Getting the unset parameters
                $unset_parameters = array_filter(["email", "verif_code"], function($param){
                    return !isset($_POST[$param]) || $_POST[$param] == "";
                });

                //If there are unset parameters, return an error with the unset parameters
                if (count($unset_parameters) != 0){
                    $res["error"] = "Unset parameters: " . implode(", ", $unset_parameters);
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }

                //Checking if the user exists
                $user = Users :: getByEmail($_POST["email"]);
                if (!$user){
                    $res["error"] = "User not found";
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }
                
                //If the user is already verified, return an error
                if ($user->verified){
                    $res["error"] = "User already verified";
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }
                
                //If verification code is invalid, return an error
                if ($user->active_code != $_POST["verif_code"]){
                    $res["error"] = "Invalid code";
                    $res["ok"] = false;
                    echo json_encode($res);
                }

                // //FOR THE MOMENT HARDCODED
                // $role = Roles :: getById($user->role_id)->role_name;
                // switch ($user->role_name):
                //     case "hospital":
                //         Hospitals :: getByUser

                //Trying to verify the user
                try{
                    Users :: verifyUser($user->user_id);
                } catch(Exception $e){
                    $res["error"] = $e->getMessage();
                    $res["ok"] = false;
                    echo json_encode($res);
                }
                //Setting up the session
                $_SESSION["user_id"] = $user->user_id;
                $_SESSION["role_id"] = $user->role_id;
                
                //Redirecting to index
                $res["redirect"] = "/hosplive/index";
                echo json_encode($res);
        }
    }
    
        
        static private function isCodeExpired(UsersData $user): bool{
            return false;
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

        private static function sendVerificationEmail(string $email, string $verif_code){
            require_once 'deps/PHPMailer-master/src/PHPMailer.php';
            require_once 'deps/PHPMailer-master/src/Exception.php';
            require_once 'deps/PHPMailer-master/src/SMTP.php';
            require  'config/config.php';
            
            $mail = new PHPMailer(true);
            //More verbose debug messages
            //$mail->SMTPDebug = 2;
            // Server settings
            $mail->isSMTP();                                      
            $mail->Host       = $config_email["smtp"];              
            $mail->SMTPAuth   = true;                                 
            $mail->Username   = $config_email["username"];               
            $mail->Password   = $config_email["password"];               
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       
            $mail->Port       = $config_email["port"];                              
            
            //Setting the data about the transmitter and the receptor
            $mail->setFrom($config_email["sender"], $config_email["name"]);      
            $mail->addAddress($email); 
        
            // Content
            $mail->isHTML(true);                                     
            $mail->Subject = 'Authentication verification code';
            $mail->Body    = 'This is the <h1>verification code: ' . $verif_code . '</h1> for your registration';
            $mail->AltBody = 'This is the verification code: ' . $verif_code . ' for your registration';
            
            $mail->send();
            

        }

        //Checks if the user is logged in and redirects them to the login page if not
        public static function checkLogged(){
            session_start();
            $logged = isset($_SESSION["user_id"]);
            $redirect = "/hosplive/auth/login";

            if (!$logged){
                header("Location: $redirect");
                exit();
            }
            
        }

        //Checks if user is logged in and redirects them to the index page if they are
        //Private because it should only be used on auth routes(except logout)
        private static function checkLoggedAuth(){
            session_start();
            $logged = isset($_SESSION["user_id"]);
            $redirect = "/hosplive/index";

            if ($logged){
                header("Location: $redirect");
                exit();
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