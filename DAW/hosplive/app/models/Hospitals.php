<?php
    require_once 'AbstractUser.php';

    class HospitalsData extends AbstractUserData{
        //Auto increment keys are set to 0 by default so that they are ignored when inserting
        //Should be read only but the fetch mode is set to FETCH_CLASS which sets the properties directly
        public int|null $hospital_id = 0;
        public int|null $county_id;

        function __construct(int $user_id = null, int $hospital_id = null, int $county_id = null){
            parent::__construct($user_id);
            $this->hospital_id = $hospital_id;
            $this->county_id = $county_id;
        }
    }

    class Hospitals extends AbstractUser{
        public const OPENING_TIME = "08:00";
        public const CLOSING_TIME = "22:00";
        // Returns hospital from passed county
        public static function getByCounty(int $county_id): HospitalsData|false{
            $query = "SELECT * FROM " . static::class . " WHERE county_id = ?";
            self :: printQuery($query, [$county_id]);

            $stm = self :: $conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, static::class . "Data");
            
            $stm->execute([$county_id]);

            return $stm->fetch();
        }

        //Returns an assoc array of hospitals(county_name: HospitalsData)
        public static function getHospitalsAndCounties(): array{
            $query = "SELECT h.user_id, h.hospital_id, c.county_name, h.county_id
                      FROM hospitals h JOIN counties c ON h.county_id = c.county_id";
            self :: printQuery($query);

            $stm = self :: $conn->query($query);
            $stm->setFetchMode(PDO::FETCH_ASSOC);
            
            //Creating an assoc array of hospitals
            $hospitals = [];
            foreach($stm->fetchAll() as $row)
                $hospitals[$row['county_name']] = new HospitalsData($row["user_id"], $row['hospital_id'],
                                                                    $row['county_id']);

            return $hospitals;
        }

        public static function getNeccesaryRows(): array{
            return ['county_id'];
        }

        public static function getIdColumn(){
            return 'hospital_id';
        }
    }


?>