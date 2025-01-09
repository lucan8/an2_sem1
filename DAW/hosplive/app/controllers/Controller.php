<?php
require_once "app/controllers/AuthController.php";
class Controller {
    public static function index() {
        AuthController::checkLogged();
        require_once "app/views/layout.php";
        require_once "app/views/index.php";
    }

    //Gets the hospital associated with the passed county,
    //Then gets the medics from that hospital with the passed specialization
    public static function getMedics() {
        AuthController::checkLogged();

        //Only accepting get requests
        if ($_SERVER["REQUEST_METHOD"] != "GET"){
            http_response_code(405);
            return;
        }

        $res = ["ok" => true];
         //Getting the unset parameters
         $unset_parameters = array_filter(["county_id", "spec_id"], function($param){
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
        //Getting the hospital associated with the county
        try{
            require_once "app/models/Hospitals.php";
            $hospital = Hospitals::getByCounty((int)$_GET["county_id"]);
            if ($hospital === false)
                throw new Exception("No hospital found for the given county(" . $_GET["county_id"] . ")");
            else
                $res["data"]["chosen_hospital"] = $hospital->hospital_id;
        } catch (Throwable $e){
            $res["error"] = $e->getMessage();
            $res["ok"] = false;
            echo json_encode($res);
            return;
        }
        
        //Getting the medics from the hospital with the specialization
        try{
            require_once "app/models/Medics.php";
            $res["data"]["medics"] = Medics::getByHospAndSpec($res["data"]["chosen_hospital"], (int)$_GET["spec_id"]);
        } catch (Throwable $e){
            $res["error"] = $e->getMessage();
            $res["ok"] = false;
        }
        echo json_encode($res);
    }

    public static function getStatuses(){
        AuthController::checkLogged();

        //Only accepting get requests
        if ($_SERVER["REQUEST_METHOD"] != "GET"){
            http_response_code(405);
            return;
        }

        $res = ["ok" => true];
        //Getting the statuses
        try{
            require_once "app/models/Application_Statuses.php";
            $res["data"]["statuses"] = Application_Statuses::getAll();
        } catch (Throwable $e){
            $res["error"] = $e->getMessage();
            $res["ok"] = false;
        }
        echo json_encode($res);
    }
}
?>