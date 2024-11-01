<?php
    $host = '127.0.0.1';
    $db = 'HospitalDB';
    $user = 'hospital_user';
    $pass = 'password';
    $port = '100';

    try{
        $pdo  = new PDO('mysql:host=' . $host . ';' . 'port=' . $port . ';' . 'dbname=' . $db, $user, $pass);
    } catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        exit(1);
    }
    return $pdo;
    
?>