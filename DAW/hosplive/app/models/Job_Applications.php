<?php
    require_once "Entity.php";
    require_once "exceptions/AffectedRowsException.php";
    class Job_ApplicationsData extends EntityData{
        public int|null $job_application_id = 0; //Autoincrement primary key
        public int|null $applicant_user_id;
        public int|null $hirer_user_id;
        public string|null $application_date = ""; //Default to NOW() on database level
        public int|null $application_status_id;

        public function __construct(int $job_application_id = null, int $applicant_user_id = null,
                                    int $hirer_user_id = null,  string $application_date = null,
                                    int $application_status_id = null) {
            $this->job_application_id = $job_application_id;
            $this->applicant_user_id = $applicant_user_id;
            $this->hirer_user_id = $hirer_user_id;
            $this->application_date = $application_date;
            $this->application_status_id = $application_status_id;
        }
    }
    class Job_Applications extends Entity {
        //Get all applications for a medic(hospital's county, application date, status)
        public static function getByApplicant(int $applicant_user_id): array{
            $query = "SELECT c.county_name as county, ja.job_application_id as id,
                      ja.application_date as date, js.application_status_name as status
                      FROM job_applications ja JOIN hospitals h ON h.user_id = ja.hirer_user_id
                      JOIN counties c ON c.county_id = h.county_id
                      JOIN application_statuses js ON js.application_status_id = ja.application_status_id
                      WHERE applicant_user_id = ? ORDER BY ja.application_date DESC"; 
            self::printQuery($query, [$applicant_user_id]);

            $stm = self::$conn->prepare($query);
            
            $stm->execute([$applicant_user_id]);            
            return $stm->fetchAll();
        }

        //Get all applications for a hirer(appliant name, years of experience, specialization, application date, status)
        public static function getByHirer(int $hirer_user_id): array{
            $query = "SELECT concat(u.last_name, ' ', u.first_name) as medic_name, ja.applicant_user_id, m.years_exp,
                      s.specialization_name as specialization, ja.job_application_id as id,
                      ja.application_date as date, js.application_status_name as status, js.application_status_id as status_id
                      FROM job_applications ja JOIN medics m ON m.user_id = ja.applicant_user_id
                      JOIN specializations s ON s.specialization_id = m.specialization_id
                      JOIN users u ON u.user_id = m.user_id
                      JOIN application_statuses js ON js.application_status_id = ja.application_status_id
                      WHERE hirer_user_id = ? ORDER BY ja.application_date DESC"; 
            self::printQuery($query, [$hirer_user_id]);

            $stm = self::$conn->prepare($query);
            
            $stm->execute([$hirer_user_id]);            
            return $stm->fetchAll();
        }

        //Get the application that has the given applicant and hirer
        public static function getByApplicantAndHirer(int $applicant_user_id, int $hirer_user_id): Job_ApplicationsData|false{
            $query = "SELECT * FROM job_applications WHERE applicant_user_id = ? AND hirer_user_id = ?";
            self::printQuery($query, [$applicant_user_id, $hirer_user_id]);
            
            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, self::class . "Data");
            $stm->execute([$applicant_user_id, $hirer_user_id]);

            return $stm->fetch();
        }
        
        //Update the status of the application to the new status
        public static function updateStatus(int $application_id, int $new_status_id){
            $query = "UPDATE job_applications SET application_status_id = ? WHERE job_application_id = ?";
            self::printQuery($query, [$new_status_id, $application_id]);

            $stm = self::$conn->prepare($query);
            $stm->execute([$new_status_id, $application_id]);

            $row_count = $stm->rowCount();
            if ($row_count != 1)
                throw new AffectedRowsException($row_count, 1);
        }
    }
?>