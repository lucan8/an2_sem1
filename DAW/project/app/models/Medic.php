<?php
    require_once 'Entity.php';
    class MedicData extends EntityData{
        public int $medic_id;
        public string $medic_name;
        public int $specialization_id;
        public int $years_exp;

        function __construct(int $medic_id, string $medic_name, int $specialization_id, int $years_exp){
            $this->medic_id = $medic_id;
            $this->medic_name = $medic_name;
            $this->specialization_id = $specialization_id;
            $this->years_exp = $years_exp;
        }
    }

    class Medic extends Entity{
        //public function fetch(array $columns, array $where) : EntityData;

        //TO DO: Remove query1 and just return success
    }
?>