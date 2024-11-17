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
            <input type="text" placeholder="Specialization" id="specialization_input" disabled>
        </div>
        <select id="medic_select" disabled>
            <option selected>Select medic</option>
        </select>
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
    let spec_input = document.getElementById("specialization_input");

    let spec_data = <?= $specializations ?>;
    //let specializations = specializations_data.map(spec => spec.specialization_name);
    let specializations = spec_data;

    let medic_select = document.getElementById("medic_select");
    let submit_input = document.getElementById("submit_button");
    
    county_input.addEventListener("input", function(event){
        // If inputed county is valid, we enable the specialization input
        if (counties.includes(event.target.value))
            spec_input.disabled = false;
        else{
            spec_input.value = "";
            removeOptions(spec_div);
            spec_input.disabled = true;
        }

        makeSuggestions(county_div, event.target, counties, () => spec_input.disabled = false);
    });

    spec_input.addEventListener("input", function(event){
        input_specialization = event.target.value;

        if (specializations.includes(input_specialization))
            fillMedicsSelect();
        else{
            removeOptions(medic_select);
            medic_select.disabled = true;
        }

        makeSuggestions(spec_div, event.target, specializations, fillMedicsSelect);
    });

    //Create divs for each option that starts with the input value
    function makeSuggestions(option_div, input_elem, data_list, sugg_onclick_additional_func){
        removeOptions(option_div);

        let input_data = input_elem.value;
        if (input_data == "") return;

        // Keep only the data that start with the input value
        let filtered_data = data_list.filter(data => data.toLowerCase().startsWith(input_data.toLowerCase()));
        // Create div for each option and append it to specialzied div
        filtered_data.forEach(data => addSuggestion(option_div, input_elem, data, sugg_onclick_additional_func));
    }

    //Create an option div and append it to the options div
    //On click, added div sets the input value to the option value, removes all other options and
    //calls the additional function if it is not NULL
    function addSuggestion(option_div, input_elem, option_data, sugg_onclick_additional_func){
        let option = document.createElement("div");
        let option_text = document.createTextNode(option_data);

        option.appendChild(option_text);
        option_div.appendChild(option);

        option.addEventListener("click", function(){
            input_elem.value = option_data;
            removeOptions(option_div);
            if (sugg_onclick_additional_func != null)
                sugg_onclick_additional_func();
        });
    }

    //Remove all options from options_div except the first one
    function removeOptions(options_container){
        while(options_container.childElementCount > 1){
            options_container.removeChild(options_container.lastChild);
        }
    }

    //Add medic options to the medic select element, value is medic id, text is medic name
    function addMedicOption(medic){
        let option = document.createElement("option");
        option.value = medic.medic_id;
        option.text = medic.medic_name;
        medic_select.appendChild(option);
    }

    //Fetches the medics with the inputed county and specialization
    // and adds them to the medic select element
    function fillMedicsSelect(){
        fetch('getMedics?county=' + county_input.value + '&specialization=' + spec_input.value)
                .then(response => {    
                    response.json().then(medics => medics.forEach(medic => addMedicOption(medic)));
                    medic_select.disabled = false;
                });
    }
</script>
</html>
