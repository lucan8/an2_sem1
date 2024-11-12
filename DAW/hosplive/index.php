<?php
    require_once "config/pdo.php";
    require_once "config/router.php";

    $router = new Router();
    $router->direct();
?>
