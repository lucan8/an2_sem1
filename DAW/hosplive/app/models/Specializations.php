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
        //Returns all specializations present in hospital given by argument
        static public function getHospitalSpecialization(int $hospital_id): array{
            $query = "SELECT s.specialization_id, s.specialization_name
                      FROM " . static::class . " s JOIN medics m ON m.specialization_id = s.specialization_id
                      JOIN hospitals_medics hm ON hm.medic_id = m.medic_id
                      WHERE hm.hospital_id = ?";
            self::printQuery($query, [$hospital_id]);
            $stm = self :: $conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, static::class . "Data");
            $stm->execute([$hospital_id]);

            return $stm->fetchAll();
        }
        //TO DO: Remove query1 and just return success
    }
?>