<?php
    require_once 'Entity.php';
    class CountiesData extends EntityData{
        public int|null $county_id = 0; //Autoincrement primary key
        public string|null $county_name;

        function __construct(int $county_id = null, string $county_name = null){
            $this->county_id = $county_id;
            $this->county_name = $county_name;
        }
    }

    class Counties extends Entity{
        public static function getById(int $county_id): CountiesData{
            $query = "SELECT * FROM " . static::class . " WHERE county_id = ?";
            self :: printQuery($query, [$county_id]);

            $stm = self :: $conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");
            
            $stm->execute([$county_id]);

            return $stm->fetch();
        }
    }
?>