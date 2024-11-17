<?php
abstract class EntityData{}
abstract class Entity{
    protected static $conn = null;

    public static function isConnectionSet(): bool{
        return self::$conn != null;
    }

    public static function setConnection(){
        require_once 'config/pdo.php';
        $pdo = &$pdo;
        self::$conn = &$pdo;
    }

    public static function insert(EntityData $data): string{
        $query = "INSERT INTO " . static::class . " VALUES(";
        $query .= implode(", ", array_values(get_object_vars($data))) . ');';
        return $query;
    }

    public static function getAll(): array{
        $query = "SELECT * FROM " . static::class;
        $stm = self::$conn->prepare($query);
        $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");
        $stm->execute();
        return $stm->fetchAll();
    }
}
?>