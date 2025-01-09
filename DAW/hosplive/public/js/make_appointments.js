import * as utils_app from './utils_appointments.js';
addEventListener("DOMContentLoaded", async (event) => {
    const app_constants = await utils_app.getConstants();

    let county_sugg = document.getElementById("county_list");

    let county_input = document.getElementById("county_input");
    let chosen_hospital_in = document.getElementById("hospital_id");

    let spec_sugg = document.getElementById("spec_list");
    let spec_input = document.getElementById("specialization_input");

    let medic_select = document.getElementById("medic_select");
    let select_medic_opt = document.getElementById("select_medic");
    let chosen_medic_in = document.getElementById("medic_id");

    let date_input = document.getElementById("date_input");
    date_input.min = utils_app.toDateInputValue(new Date());
    let chosen_date_in = document.getElementById("appointment_date");

    let time_select = document.getElementById("time_select");
    //let time_list = document.getElementById("time_list");
    let chosen_time_in = document.getElementById("appointment_time");
    let select_time_opt = document.getElementById("select_time");


    let fill_form_btn = document.getElementById("fill_form_btn");
    let app_form = document.getElementById("appointment_form");
    let chosen_room_in = document.getElementById("room_id");
    let chosen_duration_in = document.getElementById("duration");

    let csrf_token = document.getElementById("csrf_token_make_app");
    let recaptcha_input = document.getElementById("recaptcha_input");

    county_input.addEventListener("input", function(event){
        let input_county = event.target.value;
        //Searching for input_county in the options
        let associated_opt = Array.from(county_sugg.children).find((option) => {return option.textContent.toLowerCase() == input_county.toLowerCase()});
        if (associated_opt){
            event.target.value = associated_opt.textContent;
            spec_input.disabled = false;
        }
        else
            resetSpec();
        makeSuggestions(county_sugg, event.target);
    });

    spec_input.addEventListener("input", function(event){
        let input_spec = event.target.value;
        //Searching for input_county in the options
        let associated_opt = Array.from(spec_sugg.children).find((option) => {return option.textContent.toLowerCase() == input_spec.toLowerCase()});
        if (associated_opt){
            event.target.value = associated_opt.textContent;
            fillMedicsSelect();
        }
        else
            resetMedics();

        makeSuggestions(spec_sugg, event.target);
    });

    medic_select.addEventListener("change", function(event){
        resetDate();
        date_input.disabled = false;
        chosen_medic_in.value = event.target.value;
    });

    date_input.addEventListener("change", async function(event){
        if (event.target.value){
            //Filling the time list with all possible times for appointments(if needed)
            utils_app.fillTimeOptions(date_input.min, time_select, app_constants.OPENING_TIME,
                                      app_constants.CLOSING_TIME, app_constants.DEFAULT_DURATION);
            //Getting the unavailable times for the chosen medic, hospital and date
            let unavailable_times = await utils_app.getUnavailableTimes(chosen_medic_in.value, chosen_hospital_in.value, event.target.value);
            //Getting the valid time options
            let time_options = Array.from(time_select.children).filter((option) => option.id != "select_time");

            //Enabling the times that are free
            utils_app.enableFreeTimeIntervals(unavailable_times, time_options);
            time_select.disabled = false;
        }
        else
            resetTime();
    });

    //Gets the free room for the chosen hospital, date and time
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

    //Filling the form with the remaining data and sending it to the server
    fill_form_btn.addEventListener("click", function(){
        //Making sure recaptcha library is loaded
        grecaptcha.ready(() => {
            //Getting the user activity representive token and sending the form
            grecaptcha.execute(recaptcha_input.getAttribute("site_key"), { action: 'make_appointment' }).then((token) => {
                recaptcha_input.value = token;
                sendAppointmentsForm(app_form);
            });
        });
    });

    //Fills hidden appointments form and sends it to the server with fetch
    function sendAppointmentsForm(app_form){
        fillForm();
        let data = new FormData(app_form);

        fetch("make_appointment", {
            method: "POST",
            body: data
        }).then(response => response.json().then(response => {
            if (response.ok){
                alert("Appointment made successfully");
                csrf_token.value = response.csrf_token;
                resetCounties();
            }
            else{
                alert("Failed to make appointment");
                console.log(response['error'])
            }
        }));
    }

    //Enable all options from sugg_list that start with input_elem's value  
    function makeSuggestions(sugg_list, input_elem){
        let input_data = input_elem.value;
        if (input_data == "") return;

        //Enabling options that start with the input value, disabling the rest
        Array.from(sugg_list.children).forEach((option, index, arr) => {
            arr[index].disabled = !option.textContent.toLowerCase().startsWith(input_data.toLowerCase());
        });
    }

    //Fetches the medics with the inputed county and specialization
    // and adds them to the medic select element
    // Also sets the chosen hospital id
    function fillMedicsSelect(){
        fetch('/hosplive/get_medics?county_id=' + document.getElementById(county_input.value).getAttribute('name') +
                '&spec_id=' + document.getElementById(spec_input.value).getAttribute('name'))
                .then(response => {
                    response.json().then(res => {
                        if (!res['ok']){
                            alert("Failed to get medics(interal error)");
                            console.log(res['error']);
                            return;
                        }
                        chosen_hospital_in.value = res['data']['chosen_hospital']
                        //console.log(res['data']['medics']);
                        res['data']['medics'].forEach(
                            medic => { let medic_info = medic.medic_name + ": " + medic.years_exp + " years experience";
                                       utils_app.addOption(medic_select, medic.medic_id, medic_info)});
                                                    
                    });
                });
        medic_select.disabled = false;
    }

    //Fills the hidden form with the inputed data
    function fillForm(){
        chosen_medic_in.value = medic_select.value;
        chosen_date_in.value = date_input.value;
        chosen_time_in.value = time_select.value;
        //For now the duration is fixed
        chosen_duration_in.value = app_constants.DEFAULT_DURATION;
    }

    //Used moslty for emptying the suggestions and options and disabling the associated input
    function resetInput(input, input_sugg = null){
        if (input.disabled) return;
        utils_app.removeOptions(input_sugg);
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