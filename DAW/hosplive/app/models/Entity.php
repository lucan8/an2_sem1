<?php
require_once "exceptions/FileOpenException.php";
require_once "exceptions/AffectedRowsException.php";
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

    static function getConstants() {
        $oClass = new ReflectionClass(static::class);
        return $oClass->getConstants(ReflectionClassConstant::IS_PUBLIC);
    }

    //Does not work when params is assoc array
    //TODO: Write custom replacer for ? in query
    public static function printQuery(string $query, array $params = []){
        if (count($params) != 0){
            $placeholders = array_fill(0, count($params), '?');
            $query = implode(str_replace($placeholders, $params, str_split($query)),);
        }
         
        fwrite(self::$log_file, date("d/m/yy H:i:s: ") . $query . "\n");
    }

    public static function insert(EntityData $data): bool{
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
        $success = $stm->execute($inserted_values);

        $affectred_rows = $stm->rowCount();
        if ($affectred_rows != 1)
            throw new AffectedRowsException($affectred_rows, 1);
        return $success;
    }

    abstract public static function getIdColumn(): string;

    //Should be called only by classes that have an id column
    //Returns object of the representive data class
    public static function getById(int $id){
        $query = "SELECT * FROM " . static::class . " WHERE " . static::getIdColumn() . " = ?";
        self::printQuery($query, [$id]);

        $stm = self::$conn->prepare($query);
        $stm->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, static::class . "Data");
        
        $stm->execute([$id]);
        return $stm->fetch();
    }

    public static function getAll(): array{
        $query = "SELECT * FROM " . static::class;
        self::printQuery($query);

        $stm = self::$conn->prepare($query);
        $stm->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, static::class . "Data");
        
        $stm->execute();
        return $stm->fetchAll();
    }

    //Gets the rows that should be used in the form used for inserting into the model
    //abstract public static function getNeccesaryRows(): array; 
    //Gets the rows which have default values in the model
    //abstract public static function getDefaultRows(): array;
}
?>