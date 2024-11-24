<?php
class Controller {
    public static function index() {
        require_once "app/views/index.php";
    }

    public static function makeAppointment() {
        if ($_SERVER["REQUEST_METHOD"] == "GET"){
            require_once "app/models/Counties.php";
            //$hospitals = json_encode(Hospitals::getHospitalsAndCounties());
            $counties = json_encode(Counties::getAll());
            require_once "app/models/Specializations.php";
            //$specializations = json_encode(Specializations::getAll());
            $specializations = json_encode(Specializations::getAll());
            require_once "app/models/Hospitals.php";
            require_once "app/models/Appointments.php";
            require_once "app/views/make_appointment.php";

        }
        // else if ($_SERVER["REQUEST_METHOD"] == "POST"){
        //     require_once "app/models/Appointments.php";
        //     require_once "app/models/Hospital.php";

        //     $hospital_id = Hospitals::getByCounty($_POST["county_id"])->id;
        //     new AppointmentsData(null, $hospital_id, $_POST["medic_id"], $_POST["room_id"],
        //                          $_POST["appointment_date"], $_POST["appointment_time"], null);
        //     Appointments::insert($data);
        // }
    }

    //TO DO: Get medics from chosen hospital with a given specialization passed to _GET
    public static function getMedics() {
        require_once "app/models/Hospitals.php";
        $chosen_hospital = (Hospitals::getByCounty((int)$_GET["county_id"]))->hospital_id;
        
        require_once "app/models/Medics.php";
        $medics = Medics::getByHospAndSpec($chosen_hospital, (int)$_GET["spec_id"]);
        $res = ["medics" => $medics, "chosen_hospital" => $chosen_hospital];
        echo json_encode($res);
    }

    public static function getFreeTimeIntervals() {
        require_once "app/models/Appointments.php";
        $time = json_encode(Appointments::getFreeTimeIntervals((int)$_GET["hospital_id"], (int)$_GET["medic_id"], $_GET["appointment_date"]));
        echo $time;
    }

    public static function getFreeRoom() {
        require_once "app/models/Appointments.php";
        $room = json_encode(Appointments::getFreeRoom((int)$_GET["hospital_id"], $_GET["appointment_date"], $_GET["appointment_time"]));
        echo $room;
    }
}
?>