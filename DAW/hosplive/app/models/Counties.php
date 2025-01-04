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
        public static function getIdColumn(): string{
            return 'county_id';
        }
    }
?>