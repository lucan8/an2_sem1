<?php
    class RolesData extends EntityData{
        public int|null $role_id = 0; //Autoincrement primary key
        public string|null $role_name;

        function __construct(int $role_id = null, string $role_name = null){
            $this->role_id = $role_id;
            $this->role_name = $role_name;
        }
    }

    class Roles extends Entity{
        public static function getAll() : array{
            $query = "SELECT * FROM roles WHERE role_name != 'Admin'";
            self::printQuery($query);

            $stm = self::$conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, static::class . "Data");
            
            $stm->execute();
            return $stm->fetchAll();
        }


        //Returns the model name based on the role name
        public static function getChosenModel(string $role_name): string{
            switch ($role_name){
                case "hospital":
                    require_once "app/models/Hospitals.php";
                    return Hospitals :: class;
                case "pacient":
                    require_once "app/models/Patients.php";
                    return Patients :: class;
                case "medic":
                    require_once "app/models/Medics.php";
                    return Medics :: class;
                default:
                    return "";
            }
        }

        //Returns the path to the view based on the role name and the neccesary data
        public static function getChosenView(string $role_name): array|null{
            switch ($role_name){
                case "hospital":
                    require_once "app/models/Counties.php";
                    $counties = Counties :: getAll();

                    $data = array("counties" => $counties);
                    return array("route" => "app/views/auth/add_hospital.php", "data" => $data);
                case "pacient":
                    return array("route" => "app/views/auth/add_pacient.php", "data" => null);
                case "medic":
                    require_once "app/models/Specializations.php";
                    require_once "app/models/Counties.php";

                    $specializations = Specializations :: getAll();
                    $data = array("specializations" => $specializations);

                    return array("route" => "app/views/auth/add_medic.php", "data" => $data);
                default:
                    return null;
            }
        }

        public static function getIdColumn(): string{
            return 'role_id';
        }
    }