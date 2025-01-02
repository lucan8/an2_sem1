<?php
    require_once "app/controllers/AuthController.php";
    class JobController{
        public static function index(){
            AuthController::checkLogged();
            require_once "app/views/layout.php";
            if ($_SESSION["user_role"] == "medic"){
                require_once "app/models/Job_Application.php";
                $applications = Job_Applications::getByApplicant($_SESSION["user_id"]);
                require_once "app/views/job/medic_applications.php";
            }
            else if ($_SESSION["user_role"] == "hospital"){
                require_once "app/models/Job_Application.php";
                $applications = Job_Applications::getByHirer($_SESSION["user_id"]);
                require_once "app/views/job/hospital_applications.php";
            }
            else
                http_response_code(403);
        }

        public static function apply(){
            AuthController::checkLogged();
            if ($_SESSION["user_role"] != "medic"){
                http_response_code(403);
                return;
            }

            if ($_SERVER["REQUEST_METHOD"] == "GET"){
                require_once "app/views/layout.php";
                require_once "app/models/Hospitals.php";
                $hospitals = Hospitals::getHospitalsAndCounties();
                require_once "app/views/job/apply.php";
            }
            else if ($_SERVER["REQUEST_METHOD"] == "POST"){
                //Getting the unset parameters
                $res = ["ok" => true];
                $unset_params = array_filter(["hosp_user_id"], function($param){
                    return !isset($_POST[$param]) || $_POST[$param] == ""; 
                });

                //If there are unset parameters, return an error with the unset parameters
                if (count($unset_params) != 0){
                    $res["error"] = "Unset parameters: " . implode($unset_params);
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }
                require_once "app/models/Job_Application.php";
                require_once "app/models/Application_Statuses.php";
                require_once "app/models/Hospitals.php";
                //Inserting the job application as pending
                try{
                    $app_status_id = Application_Statuses::getByName("Pending")->application_status_id;
                    Job_Applications::insert(new Job_ApplicationsData(null, $_SESSION["user_id"], $_POST["hosp_user_id"],
                                                                      null, $app_status_id));
                } catch (Exception $e){
                    $res["error"] = $e->getMessage();
                    $res["ok"] = false;
                }
                echo json_encode($res);
            }
        }

        public static function changeStatus(){
            AuthController::checkLogged();
            if ($_SESSION["user_role"] != "hospital"){
                http_response_code(403);
                return;
            }
            if ($_SERVER["REQUEST_METHOD"] != "POST"){
                http_response_code(405);
                return;
            }

            $res = ["ok" => true];
            
            $unset_params = array_filter(["applicant_user_id", "application_id", "new_status_id", "new_status_name"], function($param){
                return !isset($_POST[$param]) || $_POST[$param] == ""; 
            });

            //If there are unset parameters, return an error with the unset parameters
            if (count($unset_params) != 0){
                $res["error"] = "Unset parameters: " . implode($unset_params);
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            //If the new status is "Hired", store the hiring contract
            if ($_POST["new_status_name"] == "Hired"){
                $err = DocumentService::storeHiringContract("hiring_contract", $_SESSION["user_id"],
                                                             $_POST["applicant_user_id"]);
                if ($err){
                    $res["error"] = $err;
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }
            }
            
            //Update the status of the application
            require "app/models/Job_Application.php";
            try{
                Job_Applications::updateStatus($_POST["application_id"], $_POST["new_status_id"]);
            } catch (Exception $e){
                $res["error"] = $e->getMessage();
                $res["ok"] = false;
            }

            echo json_encode($res);
        }

        public static function getMedicCV(){
            AuthController::checkLogged();
            //Only medics and hospitals can view the CV
            if (in_array($_SESSION["user_role"], ["medic", "hospital"]) == false){
                http_response_code(403);
                return;
            }
            //Only POST requests are allowed
            if ($_SERVER["REQUEST_METHOD"] != "GET"){
                http_response_code(405);
                return;
            }

            $res = ["ok" => true];

            //Getting the unset parameters
            $unset_params = array_filter(["applicant_user_id"], function($param){
                return !isset($_GET[$param]) || $_GET[$param] == ""; 
            });

            //If there are unset parameters, return an error with the unset parameters
            if (count($unset_params) != 0){
                $res["error"] = "Unset parameters: " . implode($unset_params);
                $res["ok"] = false;
                echo json_encode($res);
                return;
            }

            //Try to display the medic's CV
            $err = DocumentService::displayMedicCV($_GET["applicant_user_id"]);
            if ($err)
                http_response_code(404);
        }

    }
?>