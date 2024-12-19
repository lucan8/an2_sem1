<?php
    //Variables which have default values do so to be ignored when checking for form parameters in the controller
    require_once 'Entity.php';
    class UsersData extends EntityData{
        //Auto increment primary key
        public int $user_id = 0;
        public string $birth_date;
        public int $gender_id;
        public string $first_name;
        public string $last_name;
        public string $phone_number;
        public string $user_name;
        public string $password;
        public string $email;
        public int $role_id;

        //Neccessary for email verification
        //Default values are to be ignored when checking for form parameters in the controller
        public string $secret = "";
        public string $active_code = "";
        public string $active_code_date = "";
        public bool $verified = false;

        function __construct(){}
        public function set(string $birth_date, int $gender_id, string $first_name, string $last_name,
                            string $phone_number, string $user_name, string $password, string $email, int $role_id,
                            string $secret, string $active_code){
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
        }
    }

    class Users extends Entity{
        //public function fetch(array $columns, array $where) : EntityData;
        public static function getById(int $id) : UsersData{
            $query = "SELECT * FROM users WHERE user_id = ?";
            self::printQuery($query, [$id]);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, "UsersData");
            
            $stm->execute([$id]);
            return $stm->fetch();
        }

        public static function getByEmail(string $email) : UsersData|false{
            $query = "SELECT * FROM users WHERE email = ?";
            self::printQuery($query, [$email]);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, "UsersData");
            
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
        //TO DO: Remove query1 and just return success
    }
?>