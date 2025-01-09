<?php
    require_once "config/config.php";
    require_once "utils/utils.php";
    require_once "AbstractController.php";
    require_once "AuthController.php";
    require_once "app/services/SecurityService.php";
    require_once "app/services/DocumentService.php";
    require_once "app/services/MailService.php";
    require_once "app/models/Appointments.php";


//The enum vlaues represent the action which should be taken for the appointment in that state
enum AppointmentState:string {
    case UPCOMING = "wait";
    case FINISHED = "write summary";
    case HAS_SUMMARY = "view summary";
}

class AppointmentsController implements AbstractController {
    public static function index() {
        AuthController::checkLogged();

         //For now only patients and medics can view their appointments
         if (!in_array($_SESSION["user_role"], ["medic", "patient"])){
            http_response_code(403);
            return;
        }

        //Accepting only get requests
        if ($_SERVER["REQUEST_METHOD"] != "GET"){
            http_response_code(405);
            return;
        }
        
        //Render the layout
        require_once "app/views/layout.php";

        // Render make_appointment
        if ($_SESSION["user_role"] == "patient")
            self::add();
        
        // Render appointments
        self::get();
    }

    public static function add() {
        AuthController::checkLogged();

        //Only patients can make appointments
        if ($_SESSION["user_role"] != "patient"){
            http_response_code(403);
            return;
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "GET"){
            require_once "app/views/layout.php";
            require_once "app/models/Counties.php";
            $counties = Counties::getAll();
            
            require_once "app/models/Specializations.php";
            $specializations = Specializations::getAll();

            // Used for constants
            require_once "app/models/Hospitals.php";
            

            //Generate CSRF token for the form
            $csrf_token = SecurityService::generateCSRFToken();
            //Render the view
            require_once "app/views/appointments/make_appointment.php";
        }
        //Get all information from form and insert it into the database
        else if ($_SERVER["REQUEST_METHOD"] == "POST"){
            
            require_once "app/models/Hospitals.php";
            $res = ["ok" => true];

            //Setting the necessary parameters for the appointment form
            $necessary_params = get_class_vars("AppointmentsData");
            $necessary_params["recaptcha_input"] = null;
            $neccessary_params["csrf_token"] = null;

            //Getting the unset parameters
            $unset_parameters = array_filter($necessary_params, function($def_val, $col){
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
            $grec_err = SecurityService::validateRecaptchaResp($_POST["recaptcha_input"], "make_appointment");
            if ($grec_err){
                $res["error"] = $grec_err;
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            //Checking the CSRF token
            if (!SecurityService::checkCSRFToken($_POST["csrf_token"])){
                $res["error"] = "Invalid CSRF token";
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            //Checking that the date and time are in the future
            $now = strtotime("now");
            $app_datetime = strtotime($_POST["appointment_date"] . " " . $_POST["appointment_time"]);
            if ($now >= $app_datetime){
                $res["error"] = "The appointment date and time must be in the future";
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            //Setting the appointment data to be inserted
            $app = new AppointmentsData(null, $_SESSION["user_id"], (int)$_POST["hospital_id"], (int)$_POST["medic_id"],
                                        (int)$_POST["room_id"], $_POST["appointment_date"], $_POST["appointment_time"],
                                        (int)$_POST["duration"]);
            try{
                $res['ok'] = Appointments::insert($app);
            } catch (Throwable $e){
                $res['error'] = $e->getMessage();
                $res['ok'] = false;
            }

            //Generating a new CSRF token
            $res["csrf_token"] = SecurityService::generateCSRFToken();
            echo json_encode($res);
        }
    }

    public static function get() {
        AuthController::checkLogged();
        //For now only patients and medics can view their appointments
        if (!in_array($_SESSION["user_role"], ["medic", "patient"])){
            http_response_code(403);
            return;
        }

        //Accepting only get requests
        if ($_SERVER["REQUEST_METHOD"] != "GET"){
            http_response_code(405);
            return;
        }
        require_once "app/views/layout.php";

        // Render appointments
        require_once "app/models/Roles.php";

        //Getting the user's appointments
        $appointments = self::getAppointments($_SESSION["user_id"]);

        //Setting the correct format for the time
        foreach ($appointments as &$app){
            $app['time'] = getHoursAndMinutes($app['time']);
            //$app['app_state'] = AppointmentState::FINISHED;
            $app['app_state'] = self::getAppointmentState($app['id'], $app['date'], $app['time']);
        }

        //Generating the csrf token
        $csrf_token = SecurityService::generateCSRFToken();

        //Render the appointments view
        require_once "app/views/appointments/appointments.php";
    }
    
    public static function remove(){
        AuthController::checkLogged();

        //For now only patients and medics can view their appointments
        if (!in_array($_SESSION["user_role"], ["medic", "patient"])){
            http_response_code(403);
            return;
        }

        //Only accepting post requests
        if ($_SERVER["REQUEST_METHOD"] != "POST"){
            http_response_code(405);
            return;
        }

        $res = ["ok" => true];
        //Getting the unset parameters
        $unset_parameters = array_filter(["appointment_id", "csrf_token"], function($param){
            return !isset($_POST[$param]) || $_POST[$param] == "";
        });

        //If there are unset parameters, return an error with the unset parameters
        if (count($unset_parameters) != 0){
            $res["error"] = "Unset parameters: " . implode(", ", $unset_parameters);
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }

        //Checking the CSRF token
        if (!SecurityService::checkCSRFToken($_POST["csrf_token"])){
            $res["error"] = "Invalid CSRF token";
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }

        $app = Appointments::getById((int)$_POST["appointment_id"]);
        //Making sure the appointment exists and the user is the owner
        $err = self::checkUserOwnsAppointment($app);
        if ($err){
            $res["error"] = $err;
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }


        //Removing the appointment
        try{
            $res["ok"] = Appointments::removeById((int)$_POST["appointment_id"]);
        } catch (Throwable $e){
            $res["ok"] = false;
            $res["error"] = $e->getMessage();
        }
        //Generating a new CSRF token
        $res["csrf_token"] = SecurityService::generateCSRFToken();
        echo json_encode($res);
    }

    public static function edit(){
        AuthController::checkLogged();

        //For now only patients and medics can view their appointments
        if (!in_array($_SESSION["user_role"], ["medic", "patient"])){
            http_response_code(403);
            return;
        }

        //Only accepting post requests
        if ($_SERVER["REQUEST_METHOD"] != "POST"){
            http_response_code(405);
            return;
        }

        $res = ["ok" => true];
        //Getting the unset parameters
        $unset_parameters = array_filter(["appointment_id", "appointment_date", "appointment_time", "csrf_token"], function($param){
            return !isset($_POST[$param]) || $_POST[$param] == "";
        });

        //If there are unset parameters, return an error with the unset parameters
        if (count($unset_parameters) != 0){
            $res["error"] = "Unset parameters: " . implode(", ", $unset_parameters);
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }
        
        //Checking the CSRF token
        if (!SecurityService::checkCSRFToken($_POST["csrf_token"])){
            $res["error"] = "Invalid CSRF token";
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }

        $app = Appointments::getById((int)$_POST["appointment_id"]);
        //Making sure the appointment exists and the user is the owner
        $err = self::checkUserOwnsAppointment($app);
        if ($err){
            $res["error"] = $err;
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }

        //Checking that the date and time are in the future
        $now = strtotime("now");
        $app_datetime = strtotime($_POST["appointment_date"] . " " . $_POST["appointment_time"]);
        if ($now >= $app_datetime){
            $res["error"] = "The appointment date and time must be in the future";
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }

        //Updating the appointment date and time 
        try{
            $res["ok"] = Appointments::updateDateTime((int)$_POST["appointment_id"], $_POST["appointment_date"], $_POST["appointment_time"]);
        } catch (Throwable $e){
            $res["ok"] = false;
            $res["error"] = $e->getMessage();
        }
        //Generating a new CSRF token
        $res["csrf_token"] = SecurityService::generateCSRFToken();
        echo json_encode($res);
    }


    //Gets the unavailable times for a given hospital, medic and date
    public static function getUnavailableTimes() {
        AuthController::checkLogged();

        //Only accepting get requests
        if ($_SERVER["REQUEST_METHOD"] != "GET"){
            http_response_code(405);
            return;
        }
        $res = ["ok" => true];

        //Getting the unset parameters
        $unset_parameters = array_filter(["hospital_id", "medic_id", "appointment_date"], function($param){
            return !isset($_GET[$param]) || $_GET[$param] == "";
        });

        //If there are unset parameters, return an error with the unset parameters
        if (count($unset_parameters) != 0){
            $res["error"] = "Unset parameters: " . implode(", ", $unset_parameters);
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }

        $res["data"] = [];
        //Getting the unavailable times
        try{
            
            $appointments = Appointments::getByHospMedDate((int)$_GET["hospital_id"],
                                                           (int)$_GET["medic_id"],
                                                           $_GET["appointment_date"]);
            $res["data"]["times"] = array_map(function(AppointmentsData $app){return getHoursAndMinutes($app->appointment_time);},
                                              $appointments);
        } catch (Throwable $e){
            $res["error"] = $e->getMessage();
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }

        echo json_encode($res);
    }

    //Gets the first free room for a given hospital, date and time
    public static function getFreeRoom() {
        AuthController::checkLogged();

        //Only accepting get requests
        if ($_SERVER["REQUEST_METHOD"] != "GET"){
            http_response_code(405);
            return;
        }
        $res = ["ok" => true];

        //Getting the unset parameters
        $unset_parameters = array_filter(["hospital_id", "appointment_date", "appointment_time"], function($param){
            return !isset($_GET[$param]) || $_GET[$param] == "";
        });
        //Checking if the parameters are set
        if (count($unset_parameters) != 0){
            $res["error"] = "Unset parameters: " . implode(", ", $unset_parameters);
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }
        //Getting the first free room
        try{
            
            $res["data"]["room"] = Appointments::getFreeRoom((int)$_GET["hospital_id"], $_GET["appointment_date"], $_GET["appointment_time"]);
        } catch (Throwable $e){
            $res["error"] = $e->getMessage();
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }
        echo json_encode($res);
    }

    public static function addSummary(){
        AuthController::checkLogged();
         if ($_SESSION["user_role"] != "medic"){
            http_response_code(403);
            return;
        }

        if ($_SERVER["REQUEST_METHOD"] == "GET"){
            //Getting the unset parameters
            $unset_parameters = array_filter(["appointment_id"], function($param){
                return !isset($_GET[$param]) || $_GET[$param] == "";
            });

            //Checking if the parameters are set
            if (count($unset_parameters) != 0){
                http_response_code(422);
                return;
            }

            $app = Appointments::getById((int)$_GET["appointment_id"]);

            //Checking if the appointment exists and the user is the owner
            $err = self::checkUserOwnsAppointment($app);
            if ($err){
                http_response_code(403);
                return;
            }

            //Checking the state of the appointment
            $app_state = self::getAppointmentState($app->appointment_id, $app->appointment_date,
                                                   $app->appointment_time);


            //If the appointment already has a summary redirect to the summary
            if ($app_state == AppointmentState::HAS_SUMMARY){
                header("Location: /hosplive/appointments/view_summary?appointment_id=" . $_GET["appointment_id"]);
                return;
            }

            //If the appointment is not finished redirect them to appointments
            if ($app_state == AppointmentState::UPCOMING){
                header("Location: /hosplive/appointments/appointments");
                return;
            }

            $csrf_token = SecurityService::generateCSRFToken();
            require_once "app/views/appointments/add_summary.php";
        }
        else if ($_SERVER["REQUEST_METHOD"] == "POST"){
            require_once "app/models/Appointments_Summary.php";
            
            $res = ["ok" => true];

            $neccessar_rows = Appointments_Summary::getNeccessaryRows();
            $neccessary_params = $neccessar_rows + ["csrf_token", "recaptcha_input"];
            //Getting the unset parameters
            $unset_parameters = array_filter($neccessary_params, function($param){
                return !isset($_POST[$param]) || $_POST[$param] == "";
            });

            //If there are unset parameters, return an error with the unset parameters
            if (count($unset_parameters) != 0){
                $res["error"] = "Unset parameters: " . implode(", ", array_values($unset_parameters));
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            $app = Appointments::getById((int)$_POST["appointment_id"]);

            //Checking if the appointment exists and the user is the owner
            $err = self::checkUserOwnsAppointment($app);;
            if ($err){
                $res["error"] = "Unauthorized";
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            //Checking the state of the appointment
            $app_state = self::getAppointmentState($app->appointment_id, $app->appointment_date,
                                                   $app->appointment_time);

            //If the appointment already has a summary, return error and redirect to the summary
            if ($app_state == AppointmentState::HAS_SUMMARY){
                $res["error"] = "Appointment already has summary";
                $res["redirect"] = "/hosplive/appointments/view_summary?appointment_id=" . $_POST["appointment_id"];
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            //If the appointment is not finished, return an error and redirect them to appointments
            if ($app_state == AppointmentState::UPCOMING){
                $res["error"] = "Appointment is not finished";
                $res["redirect"] = "/hosplive/appointments/appointments";
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            //Checking csrf token
            if (!SecurityService::checkCSRFToken($_POST["csrf_token"])){
                $res["error"] = "Invalid CSRF token";
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            //Checking for bots
            $grec_err = SecurityService::validateRecaptchaResp($_POST["recaptcha_input"], "add_summary");
            if ($grec_err){
                $res["error"] = $grec_err;
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            //Populating the appointment summary obj
            $appointment_summary = new Appointments_SummaryData();
            foreach ($neccessar_rows as $row)
                $appointment_summary->$row = htmlspecialchars($_POST[$row]);

            //Inserting the summary
            try{
                Appointments_Summary::insert($appointment_summary);
            } catch(Throwable $e){
                $res["error"] = $e->getMessage();
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            //Adding remaining neccessary data to the summary
            $appointment_summary =  (array)$appointment_summary;
            require_once "app/models/Hospitals.php";
            require_once "app/models/Medics.php";
            require_once "app/models/Patients.php";

            $appointment_summary["hospital_county"] = Hospitals::getCountyName($app->hospital_id);
            $appointment_summary = array_merge($appointment_summary, Medics::getById($app->medic_id));
            $appointment_summary["patient_name"] = Patients::getPatientName($app->patient_id);
            
            //Create and store pdf file
            $err = DocumentService::createAppointmentSummaryPDF($app->appointment_id,
                                                                (array)$appointment_summary);
            if ($err){
                $res["error"] = $err;
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            $patient_email = Users::getById($app->patient_id)->email;
            //Send the summary to the patient
            MailService::sendAppointmentSummaryEmail($patient_email, $app->appointment_id);

            //Redirecting to view the summary
            $res["redirect"] = "/hosplive/appointments/view_summary?appointment_id=" . $_POST["appointment_id"];
            echo json_encode($res);
        }
    }

    public static function viewSummary(){
        AuthController::checkLogged();

        //For now only patients and medics can view the appointment summary
        if (!in_array($_SESSION["user_role"], ["medic", "patient"])){
            http_response_code(403);
            return;
        }

        //Accepting only get requests
        if ($_SERVER["REQUEST_METHOD"] != "GET"){
            http_response_code(405);
            return;
        }

        //Getting the unset parameters
        $unset_parameters = array_filter(["appointment_id"], function($param){
            return !isset($_GET[$param]) || $_GET[$param] == "";
        });

        //Checking if the parameters are set
        if (count($unset_parameters) != 0){
            http_response_code(422);
            return;
        }

        //Checking the state of the appointment
        $app = Appointments::getById((int)$_GET["appointment_id"]);
        $err = self::checkUserOwnsAppointment($app);
        if ($err){
            http_response_code(403);
            return;
        }

        //Checking the state of the appointment
        $app_state = self::getAppointmentState($app->appointment_id, $app->appointment_date,
                                               $app->appointment_time);


        // //If the appointment has no summary but is finished, redirect them to add_summary
        if ($app_state == AppointmentState::FINISHED){
            header("Location: /hosplive/appointments/add_summary?appointment_id=" . $_GET["appointment_id"]);
            return;
        }

        //If the appointment is not finished redirect them to appointments
        if ($app_state == AppointmentState::UPCOMING){
            header("Location: /hosplive/appointments/appointments");
            return;
        }

        DocumentService::displayAppointmentSummaryPDF($app->appointment_id);
    }

    public static function getConstants(){
        AuthController::checkLogged();
        
        //Only accepting get requests
        if ($_SERVER["REQUEST_METHOD"] != "GET"){
            http_response_code(405);
            return;
        }

        //Merging the constants from appointments and hospitals
        require_once "app/models/Hospitals.php";
        
        $res["constants"] = Appointments::getConstants() + Hospitals::getConstants();
        echo json_encode($res["constants"]);
    }

    private static function getAppointmentState(int $app_id, string $app_date, string $app_time): AppointmentState|null{
        require_once "app/models/Appointments_Summary.php";

        $now = strtotime("now");
        $app_datetime = strtotime($app_date . " " . $app_time);
        //Checking if the has been completed
        if ($now >= $app_datetime){
            if (Appointments_Summary::getById($app_id))
                return AppointmentState::HAS_SUMMARY;
            return AppointmentState::FINISHED;
        }
        return AppointmentState::UPCOMING;
    }

    private static function checkUserOwnsAppointment(AppointmentsData|false $app): string|null{
        if (!$app)
            return "Invalid appointment";
        if ($_SESSION["user_role"] == "medic" && $app->medic_id != $_SESSION["user_id"] || 
            $_SESSION["user_role"] == "patient" && $app->patient_id != $_SESSION["user_id"])
            return "Unauthorized";
        return null;
    }

    //Based on the user's role, get their appointments using their id
    private static function getAppointments(int $user_id): array{
        switch ($_SESSION['user_role']){
            case "medic":
                return Appointments::getAppointmentsByMedic($user_id);
            case "patient":
                return Appointments::getAppointmentsByPatient($user_id);
            default:
                return [];
        }
    }
}
?>