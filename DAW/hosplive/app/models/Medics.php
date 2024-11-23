<?php
    require_once 'Entity.php';
    class MedicsData extends EntityData{
        public int $medic_id;
        public string $medic_name;
        public int $specialization_id;
        public int $years_exp;

        function __construct(){}
        public function set(int $medic_id, string $medic_name, int $specialization_id, int $years_exp){
            $this->medic_id = $medic_id;
            $this->medic_name = $medic_name;
            $this->specialization_id = $specialization_id;
            $this->years_exp = $years_exp;
        }
    }

    class Medics extends Entity{
        public static function getMedicsBySpec($spec_id): array{
            $query = "SELECT * FROM " . static::class . " WHERE specialization_id = ?";
            self::printQuery($query, [$spec_id]);

            $stm = self :: $conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");

            $stm->execute([$spec_id]);
            return $stm->fetchAll();
        }

        //Returns an array of medics from a given hospital with a given specialization
        public static function getByHospAndSpec($hosp_id, $spec_id): array{
            $query = "SELECT m.medic_name, m.medic_id, m.specialization_id
                      FROM " . static::class . " m JOIN hospitals_medics hm ON m.medic_id = hm.medic_id
                      WHERE hm.hospital_id = ? AND m.specialization_id = ?";

            self::printQuery($query, [$hosp_id, $spec_id]);
            
            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");
            
            $stm->execute([$hosp_id, $spec_id]);
            
            return $stm->fetchAll();
        }
    }
?>