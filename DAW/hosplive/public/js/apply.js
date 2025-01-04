addEventListener("DOMContentLoaded", (event) => {
    let apply_form = document.getElementById("apply_form");
    let input_county = document.getElementById("county_input");
    let county_sugg = document.getElementById("county_list");
    let recaptcha_input = document.getElementById("recaptcha_input");
    let csrf_token = document.getElementById("csrf_token");

    apply_form.addEventListener("submit", (event) => {
        event.preventDefault();
        
        //Making sure recaptcha library is loaded
        grecaptcha.ready(() => {
        //Getting the user activity representive token and sending the form
        grecaptcha.execute(recaptcha_input.getAttribute("site_key"), { action: 'job_application' }).then((token) => {
            recaptcha_input.value = token;
            sendApplicationForm();
        });
    });
       
    });

    function sendApplicationForm(){
        //Checking that the hospital actually exists
        let chosen_hosp =  document.getElementById(input_county.value.toLowerCase());
        if (!chosen_hosp){
            alert("Hospital from " + input_county.value + " does not exist!");
            return;
        }

        let data = new FormData();
        data.append("hospital_id", chosen_hosp.getAttribute("hospital_id"));
        data.append(recaptcha_input.name, recaptcha_input.value);
        data.append(csrf_token.name, csrf_token.value);

        fetch("apply", {
            method: "POST",
            body: data
        }).then(response => response.json().then(resp => {
            if (resp.ok){
                alert("Job application sent!");
                csrf_token.value = resp.csrf_token;
            }
            else{
                alert("Error sending job application!");
                console.log(resp.error);
            }
        }));
    }

    county_input.addEventListener("input", (event) => {
        let input_county = event.target.value;
        //Searching for input_spec in the options
        let associated_opt = Array.from(county_sugg.children).find((option) => {return option.textContent.toLowerCase() == input_county.toLowerCase()});
        if (associated_opt)
            event.target.value = associated_opt.textContent;
        else
            makeSuggestions(county_sugg, event.target);
    });

    //Enable all options from sugg_list that start with input_elem's value    
    function makeSuggestions(sugg_list, input_elem){
        let input_data = input_elem.value;
        if (input_data == "") return;

        //Enabling options that start with the input value, disabling the rest
        Array.from(sugg_list.children).forEach((option, index, arr) => {
            arr[index].disabled = !option.textContent.toLowerCase().startsWith(input_data.toLowerCase());
        });
    }
});