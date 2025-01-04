<?php
    require_once "config/config.php";
    require_once "utils/utils.php";
    require_once "AbstractController.php";
    require_once "AuthController.php";
    require_once "app/services/SecurityService.php";

class AppointmentsController implements AbstractController {
    public static function index() {
        AuthController::checkLogged();

        //For now only patients can view their appointments
        //In the feature both medics and hospitals will have this feature
        if ($_SESSION["user_role"] != "pacient"){
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
        self::add();
        
        // Render appointments
        require_once "app/models/Appointments.php";
        require_once "app/models/Roles.php";

        //Getting the user's appointments
        $appointments = Appointments::getAppointmentsByPatient($_SESSION["user_id"]);

        //Setting the correct format for the time
        foreach ($appointments as &$app){
            $app['time'] = getHoursAndMinutes($app['time']);
        }

        require_once "app/views/appointments/appointments.php";
    }

    public static function add() {
        AuthController::checkLogged();

        //Only patients can make appointments
        if ($_SESSION["user_role"] != "pacient"){
            http_response_code(403);
            return;
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "GET"){
            require_once "app/models/Counties.php";
            $counties = Counties::getAll();
            
            require_once "app/models/Specializations.php";
            $specializations = Specializations::getAll();

            // Used for constants
            require_once "app/models/Hospitals.php";
            require_once "app/models/Appointments.php";

            //Generate CSRF token for the form
            $csrf_token = SecurityService::generateCSRFToken();
            //Render the view
            require_once "app/views/appointments/make_appointment.php";
        }
        //Get all information from form and insert it into the database
        else if ($_SERVER["REQUEST_METHOD"] == "POST"){
            require_once "app/models/Appointments.php";
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

            //Setting the appointment data to be inserted
            $app = new AppointmentsData(null, $_SESSION["user_id"], (int)$_POST["hospital_id"], (int)$_POST["medic_id"],
                                        (int)$_POST["room_id"], $_POST["appointment_date"], $_POST["appointment_time"],
                                        (int)$_POST["duration"]);
            try{
                $res['ok'] = Appointments::insert($app);
            } catch (Exception $e){
                $res['error'] = $e->getMessage();
                $res['ok'] = false;
            }

            //Generating a new CSRF token
            $res["csrf_token"] = SecurityService::generateCSRFToken();
            echo json_encode($res);
        }
    }

    public static function remove(){
        AuthController::checkLogged();

       
        //Only accepting post requests
        if ($_SERVER["REQUEST_METHOD"] != "POST"){
            http_response_code(405);
            return;
        }

        $res = ["ok" => true];
        //Getting the unset parameters
        $unset_parameters = array_filter(["appointment_id"], function($param){
            return !isset($_POST[$param]) || $_POST[$param] == "";
        });

        //If there are unset parameters, return an error with the unset parameters
        if (count($unset_parameters) != 0){
            $res["error"] = "Unset parameters: " . implode(", ", $unset_parameters);
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }

        require_once "app/models/Appointments.php";
        try{
            $res["ok"] = Appointments::removeById((int)$_POST["appointment_id"]);
        } catch (Exception $e){
            $res["ok"] = false;
            $res["error"] = $e->getMessage();
        }
        echo json_encode($res);
    }

    public static function edit(){
        AuthController::checkLogged();

        //Only accepting post requests
        if ($_SERVER["REQUEST_METHOD"] != "POST"){
            http_response_code(405);
            return;
        }

        $res = ["ok" => true];
        //Getting the unset parameters
        $unset_parameters = array_filter(["appointment_id", "appointment_date", "appointment_time"], function($param){
            return !isset($_POST[$param]) || $_POST[$param] == "";
        });

        //If there are unset parameters, return an error with the unset parameters
        if (count($unset_parameters) != 0){
            $res["error"] = "Unset parameters: " . implode(", ", $unset_parameters);
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }

        require_once "app/models/Appointments.php";
        try{
            $res["ok"] = Appointments::updateDateTime((int)$_POST["appointment_id"], $_POST["appointment_date"], $_POST["appointment_time"]);
        } catch (Exception $e){
            $res["ok"] = false;
            $res["error"] = $e->getMessage();
        }
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
            require_once "app/models/Appointments.php";
            $appointments = Appointments::getByHospMedDate((int)$_GET["hospital_id"],
                                                           (int)$_GET["medic_id"],
                                                           $_GET["appointment_date"]);
            $res["data"]["times"] = array_map(function(AppointmentsData $app){return getHoursAndMinutes($app->appointment_time);},
                                              $appointments);
        } catch (Exception $e){
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
            require_once "app/models/Appointments.php";
            $res["data"]["room"] = Appointments::getFreeRoom((int)$_GET["hospital_id"], $_GET["appointment_date"], $_GET["appointment_time"]);
        } catch (Exception $e){
            $res["error"] = $e->getMessage();
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }
        echo json_encode($res);
    }

    // public static function addInfo(){
    //     AuthController::checkLogged();
    //     //Checking if the user is authorized to add information
    //     if ($_SESSION["user_role"] != "medic"){
    //         http_response_code(403);
    //         return;
    //     }
    //     //If the request method is get, render the layout and the add_appointment_information view
    //     if ($_SERVER["REQUEST_METHOD" == "GET"]){
    //         require_once "app/views/layout.php";
    //         $apointments = 
    //         require_once "app/views/add_appointment_info.php";
    //     }
    //     else if ($_SERVER["REQUEST_METHOD"] == "POST"){
    //         $res = ["ok" => true];
    //         //Getting the unset parameters
    //         $unset_parameters = array_filter(["appointment_id", "info"], function($param){
    //             return !isset($_POST[$param]) || $_POST[$param] == "";
    //         });

    //         //If there are unset parameters, return an error with the unset parameters
    //         if (count($unset_parameters) != 0){
    //             $res["error"] = "Unset parameters: " . implode(", ", $unset_parameters);
    //             $res["ok"] = false;
    //             echo json_encode($res);
    //             return;
    //         }

    //         require_once "app/models/Appointments.php";
    //         try{
    //             $res["ok"] = Appointments::addInfo((int)$_POST["appointment_id"], $_POST["info"]);
    //         } catch (Exception $e){
    //             $res["ok"] = false;
    //             $res["error"] = $e->getMessage();
    //         }
    //         echo json_encode($res);
    //     }
    // }


    public static function get() {}
    public static function getConstants(){
        AuthController::checkLogged();
        
        //Only accepting get requests
        if ($_SERVER["REQUEST_METHOD"] != "GET"){
            http_response_code(405);
            return;
        }

        //Merging the constants from appointments and hospitals
        require_once "app/models/Hospitals.php";
        require_once "app/models/Appointments.php";
        $res["constants"] = Appointments::getConstants() + Hospitals::getConstants();
        echo json_encode($res["constants"]);
    }
}
?>