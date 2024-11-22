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
    <div>
        <div id="county_div">
            <input type="text" placeholder="County" id="county_input" list="county_list">
            <datalist id="county_list">
                <option disabled>Select County</option>
            </datalist>
        </div>

        <div id="spec_div">
            <input type="text" placeholder="Specialization" id="specialization_input" list="spec_list" disabled>
            <datalist id="spec_list">
                <option disabled>Select Specialization</option>
            </datalist>
        </div>

        <select id="medic_select" disabled>
            <option selected>Select medic</option>
        </select>

        <input type="date" required id="date_input" min="2018-01-01">

        <div>
            <input type="time" required id="time_input" list="time_list">
            <datalist id="time_list">
                <option value="08:00"></option>
            </datalist>
        </div>
        <input type="submit" value="Make Appointment" id="fill_form_btn">
    </div>

    <!-- Form with actual data -->
    <form action="makeAppointment" method="post" id="appointment_form" hidden>
        <input type="number" id="hospital_id" name="county_id">
        <input type="number" id="spec_id" name="spec_id">
        <input type="number" id="medic_id" name="medic_id">
        <input type="date" id="appointment_date" name="appointment_date">
        <input type="time" id="appointment_time" name="appointment_time">
    </form>
</body>
<script>
    //TO DO: Make date dependent on medic and time dependent on date
    let county_sugg = document.getElementById("county_list");
    let county_input = document.getElementById("county_input");
    let hospitals_data = <?= $hospitals ?>;

    let spec_sugg = document.getElementById("spec_list");
    let spec_input = document.getElementById("specialization_input");
    let spec_data = <?= $specializations ?>;

    let medic_select = document.getElementById("medic_select");

    let date_input = document.getElementById("date_input");
    date_input.min = toDateInputValue(new Date());

    let time_input = document.getElementById("time_input");
    let fill_form_btn = document.getElementById("fill_form_btn");
    
    
    county_input.addEventListener("input", function(event){
        let input_county = event.target.value;
        if (hospitals_data.hasOwnProperty(input_county)){
            spec_input.disabled = false;
        }
        else{
            resetMedics();
            resetSpecializations();
        }

        makeSuggestions(county_sugg, event.target, hospitals_data, () => spec_input.disabled = false);
    });

    spec_input.addEventListener("input", function(event){
        input_specialization = event.target.value;

        if (spec_data.hasOwnProperty(input_specialization))
            fillMedicsSelect();
        else
            resetMedics();

        makeSuggestions(spec_sugg, event.target, spec_data, fillMedicsSelect);
    });

    fill_form_btn.addEventListener("click", function(){
        let form = document.getElementById("appointment_form");

        fillForm();

        form.submit();
    });

    //Create divs for each option that starts with the input value
    function makeSuggestions(sugg_list, input_elem, data_list, sugg_onclick_additional_func){
        removeOptions(sugg_list);

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
    function addSuggestion(sugg_container, input_elem, option_value, option_text, sugg_onclick_additional_func){
        let option = addOption(sugg_container, option_value, option_text);

        option.addEventListener("click", function(){
            input_elem.value = option_data;
            removeOptions(suggestion_container);
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

    //Adds an option to option cont with the given value and text and returns the created option
    function addOption(option_cont, option_value, option_text){
        let option = document.createElement("option");
        option.value = option_value;
        option.text = option_text;
        option_cont.appendChild(option);

        return option;
    }

    //Fetches the medics with the inputed county and specialization
    // and adds them to the medic select element
    function fillMedicsSelect(){
        fetch('getMedics?hospital_id=' + hospitals_data[county_input.value] + '&specialization=' + spec_input.value)
                .then(response => {    
                    response.json().then(medics => medics.forEach(medic => addOption(medic_select, 
                                                                                     medic.medic_id,
                                                                                     medic.medic_name)));
                    medic_select.disabled = false;
                });
    }

    //Fills the hidden form with the inputed data
    function fillForm(){
        document.getElementById("county_id").value = counties_data.find(county => county.county_name == county_input.value).county_id;
        document.getElementById("spec_id").value = spec_data.find(spec => spec.specialization_name == spec_input.value).specialization_id;

        document.getElementById("medic_id").value = medic_select.value;

        document.getElementById("appointment_date").value = date_input.value;
        document.getElementById("appointment_time").value = time_input.value;
    }

    function toDateInputValue(dateObject){
        let local = new Date(dateObject);
        local.setMinutes(dateObject.getMinutes() - dateObject.getTimezoneOffset());
        return local.toJSON().slice(0,10);
    }

    function resetSpecializations(){
        if (spec_input.disabled) return;
        removeOptions(spec_sugg);
        spec_input.value = "";
        spec_input.disabled = true;
    }

    function resetMedics(){
        if (medic_select.disabled) return;
        removeOptions(medic_select);
        medic_select.disabled = true;
    }
</script>
</html>
