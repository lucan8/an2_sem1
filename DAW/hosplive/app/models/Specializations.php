<?php
    require_once 'Entity.php';
    class SpecializationsData extends EntityData{
        public int $specialization_id;
        public string $specialization_name;

        function __construct(int $specialization_id, string $specialization_name){
            $this->specialization_id = $specialization_id;
            $this->specialization_name = $specialization_name;
        }
    }

    class Specializations extends Entity{
        // static public function getSpecializations() : array{
        //     return ["cardiology", "neurology", "orthopedics", "pediatrics", "oncology", "gynecology", "urology", "dermatology", "ophthalmology", "psychiatry"];
        // }
        //TO DO: Remove query1 and just return success
    }
?>