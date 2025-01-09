<?php
    require_once "config/config.php";
    require_once "app/controllers/AuthController.php";
    require_once "app/services/SecurityService.php";
    require_once "app/services/DocumentService.php";

    class JobController{
        public static function index(){
            AuthController::checkLogged();
            if ($_SESSION["user_role"] == "medic"){
                require_once "app/views/layout.php";
                require_once "app/models/Job_Applications.php";
                $applications = Job_Applications::getByApplicant($_SESSION["user_id"]);
                require_once "app/views/job/medic_applications.php";
            }
            else if ($_SESSION["user_role"] == "hospital"){
                require_once "app/views/layout.php";
                require_once "app/models/Job_Applications.php";
                $applications = Job_Applications::getByHirer($_SESSION["user_id"]);
                require_once "app/views/job/hospital_applications.php";
            }
            else{
                http_response_code(403);
                return;
            }
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

                //Get the available hospitals
                $hospitals = Hospitals::getHospitalsAndCounties();
                //Generate CSRF token for the form
                $csrf_token = SecurityService::generateCSRFToken();
                require_once "app/views/job/apply.php";
            }
            else if ($_SERVER["REQUEST_METHOD"] == "POST"){
                //Getting the unset parameters
                $res = ["ok" => true];
                $unset_params = array_filter(["hospital_id", "recaptcha_input", "csrf_token"], function($param){
                    return !isset($_POST[$param]) || $_POST[$param] == ""; 
                });

                //If there are unset parameters, return an error with the unset parameters
                if (count($unset_params) != 0){
                    $res["error"] = "Unset parameters: " . implode($unset_params);
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }
                //Checking for bots
                $grec_err = SecurityService::validateRecaptchaResp($_POST["recaptcha_input"], "job_application");
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

                require_once "app/models/Job_Applications.php";
                require_once "app/models/Application_Statuses.php";
                require_once "app/models/Hospitals.php";
                //Inserting the job application as pending
                try{
                    $app_status_id = Application_Statuses::getByName("Pending")->application_status_id;
                    Job_Applications::insert(new Job_ApplicationsData(null, $_SESSION["user_id"], $_POST["hospital_id"],
                                                                      null, $app_status_id));
                } catch (Throwable $e){
                    $res["error"] = $e->getMessage();
                    $res["ok"] = false;
                }

                //Generating a new CSRF token
                $res["csrf_token"] = SecurityService::generateCSRFToken();
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
            
            $unset_params = array_filter(["applicant_id", "application_id", "new_status_id", "new_status_name"], function($param){
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
                                                             $_POST["applicant_id"]);
                if ($err){
                    $res["error"] = $err;
                    $res["ok"] = false;
                    echo json_encode($res);
                    return;
                }
            }
            
            //Update the status of the application
            require "app/models/Job_Applications.php";
            try{
                Job_Applications::updateStatus($_POST["application_id"], $_POST["new_status_id"]);
            } catch (Throwable $e){
                $res["error"] = $e->getMessage();
                $res["ok"] = false;
            }

            echo json_encode($res);
        }

        public static function getMedicCV(){
            AuthController::checkLogged();
            //Only medics and hospitals can view the CV
            if (in_array($_SESSION["user_role"], ["hospital"]) == false){
                http_response_code(403);
                return;
            }
            //Only POST requests are allowed
            if ($_SERVER["REQUEST_METHOD"] != "GET"){
                http_response_code(405);
                return;
            }

            //Getting the unset parameters
            $unset_params = array_filter(["applicant_id"], function($param){
                return !isset($_GET[$param]) || $_GET[$param] == ""; 
            });

            //If there are unset parameters, return an error with the unset parameters
            if (count($unset_params) != 0){
                http_response_code(422);
                return;
            }

            require_once "app/models/Job_Applications.php";

            //Check if the applicant is actually applying to the hirer
            $job_app = Job_Applications::getByApplicantAndHirer($_GET["applicant_id"], $_SESSION["user_id"]);
            if (!$job_app){
                http_response_code(403);
                return;
            }
            //Try to display the medic's CV
            $err = DocumentService::displayMedicCV($_GET["applicant_id"]);
            if ($err)
                http_response_code(404);
        }

    }
?>