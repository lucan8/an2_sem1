<?php
    require_once 'Entity.php';

    class HospitalsData extends EntityData{
        public int|null $hospital_id;
        public int|null $county_id;

        function __construct(int $hospital_id = null, int $county_id = null){
            $this->hospital_id = $hospital_id;
            $this->county_id = $county_id;
        }
    }

    class Hospitals extends Entity{
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
            $query = "SELECT h.hospital_id, c.county_name, h.county_id
                      FROM Hospitals h JOIN Counties c ON h.county_id = c.county_id";
            self :: printQuery($query);

            $stm = self :: $conn->query($query);
            $stm->setFetchMode(PDO::FETCH_ASSOC);
            
            //Creating an assoc array of hospitals
            $hospitals = [];
            foreach($stm->fetchAll() as $row)
                $hospitals[$row['county_name']] = new HospitalsData($row['hospital_id'], $row['county_id']);

            return $hospitals;
        }

        public static function getCountyName(int $hospital_id): string|false{
            $query = "SELECT c.county_name FROM " . static::class . " h 
                      JOIN Counties c ON h.county_id = c.county_id
                      WHERE hospital_id = ?";
            self :: printQuery($query, [$hospital_id]);

            $stm = self :: $conn->prepare($query);
            $stm->execute([$hospital_id]);

            return $stm->fetchColumn();
        }

        public static function getNeccesaryRows(): array{
            return ['hospital_id', 'county_id'];
        }

        public static function getIdColumn(): string{
            return 'hospital_id';
        }
    }


?>