<?php
    require_once "config/router.php";
    require_once "app/models/Entity.php";

    //Run this before:
    //cd "C:\Program Files\MySQL\MySQL Server 8.4\bin"
    //mysqld --defaults-file="C:\ProgramData\MySQL\MySQL Server 8.4\my.ini" --init-file="C:\mysql_init.txt" --console
    //This resets the password everytime
    Entity::init();

    $router = new Router();
    $router->direct();
?>
