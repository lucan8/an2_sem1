<?php
abstract class EntityData{}
abstract class Entity{
    protected static PDO $conn;
    public static function initConnection(){
        self::$conn = require "config/pdo.php";
    }
    //public function fetch(array $columns, array $where) : EntityData;
    public static function insert(EntityData $data): string{
        $query = "INSERT INTO " . static::class . " VALUES(";
        $query .= implode(", ", array_values(get_object_vars($data))) . ');';
        return $query;
    }
}
?>