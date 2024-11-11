<?php
    require_once "../exceptions/FileOpenException.php";
    require_once "../exceptions/FileWriteException.php";
    require_once "../utils/parse.php";
    require_once "constants.php";

    $constants = $constants;

    generateWriteMedics($constants["NR_HOSPITALS"], $constants["NR_SPECIALIZATIONS"],
                        $constants["MAX_NR_YEARS_EXP"], $constants["NR_HOSPITALS_PER_MEDIC"]);
    
    //Generating and writing the medics and hospitals_medics
    //For each hospital, for each specialization, generate nr_hospitals_medics medics
    function generateWriteMedics($nr_hospitals, $nr_specializations, $max_years_of_experience,
                                 $nr_hospitals_medics){
        //Opening medic file and checking
        $MEDICS_PATH = "../resources/medics.txt";
        $medics_out = fopen($MEDICS_PATH, "w");
        if (!$medics_out)
            throw new FileOpenException($MEDICS_PATH);

        //Opening hospital file and checking
        $HOSPITALS_PATH = "../resources/hospitals_medics.txt";
        $hospitals_out = fopen($HOSPITALS_PATH, "w");
        if (!$hospitals_out)
            throw new FileOpenException($HOSPITALS_PATH);

        //Writing the header of the files and checking
        if (!fwrite($medics_out, "medic_id medic_name specialization_id years_exp\n"))
            throw new FileWriteException($MEDICS_PATH);
        if (!fwrite($hospitals_out, "hospital_id medic_id hire_date\n"))
            throw new FileWriteException($HOSPITALS_PATH);

        $medic_id = 0;
        $hospitals_ids = range(1, $nr_hospitals);
        for ($i = 1; $i <= $nr_hospitals; $i++)
            for ($specialization_id = 1; $specialization_id < $nr_specializations; $specialization_id++){
                    $medic_id ++;
                    generateWriteHospitalsMedic($hospitals_out, $hospitals_ids, $nr_hospitals_medics,
                                                $medic_id);
                    generateWriteMedic($medics_out, $specialization_id, $max_years_of_experience, $medic_id);
            }
        
        //last medic
        $medic_id ++;
        generateWriteHospitalsMedic($hospitals_out, $hospitals_ids, $nr_hospitals_medics, $medic_id, true);
        generateWriteMedic($medics_out, $specialization_id, $max_years_of_experience, $medic_id, true);
        
            
        fclose($medics_out);
        fclose($hospitals_out);
    }

    //Generates $nr_hospitals hospitals from $hospitals_ids, pairs them with $medic_id
    //and writes them to $hospitals_out
    function generateWriteHospitalsMedic($hospitals_out, array $hospitals_ids,
                                         $nr_hospitals, $medic_id, $last = false){
        $chosen_hospitals = chooseRandomElements($hospitals_ids, $nr_hospitals);
        $hire_date = date("y-m-d");

        //If this is the last medic, don't add a backslash
        $backslash = $last ? "" : "\n";

        //Setting the pair of hospitals_id and medics_id
        for ($i = 0; $i < $nr_hospitals - 1; $i++)
            if (!fwrite($hospitals_out, $chosen_hospitals[$i] . ", " .
                                        $medic_id . ", " .
                                        $hire_date . "\n"))
                throw new FileWriteException(stream_get_meta_data($hospitals_out)["uri"]);
        
        //If this is the last medic_hospital, don't add a backslash
        if (!fwrite($hospitals_out, $chosen_hospitals[$i] . ", " .
                                    $medic_id . ", " .
                                    $hire_date . $backslash))
            throw new FileWriteException(stream_get_meta_data($hospitals_out)["uri"]);
    }

    //Generates and writes to $medics_out information about a medic
    function generateWriteMedic($medics_out, $specialization , $max_years_exp, $medic_id, $last = false){
        global $constants;
        $years_of_exp = $constants["FAKER_GEN"]->numberBetween(1, $max_years_exp);
        $medic_name = $constants["FAKER_GEN"]->name;

        //If this is the last medic, don't add a backslash
        $backslash = $last ? "" : "\n";

        if (!fwrite($medics_out, $medic_id . ", " .
                                 $medic_name . ", " .
                                 $specialization . ", " .
                                 $years_of_exp . $backslash))
            throw new FileWriteException(stream_get_meta_data($medics_out)["uri"]);
    }

    function chooseRandomElements(array $array, $nr_elements){
        $chosen_indexes = array_rand($array, $nr_elements);
        if (!is_array($chosen_indexes))
            $chosen_indexes = [$chosen_indexes];

        $chosen_elements = [];
        foreach ($chosen_indexes as $index)
            array_push($chosen_elements, $array[$index]);

        return $chosen_elements;
    }
?>