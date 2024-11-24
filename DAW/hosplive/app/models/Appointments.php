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

        function __construct(){}
        public function set(int $user_id, int $hospital_id, int $medic_id, int $room_id,
                            string $appointment_date, string $appointment_time,
                            int $duration = Appointments :: DEFAULT_DURATION){
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
        // Default duration of an appointment in minutes
        public const DEFAULT_DURATION = 30;
        public static function insert(AppointmentsData $data){
            self::_insert($data);
        }

        //Gets the first free room for a given hospital, date and time
        public static function getFreeRoom($hospital_id, $appointment_date, $appointment_time): AppointmentsData|false{
            $query = "SELECT room_id FROM rooms WHERE hospital_id = ? EXCEPT
                      SELECT room_id FROM " . static::class .
                      " WHERE hospital_id = ? AND appointment_date = ? AND appointment_time = ? LIMIT 1";
            self::printQuery($query, [$hospital_id, $hospital_id, $appointment_date, $appointment_time]);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");
            
            $stm->execute([$hospital_id, $hospital_id, $appointment_date, $appointment_time]);

            return $stm->fetch();
        }
        
        public static function getByHospMedDate($hospital_id, $medic_id, $appointment_date): array{
            // Getting the appointments made at the given hospital, medic and date ordered by time
            $query = "SELECT * FROM " . static::class . " WHERE hospital_id = ? AND medic_id = ?
                      AND appointment_date = ? ORDER BY appointment_time";
            self::printQuery($query, [$hospital_id, $medic_id, $appointment_date]);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");
            
            $stm->execute([$hospital_id, $medic_id, $appointment_date]);

            return $stm->fetchAll();
        }

        //Returns a list of available times for a given hospital, medic and date
        public static function getFreeTimeIntervals($hospital, $medic_id, $appointment_date): array{
            require_once "app/models/Hospitals.php";
            // Getting the appointments made at the given hospital, medic and date
            $res = self::getByHospMedDate($hospital, $medic_id, $appointment_date);

            $start_time = new DateTime($appointment_date . Hospitals :: OPENING_TIME);
            $end_time = new DateTime($appointment_date . Hospitals :: CLOSING_TIME);
            
            // Times open for appointments
            $times = [];
            // Index of the current appointment
            $curr_index = 0;
            
            //Adding all possible appointments that are different from the ones already made
            while ($start_time < $end_time){
                if ($curr_index < count($res) && $start_time == $res[$curr_index]->appointment_time)
                    $curr_index++;
                else
                    $times[] = $start_time->format("H:i");
                    
                $start_time->add(new DateInterval('PT' . self::DEFAULT_DURATION . 'M'));   
            }
            return $times;
        }
    }
?>