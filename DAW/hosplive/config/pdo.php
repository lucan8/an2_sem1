<?php
    require_once "config/config.php";

    $pdo = null;
    // try{
    //     $pdo = new PDO(
    //     'mysql:host=' . $config_prod['host'] . ';port=' . $config_prod['port'] . ';dbname=' . $config_prod['db_name'],
    //     $config_prod['username'],
    //     $config_prod['password']
    //     );
    // }
    // catch(PDOException $e){
    //     echo $e->getMessage() . "<br>";
    //     echo 'mysql:host=' . $config_prod['host'] . ';port=' . $config_prod['port'] . ';dbname=' . $config_prod['db_name'] . "<br>";
    //     echo $config_prod['username'] . "<br>";
    //     echo $config_prod['password'] . "<br>";
    // }

    // $pdo = null;
    // try{
    //     $pdo = new PDO(
    //     'mysql:host=' . $config_root['host'] . ';port=' . $config_root['port'],
    //     $config_root['username'],
    //     $config_root['password']
    //     );
    // }
    // catch(PDOException $e){
    //     echo $e->getMessage() . "<br>";
    //     echo 'mysql:host=' . $config_root['host'] . ';port=' . $config_root['port'] . "<br>";
    //     echo $config_root['username'] . "<br>";
    //     echo $config_root['password'] . "<br>";
    // }
    try{
        $pdo = new PDO(
        'mysql:host=' . $config_hosp['host'] . ';port=' . $config_hosp['port'],
        $config_hosp['username'],
        $config_hosp['password']
        );
    }
    catch(PDOException $e){
        echo $e->getMessage() . "<br>";
        echo 'mysql:host=' . $config_hosp['host'] . ';port=' . $config_hosp['port'] . "<br>";
        echo $config_hosp['username'] . "<br>";
        echo $config_hosp['password'] . "<br>";
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>