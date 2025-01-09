<?php
    require_once "Entity.php";
    require_once "exceptions/AffectedRowsException.php";
    class Job_ApplicationsData extends EntityData{
        public int|null $job_application_id = 0; //Autoincrement primary key
        public int|null $applicant_id;
        public int|null $hirer_id;
        public string|null $application_date = ""; //Default to NOW() on database level
        public int|null $application_status_id;

        public function __construct(int $job_application_id = null, int $applicant_id = null,
                                    int $hirer_id = null,  string $application_date = null,
                                    int $application_status_id = null) {
            $this->job_application_id = $job_application_id;
            $this->applicant_id = $applicant_id;
            $this->hirer_id = $hirer_id;
            $this->application_date = $application_date;
            $this->application_status_id = $application_status_id;
        }
    }
    class Job_Applications extends Entity {
        //Get all applications for a medic(hospital's county, application date, status)
        public static function getByApplicant(int $applicant_id): array{
            $query = "SELECT c.county_name as county, ja.job_application_id as id,
                      ja.application_date as date, js.application_status_name as status
                      FROM Job_Applications ja
                      JOIN Hospitals h ON h.hospital_id = ja.hirer_id
                      JOIN Counties c ON c.county_id = h.county_id
                      JOIN Application_statuses js ON js.application_status_id = ja.application_status_id
                      WHERE applicant_id = ? ORDER BY ja.application_date DESC"; 
            self::printQuery($query, [$applicant_id]);

            $stm = self::$conn->prepare($query);
            
            $stm->execute([$applicant_id]);            
            return $stm->fetchAll();
        }

        //Get all applications for a hirer(appliant name, years of experience, specialization, application date, status)
        public static function getByHirer(int $hirer_id): array{
            $query = "SELECT concat(u.last_name, ' ', u.first_name) as medic_name, ja.applicant_id, m.years_exp,
                      s.specialization_name as specialization, ja.job_application_id as id,
                      ja.application_date as date, js.application_status_name as status, js.application_status_id as status_id
                      FROM Job_Applications ja 
                      JOIN Medics m ON m.medic_id = ja.applicant_id
                      JOIN Specializations s ON s.specialization_id = m.specialization_id
                      JOIN Users u ON u.user_id = m.medic_id
                      JOIN Application_Statuses js ON js.application_status_id = ja.application_status_id
                      WHERE hirer_id = ? ORDER BY ja.application_date DESC"; 
            self::printQuery($query, [$hirer_id]);

            $stm = self::$conn->prepare($query);
            
            $stm->execute([$hirer_id]);            
            return $stm->fetchAll();
        }

        //Get the application that has the given applicant and hirer
        public static function getByApplicantAndHirer(int $applicant_id, int $hirer_id): Job_ApplicationsData|false{
            $query = "SELECT * FROM " . static::class . " WHERE applicant_id = ? AND hirer_id = ?";
            self::printQuery($query, [$applicant_id, $hirer_id]);
            
            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, self::class . "Data");
            $stm->execute([$applicant_id, $hirer_id]);

            return $stm->fetch();
        }
        
        //Update the status of the application to the new status
        public static function updateStatus(int $application_id, int $new_status_id){
            $query = "UPDATE " . static::class . " SET application_status_id = ? WHERE job_application_id = ?";
            self::printQuery($query, [$new_status_id, $application_id]);

            $stm = self::$conn->prepare($query);
            $stm->execute([$new_status_id, $application_id]);

            $row_count = $stm->rowCount();
            if ($row_count != 1)
                throw new AffectedRowsException($row_count, 1);
        }

        public static function getIdColumn(): string{
            return 'job_application_id';
        }
    }
?>