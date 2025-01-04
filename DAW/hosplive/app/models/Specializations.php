<?php
    require_once 'Entity.php';
    class SpecializationsData extends EntityData{
        public int|null $specialization_id;
        public string|null $specialization_name;

        function __construct(int $specialization_id = null, string $specialization_name = null){
            $this->specialization_id = $specialization_id;
            $this->specialization_name = $specialization_name;
        }
    }
    class Specializations extends Entity{
        //TO DO: Remove query1 and just return success
        public static function getIdColumn(): string{
            return 'specialization_id';
        }
    }
?>