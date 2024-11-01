<?php
    require_once 'Entity.php';
    class CountyData extends EntityData{
        public int $county_id;
        public string $county_name;

        function __construct(int $county_id, string $county_name){
            $this->county_id = $county_id;
            $this->county_name = $county_name;
        }
    }

    class County extends Entity{
        //public function fetch(array $columns, array $where) : EntityData;

        //TO DO: Remove query1 and just return success
    }
?>