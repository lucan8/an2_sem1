<?php
    require_once 'Entity.php';
    class MedicsData extends EntityData{
        public int|null $medic_id;
        public int|null $specialization_id;
        public int|null $years_exp;

        function __construct($medic_id = null, int $specialization_id = null, int $years_exp = null){
            $this->medic_id = $medic_id;
            $this->specialization_id = $specialization_id;
            $this->years_exp = $years_exp;
        }
    }

    class Medics extends Entity{
        public static function getMedicsBySpec(int $spec_id): array{
            $query = "SELECT * FROM " . static::class . " WHERE specialization_id = ?";
            self::printQuery($query, [$spec_id]);

            $stm = self :: $conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, static::class . "Data");

            $stm->execute([$spec_id]);
            return $stm->fetchAll();
        }

        //Returns an array of medics from a given hospital with a given specialization
        public static function getByHospAndSpec(int $hosp_id, int $spec_id): array{
            //Complicated for now, will be simplified after medics and hospitals will use only user_ids
            $query = "SELECT concat(u.last_name,' ', u.first_name) as medic_name, m.medic_id, m.specialization_id, m.years_exp
                      FROM " . static::class . " m
                      JOIN users u ON m.medic_id = u.user_id
                      JOIN job_applications ja ON u.user_id = ja.applicant_id
                      WHERE ja.hirer_id = ? AND m.specialization_id = ?
                      AND ja.application_status_id = (SELECT application_status_id FROM application_statuses WHERE application_status_name = 'Hired')";

            self::printQuery($query, [$hosp_id, $spec_id]);
            
            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_ASSOC);
            
            $stm->execute([$hosp_id, $spec_id]);
            
            return $stm->fetchAll();
        }

        public static function getNeccesaryRows(): array{
            return ['medic_id', 'specialization_id', 'years_exp'];
        }

        public static function getIdColumn(): string{
            return 'medic_id';
        }
    }
?>