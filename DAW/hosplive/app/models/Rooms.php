<?php
    require_once 'Entity.php';
    class RoomsData extends EntityData{
        public int $room_id;
        public int $hospital_id;

        function __construct(int $room_id, int $hospital_id){
            $this->room_id = $room_id;
            $this->hospital_id = $hospital_id;
        }
    }

    class Rooms extends Entity{
        //public function fetch(array $columns, array $where) : EntityData;
        //TO DO: Remove query1 and just return success
        public function getHospitalRooms($hospital_id): array{
            $query = "SELECT * FROM " . static::class . " WHERE hospital_id = ?";
            self::printQuery($query, [$hospital_id]);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");
            
            $stm->execute([$hospital_id]);

            return $stm->fetchAll();
        }
    }
?>