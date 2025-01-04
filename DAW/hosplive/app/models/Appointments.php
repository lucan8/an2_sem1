<?php
    require_once 'Entity.php';
    class AppointmentsData extends EntityData{
        //Auto increment keys are set to 0 by default so that they are ignored when inserting
        //Should be read only but the fetch mode is set to FETCH_CLASS which sets the properties directly
        public int|null $appointment_id = 0;

        public int|null $patient_id;
        public int|null $hospital_id;
        public int|null $medic_id;
        public int|null $room_id;
        
        public string|null $appointment_date;
        public string|null $appointment_time;
        public int|null $duration;

        function __construct(int $appointment_id = null, int $patient_id = null, int $hospital_id = null,
                             int $medic_id = null, int $room_id = null, string $appointment_date = null,
                             string $appointment_time = null, int $duration = Appointments :: DEFAULT_DURATION){
            $this->appointment_id = $appointment_id;
            $this->patient_id = $patient_id;
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
        
        //Returns appointments data substituting the ids with the names
        public static function getAppointmentsByPatient(int $patient_id): array{
            $query = "SELECT a.appointment_id as id, c.county_name as county,
                             CONCAT(u.last_name, ' ', u.first_name) as medic_name, m.medic_id, h.hospital_id,
                             a.appointment_date as date, a.appointment_time as time,
                             a.room_id as room, a.duration
                      FROM " . static::class . " a
                      JOIN hospitals h ON a.hospital_id = h.hospital_id
                      JOIN medics m ON a.medic_id = m.medic_id
                      JOIN counties c ON h.county_id = c.county_id
                      JOIN users u ON m.medic_id = u.user_id
                      WHERE a.patient_id = ? ORDER BY date, time";
            self::printQuery($query, [$patient_id]);

            $stm = self::$conn->prepare($query);
            $stm->execute([$patient_id]);

            return $stm->fetchAll();
        }

        //Gets the first free room for a given hospital, date and time
        //If none is found throw error
        public static function getFreeRoom(int $hospital_id, string $appointment_date, string $appointment_time): AppointmentsData{
            $query = "SELECT room_id FROM rooms WHERE hospital_id = ? EXCEPT
                      SELECT room_id FROM " . static::class .
                      " WHERE hospital_id = ? AND appointment_date = ? AND appointment_time = ? LIMIT 1";
            self::printQuery($query, [$hospital_id, $hospital_id, $appointment_date, $appointment_time]);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, static::class . "Data");
            
            $stm->execute([$hospital_id, $hospital_id, $appointment_date, $appointment_time]);

            $room = $stm->fetch();
            if ($room === false)
                throw new Exception("No free room found for the given hospital, date and time");
            return $room;
        }
        
        public static function getByHospMedDate(int $hospital_id, int $medic_id, string $appointment_date): array{
            // Getting the appointments made at the given hospital, medic and date ordered by time
            $query = "SELECT * FROM " . static::class . " WHERE hospital_id = ? AND medic_id = ?
                      AND appointment_date = ? ORDER BY appointment_time";
            self::printQuery($query, [$hospital_id, $medic_id, $appointment_date]);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, static::class . "Data");
            
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
        
        public static function removeById(int $appointment_id): bool{
            $query = "DELETE FROM " . static::class . " WHERE appointment_id = ?";
            self::printQuery($query, [$appointment_id]);
    
            $stm = self::$conn->prepare($query);
            $success = $stm->execute([$appointment_id]);
    
            //Checking if exactly one row was affected
            $affectred_rows = $stm->rowCount();
            if ($affectred_rows != 1)
                throw new AffectedRowsException($affectred_rows, 1);
            return $success;
        }
        public static function getIdColumn(): string{
            return 'appointment_id';
        }
    }
?>