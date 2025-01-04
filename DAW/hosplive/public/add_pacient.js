addEventListener("DOMContentLoaded", (event) =>{
    let patient_form = document.getElementById("patient_form");
    
    patient_form.addEventListener("submit", (event) => {
        event.preventDefault();
        
        //Making sure recaptcha library is loaded
        grecaptcha.ready(() => {
            //Getting the user activity representive token and sending the form
            grecaptcha.execute(recaptcha_input.getAttribute("site_key"), { action: 'specialize' }).then((token) => {
                recaptcha_input.value = token;
                sendpatientForm(patient_form);
            });
        });
        
    });

    function sendpatientForm(patient_form){
        let data = new FormData(patient_form);

        fetch("specialize_user", {
            method: "POST",
            body: data
        }).then((response) => response.json().then(resp => {
            if (resp.ok) 
                window.location.href = resp.redirect;
            else {
                alert("Error specializing user")
                console.log(resp.error)
            }
        }));
    }
});