<?php
    require_once 'Entity.php';

    class HospitalsData extends EntityData{
        public int $hospital_id;
        public int $county_id;
        public string $phone_number;

        function __construct(){}
        public function set(int $hospital_id, int $county_id, string $phone_number){
            $this->hospital_id = $hospital_id;
            $this->county_id = $county_id;
            $this->phone_number = $phone_number;
        }
    }

    class Hospitals extends Entity{
        public const OPENING_TIME = "08:00:00";
        public const CLOSING_TIME = "22:00:00";
        // Returns hospital from passed county
        public static function getByCounty(int $county_id): HospitalsData{
            $query = "SELECT * FROM " . static::class . " WHERE county_id = ?";
            self :: printQuery($query, [$county_id]);

            $stm = self :: $conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");
            
            $stm->execute([$county_id]);

            return $stm->fetch();
        }

        //Returns an assoc array of hospitals(county_name: HospitalsData)
        public static function getHospitalsAndCounties(): array{
            $query = "SELECT h.hospital_id, c.county_name, h.phone_number, h.county_id
                      FROM hospitals h JOIN counties c ON h.county_id = c.county_id";
            self :: printQuery($query);

            $stm = self :: $conn->query($query);
            $stm->setFetchMode(PDO::FETCH_ASSOC);
            
            //Creating an assoc array of hospitals
            $hospitals = [];
            foreach($stm->fetchAll() as $row)
                $hospitals[$row['county_name']] = new HospitalsData($row['hospital_id'],
                                                                    $row['county_id'],
                                                                    $row['phone_number']);

            return $hospitals;
        }  
    }
?>