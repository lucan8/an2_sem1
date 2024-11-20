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

        //Returns all specializations present in hospital given by argument
        static public function getHospitalSpecialization($hospital_id): array{
            $query = "SELECT s.specialization_id, s.specialization_name
                      FROM " . static::class . " s JOIN medics m ON m.specialization_id = s.specialization_id
                      JOIN hospitals_medics hm ON hm.medic_id = m.medic_id
                      WHERE hm.hospital_id = ?";
            self::printQuery($query, [$hospital_id]);
            $stm = self :: $conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");
            $stm->execute([$hospital_id]);

            return $stm->fetchAll();
        }
        //TO DO: Remove query1 and just return success
    }
?>