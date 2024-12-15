export async function getConstants(){
    return fetch('getConstants').then(response => {return response.json();})
}

//Gets the unavailable times for the medic and hospital on the specified date
//Disables the options that are in the unavailable times array
//Selects the first time option that is enabled
export async function getUnavailableTimes(medic_id, hospital_id, date){
    return fetch('getUnavailableTimes?hospital_id=' + hospital_id +
                 '&medic_id=' + medic_id +
                 '&appointment_date=' + date)
            .then(async response => {
                return response.json().then(res => {
                    if (!res['ok']){
                        alert("Failed to get available times(interal error)");
                        console.log(res['error']);
                        return;
                    }
                    return res['data']['times'];
                });
            })
}

//Disable the options that are in the unavailable times array, enable the rest
//Both arrays are sorted
export function enableFreeTimeIntervals(unavailable_times, time_options){
    let u_time_index = 0;
    let t_options_index = 0;

    // Iterate through all options, if curr option is equal to unavailable time
    // We disable the option and go to the next unavailable time
    // Otherwise the option gets enabled because it's free
    while (u_time_index < unavailable_times.length){
        let u_time = unavailable_times[u_time_index];
        let time_option = time_options[t_options_index];
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
        time_options[t_options_index].disabled = false;
        time_options[t_options_index].hidden = false;
        t_options_index++;
    }
}

//Adds all possible appointments times between the min and max time of the time input with specified step
export function fillTimeOptions(date, time_options, op_time, cl_time, app_step){
    //If there are already options, don't add more
    if (time_options.childElementCount > 1) return false;
    
    //Getting the opening and closing time of the hospital
    let start = new Date(date + " " + op_time);
    let end = new Date(date + " " + cl_time);

    //Going from start to end adding options with the specified step, with the format HH:MM
    while (start.getTime() < end.getTime()){
        let time_string = getHoursAndMinutes(start);
        addOption(time_options, time_string, time_string);
        start.setMinutes(start.getMinutes() + app_step);
    }

    return true;
}

//Adds an option to option cont with the given value and text and returns the created option
export function addOption(option_cont, option_value, option_text){
    let option = document.createElement("option");
    option.value = option_value;
    option.text = option_text;
    option_cont.appendChild(option);

    return option;
}

//Remove all options from options_div except the first one
export function removeOptions(options_container){
    if (!options_container) return;
    while(options_container.childElementCount > 1){
        options_container.removeChild(options_container.lastChild);
    }
}

//Selects the first enabled option in the select element
//Returns false if there is no enabled option, true otherwise
export function selectFirstEnabledOption(options){
    for (let opt of options.children)
        if (!opt.disabled){
            opt.selected = true;
            options.value = opt.value;
            return true;
        }
    return false;
}

//Removes the selected option from the select element and returns the value of the removed option
export function removeSelectedOption(select_elem){
    let selected_opt = select_elem.options[select_elem.selectedIndex];
    let selected_val = selected_opt.value;
    selected_opt.remove();
    return selected_val;
}

//Returns the time as string in HH:MM format
export function getHoursAndMinutes(date_time){
    return ("0" + date_time.getHours()).slice(-2) + ":" + ("0" + date_time.getMinutes()).slice(-2)
}

export function toDateInputValue(dateObject){
    let local = new Date(dateObject);
    local.setMinutes(dateObject.getMinutes() - dateObject.getTimezoneOffset());
    return local.toJSON().slice(0,10);
}