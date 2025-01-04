<?php
    require_once 'Entity.php';
    class GendersData extends EntityData{
        public int|null $gender_id = 0;
        public string|null $gender_name;

        function __construct(int $gender_id = null, string $gender_name = null){
            $this->gender_id = $gender_id;
            $this->gender_name = $gender_name;
        }
    }

    class Genders extends Entity{
        public static function getIdColumn(): string{
            return 'gender_id';
        }
    }
?>