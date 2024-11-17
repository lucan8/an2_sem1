<?php
    require_once "layout.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Appointments</title>
</head>
<body>
    <form>
        <div id="county_div">
            <input type="text" placeholder="County" id="county_input">
        </div>
        <div id="spec_div">
            <input type="text" placeholder="Specialization" id="specialization_input">
        </div>
        <!-- <select required id="doctor_select">
            <option value="Doctor" selected>Doctor</option>
            <?php foreach ($doctors as $doctor) : ?>
                <option value=<?= $doctor["name"] ?>><?= $doctor["name"] ?>: <?= $doctor["years_exp"] ?> years experience</option>
            <?php endforeach; ?>
        </select> -->
        <input type="date" required id="input_date">
        <input type="submit" value="Make Appointment" id="submit_button">
    </form>
</body>
<script>
    let county_div = document.getElementById("county_div");
    let county_input = document.getElementById("county_input");

    let counties_data = <?= $counties ?>;
    let counties = counties_data.map(county => county.county_name);

    let spec_div = document.getElementById("spec_div");
    let specialization_input = document.getElementById("specialization_input");

    let specializations_data = <?= $specializations ?>;
    let specializations = specializations_data.map(spec => spec.specialization_name);

    let doctor_select = document.getElementById("doctor_select");
    let submit_input = document.getElementById("submit_button");
    
    county_input.addEventListener("input", function(event){
        autoComplete(county_div, event.target, counties);
    });

    specialization_input.addEventListener("input", function(event){
        autoComplete(spec_div, event.target, specializations);
    });

    //Create divs for each option that starts with the input value
    function autoComplete(option_div, input_elem, data_list){
        removeOptions(option_div);

        let input_data = input_elem.value;
        if (input_data === "") return;

        // Keep only the data that start with the input value
        let filtered_data = data_list.filter(data => data.toLowerCase().startsWith(input_data.toLowerCase()));
        // Create div for each option and append it to specialzied div
        filtered_data.forEach(data => addOption(option_div, input_elem, data));
    }

    //Create an option div and append it to the options div
    //On click, added div sets the input value to the option value and removes all other options
    function addOption(option_div, input_elem, option_data){
        let option = document.createElement("div");
        let option_text = document.createTextNode(option_data);

        option.appendChild(option_text);
        option_div.appendChild(option);

        option.addEventListener("click", function(){
            input_elem.value = option_data;
            removeOptions(option_div);
        });
    }

    //Remove all options from options_div except the first one
    function removeOptions(options_div){
        while(options_div.childElementCount > 1){
            options_div.removeChild(options_div.lastChild);
        }
    }  
</script>
</html>
