<?php
abstract class EntityData{}
abstract class Entity{
    protected static PDO|null $conn = null;

    public static function isConnectionSet(): bool{
        return self::$conn != null;
    }

    public static function setConnection(){
        require_once 'config/pdo.php';
        $pdo = &$pdo;
        self::$conn = &$pdo;
    }

    public static function printQuery(string $query, array $params = []){
        if (count($params) != 0){
            $placeholders = array_fill(0, count($params), '?');
            str_replace($placeholders, $params, $query);
        }
        print($query);
    }

    protected static function _insert(EntityData $data){
        //Setting the data as array and the placeholders(filtering out null values)
        $data_array = array_filter(get_object_vars($data));
        $inserted_values = array_values($data_array);
        $placeholders = '?' . str_repeat(', ?', count($data_array) - 1);

        //Creating the query
        $query = "INSERT INTO " . static::class . "(" .
                  implode(", ", array_keys($data_array)) .
                  ") VALUES(" . $placeholders. ")";
        

        //Print substituted query for debug
        self :: printQuery($query, $inserted_values);

        //Executing the query
        $stm = self::$conn->prepare($query);
        $stm->execute(array_values($inserted_values));
    }

    public static function getAll(): array{
        $query = "SELECT * FROM " . static::class;
        self::printQuery($query);

        $stm = self::$conn->prepare($query);
        $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");
        
        $stm->execute();
        return $stm->fetchAll();
    }
}
?>