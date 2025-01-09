<?php
    require_once "Entity.php";
    class PatientsData extends EntityData{
        public int|null $patient_id;

        function __construct(int $patient_id = null){
            $this->patient_id = $patient_id;
        }
    }

    class Patients extends Entity{
        public static function getPatientName(int $pacient_id): string|false{
            $query = "SELECT CONCAT(u.last_name, ' ', u.first_name) as patient_name FROM " . self::class . " p
                      JOIN Users u ON u.user_id = p.patient_id
                      WHERE patient_id = ?";
            self::printQuery($query, [$pacient_id]);

            $stm = self::$conn->prepare($query);

            $stm->execute([$pacient_id]);

            return $stm->fetchColumn();

        }
        public static function getIdColumn(): string{
            return 'patient_id';
        }

        public static function getNeccesaryRows(): array{
            return ["patient_id"];
        }
    }
?>