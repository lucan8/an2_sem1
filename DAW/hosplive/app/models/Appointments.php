<?php
    require_once 'Entity.php';
    class AppointmentsData extends EntityData{
        //Auto increment(auto increment keys are set to 0 by default so that they are ignored when inserting)
        public int $appointment_id = 0;

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

        public static function getByUser($user_id): array{
            $query = "SELECT * FROM " . static::class . " WHERE user_id = ?";
            self::printQuery($query, [$user_id]);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");
            
            $stm->execute([$user_id]);

            return $stm->fetchAll();
        }
        
        //Returns appointments data substituting the ids with the names
        public static function getAppointments($user_id): array{
            $query = "SELECT a.appointment_id as id, c.county_name as county,
                             m.medic_name, m.medic_id, h.hospital_id, a.appointment_date as date,
                             a.appointment_time as time, a.room_id as room
                      FROM " . static::class . " a
                      JOIN hospitals h ON a.hospital_id = h.hospital_id
                      JOIN medics m ON a.medic_id = m.medic_id
                      JOIN counties c ON h.county_id = c.county_id
                      WHERE a.user_id = ? ORDER BY date, time";
            self::printQuery($query, [$user_id]);

            $stm = self::$conn->prepare($query);
            $stm->execute([$user_id]);

            return $stm->fetchAll();
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

        public static function updateDateTime($appointment_id, $appointment_date, $appointment_time): bool{
            $query = "UPDATE " . static::class . " SET appointment_date = ?, appointment_time = ?
                      WHERE appointment_id = ?";
            self::printQuery($query, [$appointment_date, $appointment_time, $appointment_id]);

            $stm = self::$conn->prepare($query);
            $success = $stm->execute([$appointment_date, $appointment_time, $appointment_id]);

            $affectred_rows = $stm->rowCount();
            if ($affectred_rows != 1)
                throw new AffectedRowsException($affectred_rows, 1);
            return $success;
        }
    }
?>