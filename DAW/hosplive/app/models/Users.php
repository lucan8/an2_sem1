<?php
    //Variables which have default values do so to be ignored when checking for form parameters in the controller
    require_once 'Entity.php';
    class UsersData extends EntityData{
        //Auto increment keys are set to 0 by default so that they are ignored when inserting
        //Should be read only but the fetch mode is set to FETCH_CLASS which sets the properties directly
        public int|null $user_id = 0;
        public string|null $birth_date;
        public int|null $gender_id;
        public string|null $first_name;
        public string|null $last_name;
        public string|null $phone_number;
        public string|null $user_name;
        public string|null $password;
        public string|null $email;
        public int|null $role_id;

        //Neccessary for email verification
        //Default values are to be ignored when checking for form parameters in the controller
        public string|null $secret = "";
        public string|null $active_code = "";
        public string|null $active_code_date = ""; //Set to NOW() on a database level
        public bool|null $verified = false;

        function __construct(int $user_id = null, string $birth_date = null, int $gender_id = null, string $first_name = null,
                             string $last_name = null, string $phone_number = null, string $user_name = null,
                             string $password = null, string $email = null, int $role_id = null,
                             string $secret = null, string $active_code = null, string $active_code_date = null){
            $this->user_id = $user_id;
            $this->birth_date = $birth_date;
            $this->gender_id = $gender_id;
            $this->first_name = $first_name;
            $this->last_name = $last_name;
            $this->phone_number = $phone_number;
            $this->user_name = $user_name;
            $this->password = $password;
            $this->email = $email;
            $this->role_id = $role_id;

            $this->secret = $secret;
            $this->active_code = $active_code;
            $this->active_code_date = $active_code_date;
        }
    }

    class Users extends Entity{
        public static function getByEmail(string $email) : UsersData|false{
            $query = "SELECT * FROM users WHERE email = ?";
            self::printQuery($query, [$email]);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, static::class . "Data");
            
            $stm->execute([$email]);
            return $stm->fetch();
        }

        public static function verifyUser(string $id) : bool{
            $query = "UPDATE users SET verified = 1 WHERE user_id = ?";
            self::printQuery($query, [$id]);

            $stm = self::$conn->prepare($query);
            $success = $stm->execute([$id]);

            $affectred_rows = $stm->rowCount();
            if ($affectred_rows != 1)
                throw new AffectedRowsException($affectred_rows, 1);
            return $success;
        }

        public static function updateVerificationCode($user_id, $verif_code): bool{
            $query = "UPDATE users SET active_code = ?, active_code_date = NOW() WHERE user_id = ?";
            self::printQuery($query, [$verif_code, $user_id]);

            $stm = self::$conn->prepare($query);
            $success = $stm->execute([$verif_code, $user_id]);

            $affectred_rows = $stm->rowCount();
            if ($affectred_rows != 1)
                throw new AffectedRowsException($affectred_rows, 1);
            return $success;
        }

        public static function getIdColumn(): string{
            return 'user_id';
        }
        
    }

?>