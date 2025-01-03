addEventListener("DOMContentLoaded", (event) => {
    let apply_form = document.getElementById("apply_form");
    let input_county = document.getElementById("county_input");
    let county_sugg = document.getElementById("county_list");

    apply_form.addEventListener("submit", (event) => {
        event.preventDefault();
        let chosen_hosp =  document.getElementById(input_county.value);
        //Checking that the hospital actually exists
        if (!chosen_hosp){
            alert("Hospital from " + input_county.value + " does not exist!");
            return;
        }
        let data = new FormData();
        data.append("hosp_user_id", chosen_hosp.getAttribute("hosp_user_id"));

        fetch("apply", {
            method: "POST",
            body: data
        }).then(response => response.json().then(resp => {
            if (resp.ok)
                alert("Job application sent!");
            else{
                alert("Error sending job application!");
                console.log(resp.error);
            }
        }));
    });

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