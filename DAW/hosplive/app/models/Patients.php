<?php
    require_once "Entity.php";
    class PatientsData extends EntityData{
        public int|null $patient_id;

        function __construct(int $patient_id = null){
            $this->patient_id = $patient_id;
        }
    }

    class Patients extends Entity{
        public static function getIdColumn(): string{
            return 'patient_id';
        }

        public static function getNeccesaryRows(): array{
            return ["patient_id"];
        }
    }
?>