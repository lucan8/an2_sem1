<?php
    require_once "config/config.php";

    $pdo = null;
    try{
        $pdo = new PDO(
        'mysql:host=' . $config_prod['host'] . ';port=' . $config_prod['port'] . ';dbname=' . $config_prod['db_name'],
        $config_prod['username'],
        $config_prod['password']
        );
    }
    catch(PDOException $e){
        echo $e->getMessage() . "<br>";
        echo 'mysql:host=' . $config_prod['host'] . ';port=' . $config_prod['port'] . ';dbname=' . $config_prod['db_name'] . "<br>";
        echo $config_prod['username'] . "<br>";
        echo $config_prod['password'] . "<br>";
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>