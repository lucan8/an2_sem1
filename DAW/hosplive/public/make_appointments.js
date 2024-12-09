addEventListener("DOMContentLoaded", (event) => {
    let county_sugg = document.getElementById("county_list");

    let county_input = document.getElementById("county_input");
    let chosen_hospital_in = document.getElementById("hospital_id");

    let spec_sugg = document.getElementById("spec_list");
    let spec_input = document.getElementById("specialization_input");

    let medic_select = document.getElementById("medic_select");
    let select_medic_opt = document.getElementById("select_medic");
    let chosen_medic_in = document.getElementById("medic_id");

    let date_input = document.getElementById("date_input");
    date_input.min = toDateInputValue(new Date());
    let chosen_date_in = document.getElementById("appointment_date");

    let time_select = document.getElementById("time_select");
    //let time_list = document.getElementById("time_list");
    let chosen_time_in = document.getElementById("appointment_time");
    let op_time_opt = document.getElementById("opening_time_make_appointments");
    let cl_time_opt = document.getElementById("closing_time_make_appointments");
    let app_duration = document.getElementById("app_duration_make_appointments");
    let select_time_opt = document.getElementById("select_time");

    //Filling the time list with all possible times for appointments
    fillTimeOptions(date_input.min, time_select);

    let fill_form_btn = document.getElementById("fill_form_btn");
    let app_form = document.getElementById("appointment_form");
    let chosen_room_in = document.getElementById("room_id");

    county_input.addEventListener("input", function(event){
        let input_county = event.target.value;
        if (isOption(input_county, county_sugg)){
            spec_input.disabled = false;
        }
        else
            resetSpec();
        makeSuggestions(county_sugg, event.target, () => spec_input.disabled = false);
    });

    spec_input.addEventListener("input", function(event){
        input_specialization = event.target.value;

        if (isOption(input_specialization, spec_sugg))
            fillMedicsSelect();
        else
            resetMedics();

        makeSuggestions(spec_sugg, event.target, fillMedicsSelect);
    });

    medic_select.addEventListener("change", function(event){
        resetDate();
        date_input.disabled = false;
        chosen_medic_in.value = event.target.value;
    });

    date_input.addEventListener("change", function(event){
        if (event.target.value)
            getSetTimeOptions();
    });

    time_select.addEventListener("change", function(event){
        fetch("getFreeRoom?hospital_id=" + chosen_hospital_in.value +
                "&appointment_date=" + date_input.value + 
                "&appointment_time=" + event.target.value)
            .then(response => {
                response.json().then(res => {
                    if (!res['ok']){
                        alert("Failed to get free room(interal error)");
                        console.log(res['error']);
                        return;
                    }
                    if (!res['data']['room']) {
                        alert("No free rooms available at the chosen hospital at the selected date");
                        chosen_room_in.value = "";
                    }
                    else
                        chosen_room_in.value = res['data']['room'].room_id;
                });
            });
    });

    fill_form_btn.addEventListener("click", function(){
        fillForm();
        data = new FormData(app_form);

        fetch("make_appointment", {
            method: "POST",
            body: data
        }).then(response => response.json().then(response => {
            if (response['ok']){
                alert("Appointment made successfully");
                resetCounties();
            }
            else{
                alert("Failed to make appointment(interal error)");
                console.log(response['error'])
            }
        }));
    });

    //Create divs for each option that starts with the input value
    function makeSuggestions(sugg_list, input_elem){
        let input_data = input_elem.value;
        if (input_data == "") return;

        //Enabling options that start with the input value, disabling the rest
        Array.from(sugg_list.children).forEach((option, index, arr) => {
            arr[index].disabled = !option.textContent.toLowerCase().startsWith(input_data);
        });
    }


    //Remove all options from options_div except the first one
    function removeOptions(options_container){
        if (!options_container) return;
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

    function isOption(input, option_cont){
        if (input == "") return false;

        return Array.from(option_cont.children).find((option) => {return option.value == input}) !== undefined;
    }


    //Fetches the medics with the inputed county and specialization
    // and adds them to the medic select element
    // Also sets the chosen hospital id
    function fillMedicsSelect(){
        fetch('getMedics?county_id=' + document.getElementsByName(county_input.value)[0].id +
                '&spec_id=' + document.getElementsByName(spec_input.value)[0].id)
                .then(response => {
                    response.json().then(res => {
                        if (!res['ok']){
                            alert("Failed to get medics(interal error)");
                            console.log(res['error']);
                            return;
                        }
                        chosen_hospital_in.value = res['data']['chosen_hospital']
                        res['data']['medics'].forEach(
                            medic => { let medic_info = medic.medic_name + ": " + medic.years_exp + " years experience";
                                       addOption(medic_select, medic.medic_id, medic_info)});
                                                    
                    });
                });
        medic_select.disabled = false;
    }

    //Fetches unavailable times for the chosen medic and date, and enables times different from those
    function getSetTimeOptions(){
        fetch('getUnavailableTimes?hospital_id=' + chosen_hospital_in.value +
                '&medic_id=' + medic_select.value +
                '&appointment_date=' + date_input.value)
                .then(response => {
                    response.json().then(res => {
                        if (!res['ok']){
                            alert("Failed to get available times(interal error)");
                            console.log(res['error']);
                            return;
                        }
                        enableFreeTimeIntervals(res['data']['times'], time_select);
                        time_select.disabled = false;
                    });
                });
    }

    //Disable the options that are in the unavailable times array, enable the rest
    //Both arrays are sorted
    function enableFreeTimeIntervals(unavailable_times, time_options){
        let u_time_index = 0;
        let t_options_index = 0;

        console.log(unavailable_times);

        // Iterate through all options, if curr option is equal to unavailable time
        // We disable the option and go to the next unavailable time
        // Otherwise the option gets enabled because it's free
        while (u_time_index < unavailable_times.length){
            let u_time = unavailable_times[u_time_index];
            let time_option = time_options.children[t_options_index];
            let time_value = time_option.value;

            console.log(u_time, time_value);

            //Skipping past "select time" option
            if (time_option.id == "select_time")
                t_options_index++;
            else if (u_time == time_value){
                    time_option.disabled = true;
                    time_option.hidden = true;
                    u_time_index++;
                    t_options_index++;
                }
                else if (u_time > time_value){
                    time_option.disabled = false;
                    time_option.hidden = false;
                    t_options_index++;
                }
                else
                    u_time_index++;

        }

        // Time options include unvailable times so we need to enable the rest
        while (t_options_index < time_options.childElementCount){
            time_options.children[t_options_index].disabled = false;
            time_options.children[t_options_index].hidden = false;
            t_options_index++;
        }
        select_time_opt.disabled = true;
        select_time_opt.selected = true;
    }

    //Adds all possible appointments times between the min and max time of the time input with specified step
    function fillTimeOptions(date, time_options){
        console.log(time_options.childElementCount);
        //If there are already options, don't add more
        if (time_options.childElementCount > 1) return;
       
        let start = new Date(date + " " + op_time_opt.value);
        let end = new Date(date + " " + cl_time_opt.value);

        //In minutes
        //TO DO: Make this a parameter
        let step = parseInt(app_duration.value);
    
        while (start.getTime() < end.getTime()){
            let time_string = getHoursAndMinutes(start);
            addOption(time_options, time_string, time_string);

            start.setMinutes(start.getMinutes() + step);
        }

    }

    //Fills the hidden form with the inputed data
    function fillForm(){
        chosen_medic_in.value = medic_select.value;
        chosen_date_in.value = date_input.value;
        chosen_time_in.value = time_select.value;
    }

    function toDateInputValue(dateObject){
        let local = new Date(dateObject);
        local.setMinutes(dateObject.getMinutes() - dateObject.getTimezoneOffset());
        return local.toJSON().slice(0,10);
    }

    //Returns the time as string in HH:MM format
    function getHoursAndMinutes(date_time){
        return ("0" + date_time.getHours()).slice(-2) + ":" + ("0" + date_time.getMinutes()).slice(-2)
    }

    //Used moslty for emptying the suggestions and options and disabling the associated input
    function resetInput(input, input_sugg = null){
        if (input.disabled) return;
        removeOptions(input_sugg);
        input.value = "";
        input.disabled = true;
    }

    function resetCounties(){
        county_input.value = "";
        resetSpec();
    }

    function resetSpec(){
        resetInput(spec_input);
        resetMedics();
        chosen_hospital_in.value = "";
    }

    function resetMedics(){
        resetInput(medic_select, medic_select);
        resetDate();
        select_medic_opt.selected = true;
        chosen_medic_in.value = "";

    }

    function resetDate(){
        resetInput(date_input);
        resetTime();
        chosen_date_in.value = "";
    }

    function resetTime(){
        resetInput(time_select);
        select_time_opt.selected = true;
        chosen_room_in.value = "";
        chosen_time_in.value = "";
    }
});