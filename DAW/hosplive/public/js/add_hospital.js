addEventListener("DOMContentLoaded", () => {
    let hospital_form = document.getElementById("hospital_form");
    let county_sugg = document.getElementById("county_list");
    let county_input = document.getElementById("county_input");
    let recaptcha_input = document.getElementById("recaptcha_input");
    let hospital_id_input = document.getElementById("hospital_id");

    hospital_form.addEventListener("submit", (event) => {
        event.preventDefault();

        //Making sure recaptcha library is loaded
        grecaptcha.ready(() => {
            //Getting the user activity representive token and sending the form
            grecaptcha.execute(recaptcha_input.getAttribute("site_key"), { action: 'specialize' }).then((token) => {
                recaptcha_input.value = token;
                sendHospitalForm();
            });
        });

        
    });

    county_input.addEventListener("input", function(event){
        let input_county = event.target.value;
        //Searching for input_spec in the options
        let associated_opt = Array.from(county_sugg.children).find((option) => {return option.textContent.toLowerCase() == input_county.toLowerCase()});
        if (associated_opt)
            event.target.value = associated_opt.textContent;
        else
            makeSuggestions(county_sugg, event.target);
    });

    function sendHospitalForm(){
        //Making sure the specialization is chosen from the list
        let chosen_county = document.getElementById(county_input.value.toLowerCase());
        if (!chosen_county){
            alert("Invalid county");
            return;
        }
        
        let data = new FormData();
        data.append("county_id", chosen_county.getAttribute("county_id"));
        data.append(hospital_id_input.name, hospital_id_input.value);
        data.append(recaptcha_input.name, recaptcha_input.value);
        
        fetch("specialize_user", {
            method: "POST",
            body: data
        }).then((response) => response.json().then((resp) => {
            if (resp.ok) 
                window.location.href = resp.redirect;
            else {
                alert("Error specializing user")
                console.log(resp.error)
            }
        }));
    }

    //Create divs for each option that starts with the input value
    function makeSuggestions(sugg_list, input_elem){
        let input_data = input_elem.value;
        if (input_data == "") return;

        //Enabling options that start with the input value, disabling the rest
        Array.from(sugg_list.children).forEach((option, index, arr) => {
            arr[index].disabled = !option.textContent.toLowerCase().startsWith(input_data.toLowerCase());
        });
    }
});