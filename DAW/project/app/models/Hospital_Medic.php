<?php
    require_once 'Entity.php';
    class Hospital_MedicData extends EntityData{
        public int $hospital_id;
        public int $medic_id;
        public DateTime $hire_date;
    }

    class Hospital_Medic extends Entity{
        //public function fetch(array $columns, array $where) : EntityData;

        //TO DO: Remove query1 and just return success
    }
?>