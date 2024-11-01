<?php
    require_once 'Entity.php';
    class RoomData extends EntityData{
        public int $room_id;
        public int $hospital_id;

        function __construct(int $room_id, int $hospital_id){
            $this->room_id = $room_id;
            $this->hospital_id = $hospital_id;
        }
    }

    class Room extends Entity{
        //public function fetch(array $columns, array $where) : EntityData;

        //TO DO: Remove query1 and just return success
    }
?>