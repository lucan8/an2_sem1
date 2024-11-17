<?php
    require_once "config/router.php";
    require_once "app/models/Entity.php";

    if (!Entity::isConnectionSet())
        Entity::setConnection();

    $router = new Router();
    $router->direct();
?>
