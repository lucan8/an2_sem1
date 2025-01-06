<?php
    require_once "config/router.php";
    require_once "app/controllers/AuthController.php";
    require_once "app/models/Entity.php";

    //Run this before:
    //cd "C:\Program Files\MySQL\MySQL Server 8.4\bin"
    //mysqld --defaults-file="C:\ProgramData\MySQL\MySQL Server 8.4\my.ini" --init-file="C:\mysql_init.txt" --console
    //This resets the password everytime
    //Initialization part
    Entity::init();
    AuthController::setAuth();
    
    //Only load from this server and google recaptcha
    //Should be set differently on each view depending on what is needed
    header("Content-Security-Policy: default-src 'self'; script-src 'self' https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/releases/zIriijn3uj5Vpknvt_LnfNbF/recaptcha__ro.js; style-src 'self'; img-src 'self'; connect-src 'self'; font-src 'self'; frame-src 'self' https://www.google.com/recaptcha/;");
    
    $router = new Router();
    $router->direct();
?>
