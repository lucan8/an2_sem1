<?php
class Controller {
    public static function index() {
        require_once "app/views/index.php";
    }

    public static function make_appointment() {
        require_once "app/models/Counties.php";
        $counties = json_encode(Counties::getAll());
        require_once "app/models/Specializations.php";
        $specializations = json_encode(Specializations::getAll());
        require_once "app/views/make_appointment.php";
    }
}
?>