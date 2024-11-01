<?php
    require_once 'Entity.php';
    class AppointmentsData extends EntityData{
        public int $user_id;
        public int $hospital_id;
        public int $medic_id;
        public int $room_id;
        public DateTime $appointment_date;
        public int $duration;
    }

    class Appointment extends Entity{
        //public function fetch(array $columns, array $where) : EntityData;

        //TO DO: Remove query1 and just return success
    }
?>