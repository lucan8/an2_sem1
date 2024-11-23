<?php
    require_once 'Entity.php';
    class Hospitals_MedicsData extends EntityData{
        public int $hospital_id;
        public int $medic_id;
        public DateTime $hire_date;
    }
    
    class Hospitals_Medics extends Entity{
        //public function fetch(array $columns, array $where) : EntityData;

        //Returns all entrys with a given hospital_id,
        //for which the medics specialization is the same as the given spec_id
        
    }
?>