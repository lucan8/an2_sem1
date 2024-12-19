<?php
require_once "app/controllers/AuthController.php";
class Controller {
    public static function index() {
        AuthController::checkLogged();
        require_once "app/views/layout.php";
        require_once "app/views/index.php";
    }
}
?>