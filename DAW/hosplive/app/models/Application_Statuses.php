<?php
    require_once "Entity.php";
    class Application_StatusesData extends EntityData{
        public int|null $application_status_id = 0; //Autoincrement primary key
        public string|null $application_status_name;

        function __construct(string $application_status_id = null, string $application_status_name = null){
            $this->application_status_id = $application_status_id;
            $this->application_status_name = $application_status_name;
        }
    };

    class Application_Statuses extends Entity{
        public static function getByName(string $application_status_name): Application_StatusesData|false{
            $query = "SELECT * FROM " . static::class . " WHERE application_status_name = ?";
            self::printQuery($query, [$application_status_name]);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, static::class . "Data");

            $stm->execute([$application_status_name]);
            return $stm->fetch();
        }

        public static function getById(int $application_status_id): Application_StatusesData|false{
            $query = "SELECT * FROM " . static::class . " WHERE application_status_id = ?";
            self::printQuery($query, [$application_status_id]);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, static::class . "Data");

            $stm->execute([$application_status_id]);
            return $stm->fetch();
        }
    }
?>