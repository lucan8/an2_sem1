addEventListener("DOMContentLoaded", () => {
    let hospital_form = document.getElementById("hospital_form");
    let county_sugg = document.getElementById("county_list");
    let county_input = document.getElementById("county_input");

    hospital_form.addEventListener("submit", (event) => {
        event.preventDefault();

        let data = new FormData();
        data.append("county_id", document.getElementById(county_input.value).getAttribute("county_id"));
        
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

    county_input.addEventListener("input", function(event){
        let input_county = event.target.value;
        //Searching for input_spec in the options
        let associated_opt = Array.from(county_sugg.children).find((option) => {return option.textContent.toLowerCase() == input_county.toLowerCase()});
        if (associated_opt)
            event.target.value = associated_opt.textContent;
        else
            makeSuggestions(county_sugg, event.target);
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