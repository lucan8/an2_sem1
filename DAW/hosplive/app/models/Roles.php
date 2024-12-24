<?php
    class RolesData extends EntityData{
        public int $role_id = 0;
        public string $role_name;

        function __construct(){}
        public function set(string $role_name){
            $this->role_name = $role_name;
        }
    }

    class Roles extends Entity{
        public static function getAll() : array{
            $query = "SELECT * FROM roles WHERE role_name != 'Admin'";
            self::printQuery($query);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, "RolesData");
            
            $stm->execute();
            return $stm->fetchAll();
        }

        public static function getById(int $id): RolesData{
            $query = "SELECT * FROM roles WHERE role_id = ?";
            self::printQuery($query, [$id]);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, "RolesData");
            
            $stm->execute([$id]);
            return $stm->fetch();
        }
    }