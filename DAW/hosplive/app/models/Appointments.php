<?php
    require_once 'Entity.php';
    class AppointmentsData extends EntityData{
        public int $user_id;
        public int $hospital_id;
        public int $medic_id;
        public int $room_id;
        public string $appointment_date;
        public string $appointment_time;  
        public int $duration;

        function __construct(int|null $user_id, int $hospital_id, int $medic_id, int $room_id,
                             string $appointment_date, string $appointment_time, int|null $duration){
            $this->user_id = $user_id;
            $this->hospital_id = $hospital_id;
            $this->medic_id = $medic_id;
            $this->room_id = $room_id;
            $this->appointment_date = $appointment_date;
            $this->appointment_time = $appointment_time;
            $this->duration = $duration;
        }
    }

    class Appointments extends Entity{
        public static function insert(AppointmentsData $data){
            self::_insert($data);
        }

        public static function getFreeRooms($hospital_id, $appointment_date){
            $query = "SELECT room_id FROM " . static::class . " WHERE hospital_id = ? AND appointment_date = ?";
            self::printQuery($query, [$hospital_id, $appointment_date]);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");
            
            $stm->execute([$hospital_id, $appointment_date]);

            $stm->fetchAll();
        }
        
        //INCORRECT: For now returns appointments made at with hospital, medic and date
        public static function getFreeTimeIntervals($hospital, $medic_id, $appointment_date){
            $query = "SELECT appointment_time, duration FROM " . static::class . " WHERE hospital_id = ? AND medic_id = ? AND appointment_date = ?";
            self::printQuery($query, [$hospital, $medic_id, $appointment_date]);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");
            
            $stm->execute([$hospital, $medic_id, $appointment_date]);

            return $stm->fetchAll();
        }
    }
?>