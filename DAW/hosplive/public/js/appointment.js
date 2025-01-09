import * as utils_app from './utils_appointments.js';

addEventListener("DOMContentLoaded", async event => {
    setInitialData();
    const app_constants = await utils_app.getConstants();
    let csrf_token = document.getElementById("csrf_token_app");

    Array.from(document.getElementsByClassName("cancel_app")).forEach(btn => {
        btn.addEventListener("click", event => {
            //Getting the appointment id
            let appointment_id = event.target.parentNode.id;

            let data = new FormData();
            data.append("appointment_id", appointment_id);
            data.append(csrf_token.name, csrf_token.value);

            //Sending a post request to cancel the appointment
            fetch("cancel_appointment", {
                method: "POST",
                body: data
            }).then(response => response.json().then(resp => {
                    if (resp.ok){
                        csrf_token.value = resp.csrf_token;
                        event.target.parentNode.remove();
                        alert("Appointment cancelled successfully");
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
            //Getting the appointment id and the corresponding date and time elements
            let appointment_id = event.target.parentNode.id;

            let date_elem = document.getElementById("date_" + appointment_id);
            let time_elem = document.getElementById("time_" + appointment_id);

            //Adding the data to the formData object
            let data = new FormData();
            data.append("appointment_id", appointment_id);
            data.append("appointment_date", date_elem.value);
            data.append("appointment_time", time_elem.value);
            data.append(csrf_token.name, csrf_token.value);

            //Sending a post request to edit the appointment
            fetch("edit_appointment", {
                method: "POST",
                body: data
            }).then(response => response.json().then(resp => {
                    if (!resp.ok){
                        //If the appointment was not edited we reset the date and time to the initial values
                        date_elem.value = date_elem.initial_date;
                        time_elem.value = time_elem.initial_time;

                        alert("Error editing appointment");
                        console.log(resp.error);
                    }
                    else{
                        csrf_token.value = resp.csrf_token;
                        alert("Appointment edited successfully");
                        //Updating the initial date and time values
                        date_elem.initial_date = date_elem.value;
                        time_elem.initial_time = time_elem.value;
                    }
            })
            );
        });
    });

    Array.from(document.getElementsByClassName("reset_changes")).forEach(btn => {
        btn.addEventListener("click", event => {
            //Getting the appointment id and the corresponding date and time elements
            let appointment_id = event.target.parentNode.id;

            let date_elem = document.getElementById("date_" + appointment_id);
            let time_select = document.getElementById("time_" + appointment_id);

            //Resetting the date and time to the initial values
            date_elem.value = date_elem.initial_date;
            Array.from(time_select).find(option => option.value == time_select.initial_time).selected = true;
            time_select.value = time_select.initial_time;
        })
    });

    Array.from(document.getElementsByClassName("app_summary")).forEach(elem => {
        let app_state = elem.getAttribute("app_state");
        //If the appointment is upcoming we disable the button
        if (app_state == "UPCOMING"){
            elem.disabled = true;
            return;
        }

        elem.addEventListener("click", event => {
            let app_state = event.target.getAttribute("app_state");
            let appointment_id = event.target.parentNode.id;

            //Choosing the correct handler based on the appointment state
            switch(app_state){
                case "HAS_SUMMARY":
                    hasSummaryHandler(appointment_id);
                    break;
                case "FINISHED":
                    finishedHandler(appointment_id);
                    break;
                case "UPCOMING":
                    upcomingHandler(event.target);
                    break;
                case "INEXISTENT":
                    inexistentHandler(event.target);
                    break;
                default:
                    invalidStateHandler(event.target, app_state);
                    break;
            }
        });

    });

    //Sets initial date and time for each appointment
    //Adds event listeners to the time inputs to fill the time options or enable the valid ones
    function setInitialData(){
        Array.from(document.getElementsByClassName("app_date")).forEach(elem => {
            elem.min = utils_app.toDateInputValue(new Date());
            elem.initial_date = elem.value;

            elem.addEventListener("change", async event => {
                //Should not use .parentNode.parentNode, but it works for now
                let app_id = event.target.parentNode.parentNode.id;
                let time_select = document.getElementById("time_" + app_id);
                let date = event.target.value;
                let medic_id = document.getElementById("medic_" + app_id).getAttribute("value");
                let hospital_id = document.getElementById("hospital_" + app_id).getAttribute("value");
                let duration = parseInt(document.getElementById("duration_" + app_id).getAttribute("value"));
                
                fillTimeOptions(date, time_select, app_constants.OPENING_TIME, app_constants.CLOSING_TIME, duration);

                //Enabling the time options based on the new date
                let unavailable_times = await utils_app.getUnavailableTimes(medic_id, hospital_id, date);
                utils_app.enableFreeTimeIntervals(unavailable_times, time_select.children);
                
                utils_app.selectFirstEnabledOption(time_select);
            })
        });

        Array.from(document.getElementsByClassName("app_time")).forEach(elem => {
            elem.initial_time = elem.value;
            // Adding one time event listener for filling and enabling time options
            elem.addEventListener("click", async event => {
                //Should not use .parentNode.parentNode, but it works for now
                let app_id = event.target.parentNode.parentNode.id;
                let date_elem = document.getElementById("date_" + app_id);
                let date = date_elem.value;
                let medic_id = document.getElementById("medic_" + app_id).getAttribute("value");
                let hospital_id = document.getElementById("hospital_" + app_id).getAttribute("value");
                let duration = parseInt(document.getElementById("duration_" + app_id).getAttribute("value"));
                let time_select = event.target;

                let filled = fillTimeOptions(date, time_select, app_constants.OPENING_TIME,
                                             app_constants.CLOSING_TIME, duration);
                //If time options was filled we need to fetch the unavailable times and disable them
                if (filled){
                    //Enabling the time options based on the new date
                    let unavailable_times = await utils_app.getUnavailableTimes(medic_id, hospital_id, date);
                    utils_app.enableFreeTimeIntervals(unavailable_times, time_select.children);
                }
                
            }, {once: true});
        });
    }
    
    //Wrapper for fillTimeOptions from utils that removes the selected option, fills the time options and re-selects it
    //Such that the initial time is in it's correct place(alwaysnot first)
    function fillTimeOptions(date, time_options, op_time, cl_time, app_step){
        if (time_options.childElementCount > 1)return false;
        //Removing the selected option
        let sel_opt = utils_app.removeSelectedOption(time_options);

        //Filling the time options
        utils_app.fillTimeOptions(date, time_options, op_time, cl_time, app_step);
        
        //Re-selecting the previously selected option
        Array.from(time_options).find(option => option.value == sel_opt).selected = true;
        time_options.value = sel_opt;

        return true;
    }
    
    function inexistentHandler(elem){
        elem.disabled = true;
        alert("This appointment doesn't exist");
    }

    function upcomingHandler(elem){
        elem.disabled = true;
        alert("You can't write a summary for an upcoming appointment");
    }

    function invalidStateHandler(elem, app_state){
        elem.disabled = true;
        alert("Invalid appointment state: " + app_state);
    }

    function hasSummaryHandler(appointment_id){
        window.location.href = "/hosplive/appointments/view_summary?appointment_id=" + appointment_id;
    }

    function finishedHandler(appointment_id){
        window.location.href = "/hosplive/appointments/add_summary?appointment_id=" + appointment_id;
    }
    
});