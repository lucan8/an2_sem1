<?php
$routes = [
    "hosplive/index" => ["Controller", "index"],
    "hosplive/get_medics" => ["Controller", "getMedics"],
    "hosplive/get_statuses" => ["Controller", "getStatuses"],
    "hosplive/appointments/make_appointment" => ["AppointmentsController", "add"],
    "hosplive/appointments/getUnavailableTimes" => ["AppointmentsController", "getUnavailableTimes"],
    "hosplive/appointments/getFreeRoom" => ["AppointmentsController", "getFreeRoom"],
    "hosplive/appointments/getAppointments" => ["AppointmentsController", "getAppointments"],
    "hosplive/appointments/appointments" => ["AppointmentsController", "index"],
    "hosplive/appointments/cancel_appointment" => ["AppointmentsController", "remove"],
    "hosplive/appointments/edit_appointment" => ["AppointmentsController", "edit"],
    "hosplive/appointments/add_appointment_info" => ["AppointmentsController", "addInfo"],
    "hosplive/appointments/getConstants" => ["AppointmentsController", "getConstants"],
    "hosplive/auth/index" => ["AuthController", "index"],
    "hosplive/auth/add_user" => ["AuthController", "add"],
    "hosplive/auth/verify_user" => ["AuthController", "verifyUser"],
    "hosplive/auth/specialize_user" => ["AuthController", "specializeUser"],
    "hosplive/auth/resend_verif_code" => ["AuthController", "resendVerifCode"],
    "hosplive/auth/login" => ["AuthController", "login"],
    "hosplive/auth/logout"=> ["AuthController", "logout"],
    "hosplive/job/index" => ["JobController", "index"],
    "hosplive/job/apply" => ["JobController", "apply"],
    "hosplive/job/change_status" => ["JobController", "changeStatus"],
    "hosplive/job/get_medic_cv" => ["JobController", "getMedicCV"]
];

class Router {
    protected $uri;

    public function __construct() {
        // Get the current URI
        $this->uri = trim($_SERVER["REQUEST_URI"], "/");
        $this->uri = explode("?", $this->uri)[0];
    }

    public function direct() {
        global $routes;
        
        if (array_key_exists($this->uri, $routes)) {
            // Get the controller and method
            [$controller, $method] = $routes[$this->uri];

            // Load the controller file if it hasn't been autoloaded
            require_once "app/controllers/{$controller}.php";

            // Call the method
            return $controller::$method();
        }
        require_once "app/views/404.php";
    }
}
?>