<?php
    require_once 'Entity.php';

    class HospitalsData{
        public int $hospital_id;
        public int $county_id;
        public string $phone_number;

        function __construct(int $hospital_id, int $county_id, string $phone_number){
            $this->hospital_id = $hospital_id;
            $this->county_id = $county_id;
            $this->phone_number = $phone_number;
        }
    }

    class Hospitals extends Entity{
        // Returns hospital from passed county
        public static function getByCounty(int $county_id): HospitalsData{
            require_once 'Counties.php';
            $query = "SELECT * FROM " . static::class . " WHERE county_id = ?";
            self :: printQuery($query, [$county_id]);

            $stm = self :: $conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");
            
            $stm->execute([$county_id]);

            return $stm->fetch();
        }

        //Returns an array of all hospitals and their counties names
        //TO DO: Pairs are: county_name: Hospital_object
        public static function getHospitalsAndCounties(): array{
            $query = "SELECT h.hospital_id, h.county_id, c.county_name, h.phone_number
                      FROM hospitals h JOIN counties c ON h.county_id = c.county_id";
            self :: printQuery($query);

            $stm = self :: $conn->query($query);
            $stm->setFetchMode(PDO::FETCH_ASSOC);

            return $stm->fetchAll();
        }

    }
?>