addEventListener("DOMContentLoaded", event => {
    setInitialData();
    let op_time_opt = document.getElementById("opening_time_appointments");
    let cl_time_opt = document.getElementById("closing_time_appointments");
    let app_duration = document.getElementById("app_duration_appointments");

    Array.from(document.getElementsByClassName("cancel_app")).forEach(btn => {
        btn.addEventListener("click", event => {
            let appointment_id = event.target.parentNode.id;
            let data = new FormData();
            data.append("appointment_id", appointment_id);

            //Sending a post request to cancel the appointment
            fetch("cancel_appointment", {
                method: "POST",
                body: data
            }).then(response => response.json().then(resp => {
                    if (resp.ok){
                        event.target.parentNode.remove();
                    }
                    else{
                        alert("Error cancelling appointment");
                        console.log(resp.error);
                    }
            })
            );
        });
    });

    Array.from(document.getElementsByClassName("edit_app")).forEach(btn => {
        btn.addEventListener("click", event => {
            let appointment_id = event.target.parentNode.id;

            let date_elem = document.getElementById("date_" + appointment_id);
            let time_elem = document.getElementById("time_" + appointment_id);

            let data = new FormData();
            data.append("appointment_id", appointment_id);
            data.append("appointment_date", date_elem.value);
            data.append("appointment_time", time_elem.value);

            //Sending a post request to cancel the appointment
            fetch("edit_appointment", {
                method: "POST",
                body: data
            }).then(response => response.json().then(resp => {
                    if (!resp.ok){
                        date_elem.value = date_elem.initial_date;
                        time_elem.value = time_elem.initial_time;

                        alert("Error editing appointment");
                        console.log(resp.error);
                    }
                    else{
                        alert("Appointment edited successfully");
                        date_elem.initial_date = date_elem.value;
                        time_elem.initial_time = time_elem.value;
                    }
            })
            );
        });
    });


    //Sets initial date and time for each appointment
    //Adds event listeners to the time inputs to fill the time options or enable the valid ones
    function setInitialData(){
        Array.from(document.getElementsByClassName("app_date")).forEach(elem => {
            elem.min = toDateInputValue(new Date());
            elem.initial_date = elem.value;

            elem.addEventListener("change", event => {
                //Should not use .parentNode.parentNode, but it works for now
                let app_id = event.target.parentNode.parentNode.id;
                let time_select = document.getElementById("time_" + app_id);
                let medic_id = document.getElementById("medic_id_" + app_id).value;
                let hospital_id = document.getElementById("hospital_id_" + app_id).value;
                let date = event.target.value;

                //Filling time options if needed
                fillTimeOptions(date, time_select);
                time_select.options[time_select.selectedIndex].selected = false;

                //Changing the time options based on new date
                getSetTimeOptions(time_select, medic_id, hospital_id, date);
            })
        });

        Array.from(document.getElementsByClassName("app_time")).forEach(elem => {
            elem.initial_time = elem.value;
            // Adding one time event listener for filling and enabling time options
            elem.addEventListener("click", event => {
                //Should not use .parentNode.parentNode, but it works for now
                let app_id = event.target.parentNode.parentNode.id;
                let date_elem = document.getElementById("date_" + app_id);
                let medic_id = document.getElementById("medic_id_" + app_id).value;
                let hospital_id = document.getElementById("hospital_id_" + app_id).value;

                // Keeping track whether the time options was filled or not
                let filled = fillTimeOptions(date_elem.value, event.target);

                //Only update time if date changed or the time options were filled
                if (filled)
                    getSetTimeOptions(event.target, medic_id, hospital_id, date_elem.value);
            }, {once: true});
        });
    }
    
    //Gets the unavailable times for the medic and hospital on the specified date
    //Disables the options that are in the unavailable times array
    //Selects the first time option that is enabled
    function getSetTimeOptions(time_select, medic_id, hospital_id, date){
        fetch('getUnavailableTimes?hospital_id=' + hospital_id +
                '&medic_id=' + medic_id +
                '&appointment_date=' + date)
                .then(response => {
                    response.json().then(res => {
                        if (!res['ok']){
                            alert("Failed to get available times(interal error)");
                            console.log(res['error']);
                            return;
                        }
                        enableFreeTimeIntervals(res['data']['times'], time_select);
                        time_select.disabled = false;
                        selectFirstEnabledOption(time_select)
                    });
                })
    }

    //Disable the options that are in the unavailable times array, enable the rest
    //Both arrays are sorted
    function enableFreeTimeIntervals(unavailable_times, time_options){
        console.log('enable')
        let u_time_index = 0;
        let t_options_index = 0;
        //console.log(unavailable_times);

        // Iterate through all options, if curr option is equal to unavailable time
        // We disable the option and go to the next unavailable time
        // Otherwise the option gets enabled because it's free
        while (u_time_index < unavailable_times.length){
            let u_time = unavailable_times[u_time_index];
            let time_option = time_options.children[t_options_index];
            let time_value = time_option.value;

            //console.log(u_time, time_value);

            if (u_time == time_value){
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

        //Not needed in the appoitnments version
        // select_time_opt.disabled = true;
        // select_time_opt.selected = true;
    }

    //Adds all possible appointments times between the min and max time of the time input with specified step
    function fillTimeOptions(date, time_options){
        //If there are already options, don't add more
        if (time_options.childElementCount > 1) return false;

        //Making a copy of the select_time option value and removing it
        let selected_time_opt = time_options.options[time_options.selectedIndex];
        let selected_time_val = new Date(date + " " + selected_time_opt.value);
        selected_time_opt.remove();
        
        //Getting the opening and closing time of the hospital
        //TODO MOVE op_time and closing_time into header constants
        let start = new Date(date + " " + op_time_opt.value);
        let end = new Date(date + " " + cl_time_opt.value);

        //In minutes
        //TO DO: Make this a parameter
        let step = parseInt(app_duration.value);
    
        while (start.getTime() < end.getTime()){
            let time_string = getHoursAndMinutes(start);
            let opt = addOption(time_options, time_string, time_string);
            //Re-select the previously selected time
            if (start.getTime() == selected_time_val.getTime())
                opt.selected = true;

            start.setMinutes(start.getMinutes() + step);
        }
        return true;
    }

    //Adds an option to option cont with the given value and text and returns the created option
    function addOption(option_cont, option_value, option_text){
        let option = document.createElement("option");
        option.value = option_value;
        option.text = option_text;
        option_cont.appendChild(option);

        return option;
    }

    function selectFirstEnabledOption(options){
        for (opt of options.children)
            if (!opt.disabled){
                console.log(opt);
                opt.selected = true;
                return;
            }
    }

    //Returns the time as string in HH:MM format
    function getHoursAndMinutes(date_time){
        return ("0" + date_time.getHours()).slice(-2) + ":" + ("0" + date_time.getMinutes()).slice(-2)
    }

    function toDateInputValue(dateObject){
        let local = new Date(dateObject);
        local.setMinutes(dateObject.getMinutes() - dateObject.getTimezoneOffset());
        return local.toJSON().slice(0,10);
    }
});