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
        public static function getByHospitalAndSpec($hospital_id, $spec_id): array{
            $query = "SELECT hm.hospital_id, hm.medic_id, hm.hire_date
                      FROM " . static::class . "hm JOIN medics m ON m.medic_id = hm.medic_id
                      WHERE hm.hospital_id = ? AND m.specialization_id = ?";
            self::printQuery($query, [$hospital_id, $spec_id]);
            
            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");
            
            $stm->execute([$hospital_id, $spec_id]);
            
            return $stm->fetchAll();
        }
        //TO DO: Remove query1 and just return success
    }
?>