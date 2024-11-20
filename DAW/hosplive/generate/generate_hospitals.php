<?php
    require_once "../exceptions/FileOpenException.php";
    require_once "../exceptions/FileWriteException.php";
    require_once "constants.php";
    
    generateHospitals($constants["NR_COUNTIES"], $constants["NR_ROOMS_HOSPITAL"]);

    function generateHospitals($nr_hospitals, $nr_rooms){
        //Opening hospitals file
        $HOSPITALS_PATH = "../resources/hospitals.txt";
        $hospitals_out = fopen($HOSPITALS_PATH, "w");
        if (!$hospitals_out)
            throw new FileOpenException($HOSPITALS_PATH);

        //Opening hospital rooms file
        $ROOMS_PATH = "../resources/rooms.txt";
        $rooms_out = fopen($ROOMS_PATH, "w");
        if (!$rooms_out)
            throw new FileOpenException($ROOMS_PATH);

        //Setting the headers of the files
        if (!fwrite($hospitals_out, "hospital_id county_id phone_number\n"))
            throw new FileWriteException($HOSPITALS_PATH);

        if (!fwrite($rooms_out, "room_id hospital_id\n"))
            throw new FileWriteException($ROOMS_PATH);
        
        //Setting the array from which the counties id's will be randomly chosen
        $counties_id = range(1, $nr_hospitals);
        for ($hospital_id = 1; $hospital_id < $nr_hospitals; $hospital_id++){  
            generateWriteHospital($hospitals_out, $counties_id, $hospital_id);
            generateWriteRooms($rooms_out, $nr_rooms, $hospital_id);
        }

        //Last hospital
        generateWriteHospital($hospitals_out, $counties_id, $hospital_id, true);
        generateWriteRooms($rooms_out, $nr_rooms, $hospital_id, true);

        
        fclose($hospitals_out);
        fclose($rooms_out);
    }

    //Function to generate and write the hospital info to $hospitals_out
    function generateWriteHospital($hospitals_out, array &$counties_id, $hospital_id, $last = false){
        global $constants;
        $hospital_phone = $constants["FAKER_GEN"]->phoneNumber;
        $hospital_county_id = getRandomCountyId($counties_id);
        
        //If this is the last hospital, don't add a backslash
        $backslash = $last ? "" : "\n";
        //Setting the hospital info
        if (!fwrite($hospitals_out,  $hospital_id . ", " .
                                     $hospital_county_id . ", " .
                                     $hospital_phone . $backslash))
            throw new FileWriteException(stream_get_meta_data($hospitals_out)["uri"]);
    }

    function generateWriteRooms($rooms_out, $nr_rooms, $hospital_id, $last = false){
        for ($room_id = 1; $room_id < $nr_rooms; $room_id++)
            if (!fwrite($rooms_out, $room_id  . ", " .
                                    $hospital_id . "\n"))
                throw new FileWriteException(stream_get_meta_data($rooms_out)["uri"]);
        
        //If this is the last hospital, don't add a backslash
        $backslash = $last ? "" : "\n";
        if (!fwrite($rooms_out, $room_id  . ", " .
                                $hospital_id . $backslash))
            throw new FileWriteException(stream_get_meta_data($rooms_out)["uri"]);
    }

    //Function to get a random county_id from the list and remove it from it
    function getRandomCountyId(&$counties_id){
        $index = array_rand($counties_id);
        $county_id = $counties_id[$index];
        array_splice($counties_id, $index, 1);

        return $county_id;
    }

    
?>