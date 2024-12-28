<?php
    require_once "AbstractUser.php";
    class PacientsData extends AbstractUserData{
        //Auto increment keys are set to 0 by default so that they are ignored when inserting
        //Should be read only but the fetch mode is set to FETCH_CLASS which sets the properties directly
        public int|null $pacient_id = 0;

        function __construct($user_id = null, int $pacient_id = null){
            parent::__construct($user_id);
            $this->pacient_id = $pacient_id;
        }
    }

    class Pacients extends AbstractUser{
        public static function getIdColumn(){
            return 'pacient_id';
        }

        public static function getNeccesaryRows(): array{
            return [];
        }
    }
?>