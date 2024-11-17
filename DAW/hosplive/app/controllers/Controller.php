<?php
class Controller {
    public static function index() {
        require_once "app/views/index.php";
    }

    public static function makeAppointment() {
        require_once "app/models/Counties.php";
        //$counties = json_encode(Counties::getAll());
        $counties = json_encode(Counties::getCounties());
        require_once "app/models/Specializations.php";
        //$specializations = json_encode(Specializations::getAll());
        $specializations = json_encode(Specializations::getSpecializations());
        require_once "app/views/make_appointment.php";
    }

    public static function getMedics() {
        require_once "app/models/Medics.php";
        $doctors = json_encode(Medics::getMedics());
        echo $doctors;
    }
}
?>