<?php
require_once "exceptions/FileOpenException.php";
abstract class EntityData{}
abstract class Entity{
    private const QUERY_LOG_FILE = "query_log.txt";
    protected static PDO|null $conn = null;
    protected static $log_file = null; 

    //Should be called only once before any other method
    public static function init(){
        if (self::isConnectionSet() || self::isQueryLogSet())
            return;
        self::setConnection();
        self::setLogFile();
    }

    public static function isConnectionSet(): bool{
        return self::$conn != null;
    }

    public static function isQueryLogSet(): bool{
        return self::$log_file != null;
    }

    private static function setConnection(){
        require_once 'config/pdo.php';
        $pdo = &$pdo;
        self::$conn = &$pdo;
    }

    private static function setLogFile(){
        self::$log_file = fopen(self::QUERY_LOG_FILE, "w");
        if (self::$log_file == false)
            throw new FileOpenException(self::QUERY_LOG_FILE);
    }

    public static function printQuery(string $query, array $params = []){
        if (count($params) != 0){
            $placeholders = array_fill(0, count($params), '?');
            $query = str_replace($placeholders, $params, $query);
        }
         
        fwrite(self::$log_file, date("d/m/yy H:i:s: ") . $query . "\n");
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