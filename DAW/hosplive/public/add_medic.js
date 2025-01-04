addEventListener("DOMContentLoaded", () => {
    let medic_form = document.getElementById("medic_form");
    let spec_sugg = document.getElementById("spec_list");
    let spec_input = document.getElementById("spec_input");
    let years_exp_input = document.getElementById("years_exp");
    let medic_cv = document.getElementById("medic_cv");
    let medic_id_input = document.getElementById("medic_id");
    let recaptcha_input = document.getElementById("recaptcha_input");

    medic_form.addEventListener("submit", (event) => {
        event.preventDefault();

        //Making sure recaptcha library is loaded
        grecaptcha.ready(() => {
            //Getting the user activity representive token and sending the form
            grecaptcha.execute(recaptcha_input.getAttribute("site_key"), { action: 'specialize' }).then((token) => {
                recaptcha_input.value = token;
                sendMedicForm();
            });
        });
    });

    spec_input.addEventListener("input", (event) => {
        let input_spec = event.target.value;
        //Searching for input_spec in the options
        let associated_opt = Array.from(spec_sugg.children).find((option) => {return option.textContent.toLowerCase() == input_spec.toLowerCase()});
        if (associated_opt)
            event.target.value = associated_opt.textContent;
        else
            makeSuggestions(spec_sugg, event.target);
    });

    function sendMedicForm(){
        //Making sure the specialization is chosen from the list
        let chosen_spec = document.getElementById(spec_input.value.toLowerCase());
        if (!chosen_spec){
            alert("Invalid specialization");
            return;
        }

        let data = new FormData();
        data.append("specialization_id", chosen_spec.getAttribute("spec_id"));
        data.append("years_exp", years_exp_input.value);
        data.append("medic_cv", medic_cv.files[0]);
        data.append(medic_id_input.name, medic_id_input.value);
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