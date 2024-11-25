<?php
class AppointmentsController {
    public static function index() {
        if ($_SERVER["REQUEST_METHOD"] == "GET"){
            require_once "app/views/layout.php";
            self::makeAppointment();
            require_once "app/models/Appointments.php";
            //TODO: Get the user id from the session
            $appointments = Appointments::getAppointments(1);
            require_once "app/views/appointments.php";
        }
    }

    public static function makeAppointment() {
        if ($_SERVER["REQUEST_METHOD"] == "GET"){
            require_once "app/models/Counties.php";
            $counties = json_encode(Counties::getAll());
            
            require_once "app/models/Specializations.php";
            $specializations = json_encode(Specializations::getAll());

            // Used for constants
            require_once "app/models/Hospitals.php";
            require_once "app/models/Appointments.php";

            //Render the view
            require_once "app/views/make_appointment.php";
        }
        //Get all information from form and insert it into the database
        else if ($_SERVER["REQUEST_METHOD"] == "POST"){
            $res = ["ok" => true];
            //TODO: Check if the date is in the future
            //Getting the unset parameters
            $unset_parameters = array_filter(array_keys(get_class_vars(self::class)), function($param){
                return !isset($_POST[$param]) || $_POST[$param] == "";
            });

            //If there are unset parameters, return an error with the unset parameters
            if (count($unset_parameters) != 0){
                $res["error"] = "Invalid request parameters: " . implode(", ", $unset_parameters);
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            require_once "app/models/Appointments.php";
            require_once "app/models/Hospitals.php";

            $app = new AppointmentsData();
            $app->set((int)$_POST["user_id"], (int)$_POST["hospital_id"], (int)$_POST["medic_id"],
                      (int)$_POST["room_id"], $_POST["appointment_date"], $_POST["appointment_time"]);
            $res = [];
            try{
                $res['ok'] = Appointments::insert($app);
            } catch (Exception $e){
                $res['ok'] = false;
                $res['error'] = $e->getMessage();
            }
            echo json_encode($res);
        }
    }
    //Gets the hospital associated with the passed county,
    //Then gets the medics from that hospital with the passed specialization
    public static function getMedics() {
        $res = ["ok" => true];
        //Only accepting get requests
        if ($_SERVER["REQUEST_METHOD"] != "GET"){
            $res["error"] = "Invalid request method";
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }
         //Getting the unset parameters
         $unset_parameters = array_filter(["county_id", "spec_id"], function($param){
            return !isset($_GET[$param]) || $_GET[$param] == "";
        });

        //If there are unset parameters, return an error with the unset parameters
        if (count($unset_parameters) != 0){
            $res["error"] = "Invalid request parameters: " . implode(", ", $unset_parameters);
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }


        $res["data"] = [];
        //Getting the hospital associated with the county
        try{
            require_once "app/models/Hospitals.php";
            $res["data"]["chosen_hospital"] = Hospitals::getByCounty((int)$_GET["county_id"])->hospital_id;
        } catch (Exception $e){
            $res["error"] = $e->getMessage();
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }
        
        //Getting the medics from the hospital with the specialization
        try{
            require_once "app/models/Medics.php";
            $res["data"]["medics"] = Medics::getByHospAndSpec($res["data"]["chosen_hospital"], (int)$_GET["spec_id"]);
        } catch (Exception $e){
            $res["error"] = $e->getMessage();
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }
        echo json_encode($res);
    }

    //Gets the available times for a given hospital, medic and date
    public static function getFreeTimeIntervals() {
        $res = ["ok" => true];

        //Only accepting get requests
        if ($_SERVER["REQUEST_METHOD"] != "GET"){
            $res["error"] = "Invalid request method";
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }
        //Getting the unset parameters
        $unset_parameters = array_filter(["hospital_id", "medic_id", "appointment_date"], function($param){
            return !isset($_GET[$param]) || $_GET[$param] == "";
        });

        //If there are unset parameters, return an error with the unset parameters
        if (count($unset_parameters) != 0){
            $res["error"] = "Invalid request parameters: " . implode(", ", $unset_parameters);
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }

        $res["data"] = [];
        //Getting the available times
        try{
            require_once "app/models/Appointments.php";
            $res["data"]["times"] = Appointments::getFreeTimeIntervals((int)$_GET["hospital_id"], (int)$_GET["medic_id"], $_GET["appointment_date"]);
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
        $res = ["ok" => true];

        //Only accepting get requests
        if ($_SERVER["REQUEST_METHOD"] != "GET"){
            $res["error"] = "Invalid request method";
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }
        $unset_parameters = array_filter(["hospital_id", "appointment_date", "appointment_time"], function($param){
            return !isset($_GET[$param]) || $_GET[$param] == "";
        });
        //Checking if the parameters are set
        if (count($unset_parameters) != 0){
            $res["error"] = "Invalid request parameters: " . implode(", ", $unset_parameters);
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

    public static function getAppointments() {
        $res = ["ok" => true];
        if ($_SERVER["REQUEST_METHOD"] != "GET"){
            $res["error"] = "Invalid request method";
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }

        $res["data"] = [];
        try{
            require_once "app/models/Appointments.php";
            $res["data"]["appointments"] = Appointments::getAll();
        } catch (Exception $e){
            $res["error"] = $e->getMessage();
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }
        echo json_encode($res);
    }
}
?>