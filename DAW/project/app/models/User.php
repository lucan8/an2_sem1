<?php
    require_once 'Entity.php';
    class UserData extends EntityData{
        public int $user_id;
        public string $user_name;
        public string $user_password;
        public string $user_email;
    }

    class User extends Entity{
        //public function fetch(array $columns, array $where) : EntityData;

        //TO DO: Remove query1 and just return success
    }
?>