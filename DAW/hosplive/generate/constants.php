<?php
    require_once "utils/utils.php";
    require_once "deps/Faker-master/src/autoload.php";
    $constants = [
        "NR_ROOMS_HOSPITAL" => 30,
        "MAX_NR_YEARS_EXP" => 20,
        "NR_HOSPITALS_PER_MEDIC" => 2,
        "NR_HOSPITALS" => getNrLines("../resources/hospitals.txt") - 1,
        "NR_SPECIALIZATIONS" => getNrLines("../resources/specializations.txt") - 1,
        "NR_COUNTIES" => getNrLines("../resources/counties.txt"),
        "FAKER_GEN" => Faker\Factory::create("ro_RO"),
        "INSERT_TABLES" => [
                            "rooms", "counties", "medics",
                            "hospitals", "hospitals_medics", "specializations"
                            ]
    ]
?>