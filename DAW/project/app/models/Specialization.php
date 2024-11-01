<?php
    require_once 'Entity.php';
    class SpecializationData extends EntityData{
        public int $specialization_id;
        public string $specialization_name;

        function __construct(int $specialization_id, string $specialization_name){
            $this->specialization_id = $specialization_id;
            $this->specialization_name = $specialization_name;
        }
    }

    class Specialization extends Entity{
        //public function fetch(array $columns, array $where) : EntityData;

        //TO DO: Remove query1 and just return success
    }
?>