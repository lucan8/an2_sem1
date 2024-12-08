<?php
$routes = [
    "hosplive/index" => ["Controller", "index"],
    "hosplive/make_appointment" => ["AppointmentsController", "makeAppointment"],
    "hosplive/getMedics" => ["AppointmentsController", "getMedics"],
    "hosplive/getUnavailableTimes" => ["AppointmentsController", "getUnavailableTimes"],
    "hosplive/getFreeRoom" => ["AppointmentsController", "getFreeRoom"],
    "hosplive/getAppointments" => ["AppointmentsController", "getAppointments"],
    "hosplive/appointments" => ["AppointmentsController", "index"],
    "hosplive/cancel_appointment" => ["AppointmentsController", "cancelAppointment"],
    "hosplive/edit_appointment" => ["AppointmentsController", "editAppointment"],
];

class Router {
    private $uri;

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