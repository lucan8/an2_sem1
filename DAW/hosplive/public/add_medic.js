addEventListener("DOMContentLoaded", () => {
    let medic_form = document.getElementById("medic_form");
    let spec_sugg = document.getElementById("spec_list");
    let spec_input = document.getElementById("spec_input");
    let years_exp_input = document.getElementById("years_exp");

    medic_form.addEventListener("submit", (event) => {
        event.preventDefault();

        let data = new FormData();
        data.append("specialization_id", document.getElementById(spec_input.value).getAttribute("spec_id"));
        data.append("years_exp", years_exp_input.value);
        
        fetch("specialize_user", {
            method: "POST",
            body: data
        }).then((response) => response.json()).then((resp) => {
            if (resp.ok) 
                window.location.href = resp.redirect;
            else {
                alert("Error specializing user")
                console.log(resp.error)
            }
        })
    });
    spec_input.addEventListener("input", function(event){
        let input_spec = event.target.value;
        //Searching for input_spec in the options
        let associated_opt = Array.from(spec_sugg.children).find((option) => {return option.textContent.toLowerCase() == input_spec.toLowerCase()});
        if (associated_opt)
            event.target.value = associated_opt.textContent;
        else
            makeSuggestions(spec_sugg, event.target);
    });
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