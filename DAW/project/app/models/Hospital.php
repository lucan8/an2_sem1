<?php
    require_once 'Entity.php';
    class HospitalData{
        public int $id;
        public string $county;
        public string $phone_number;

        function __construct(int $id, string $county, string $phone_number){
            $this->id = $id;
            $this->county = $county;
            $this->phone_number = $phone_number;
        }
    }

    class Hospital extends Entity{
        //public function fetch(array $columns, array $where) : EntityData;

        //TO DO: Remove query1 and just return success
    }
?>