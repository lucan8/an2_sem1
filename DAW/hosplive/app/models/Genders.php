<?php
    require_once 'Entity.php';
    class GendersData extends EntityData{
        public int $gender_id = 0;
        public string $gender_name;

        function __construct(){}
        public function set(string $gender_name){
            $this->gender_name = $gender_name;
        }
    }

    class Genders extends Entity{
    }
?>