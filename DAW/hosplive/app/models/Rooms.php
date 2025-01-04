<?php
    require_once 'Entity.php';
    class RoomsData extends EntityData{
        public int|null $room_id;
        public int|null $hospital_id;

        function __construct(int $room_id = null, int $hospital_id = null){
            $this->room_id = $room_id;
            $this->hospital_id = $hospital_id;
        }
    }

    class Rooms extends Entity{
        public function getHospitalRooms(int $hospital_id): array{
            $query = "SELECT * FROM " . static::class . " WHERE hospital_id = ?";
            self::printQuery($query, [$hospital_id]);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, static::class . "Data");
            
            $stm->execute([$hospital_id]);

            return $stm->fetchAll();
        }

        //Should actually return the composite primary key
        public static function getIdColumn(): string{
            return 'room_id';
        }
    }
?>