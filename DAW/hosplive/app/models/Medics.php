<?php
    require_once 'AbstractUser.php';
    class MedicsData extends AbstractUserData{
        //Auto increment(auto increment keys are set to 0 by default so that they are ignored when inserting)
        public int|null $medic_id = 0;
        public int|null $specialization_id;
        public int|null $years_exp;

        function __construct(int $user_id = null, $medic_id = null,
                             int $specialization_id = null, int $years_exp = null){
            parent::__construct($user_id);
            $this->medic_id = $medic_id;
            $this->specialization_id = $specialization_id;
            $this->years_exp = $years_exp;
        }
    }

    class Medics extends AbstractUser{
        public static function getMedicsBySpec($spec_id): array{
            $query = "SELECT * FROM " . static::class . " WHERE specialization_id = ?";
            self::printQuery($query, [$spec_id]);

            $stm = self :: $conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, static::class . "Data");

            $stm->execute([$spec_id]);
            return $stm->fetchAll();
        }

        //Returns an array of medics from a given hospital with a given specialization
        public static function getByHospAndSpec($hosp_id, $spec_id): array{
            $query = "SELECT u.last_name || ' ' || u.first_name as medic_name, m.medic_id, m.specialization_id, m.years_exp
                      FROM " . static::class . " m JOIN hospitals_medics hm ON m.medic_id = hm.medic_id
                      JOIN users u ON m.user_id = u.user_id
                      WHERE hm.hospital_id = ? AND m.specialization_id = ?";

            self::printQuery($query, [$hosp_id, $spec_id]);
            
            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_ASSOC);
            
            $stm->execute([$hosp_id, $spec_id]);
            
            return $stm->fetchAll();
        }

        public static function getNeccesaryRows(): array{
            return ['specialization_id', 'years_exp'];
        }

        public static function getIdColumn(){
            return 'medic_id';
        }
    }
?>