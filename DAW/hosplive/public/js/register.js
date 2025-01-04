addEventListener('DOMContentLoaded', function(event) {
    let register_form = document.getElementById('register_form');

    register_form.addEventListener('submit', (event) =>{
        event.preventDefault();
        //Making sure recaptcha library is loaded
        grecaptcha.ready(() => {
            //Getting the user activity representive token and sending the form
            grecaptcha.execute(recaptcha_input.getAttribute("site_key"), { action: 'register' }).then((token) => {
                recaptcha_input.value = token;
                sendRegistrationForm(event.target);
            });
        });
        
    });

    function sendRegistrationForm(register_form){
        //Creating form object and filling it with the data from the inputs
        let data = new FormData(register_form);

        fetch("add_user", {
            method: "POST",
            body: data
        }).then(response => response.json().then(resp => {
            //If the response is not ok, alert the error
            if (!resp.ok)
                alert(resp.error);
            //If the response contains a redirect, redirect to the given page
            if (resp.hasOwnProperty('redirect'))
                window.location.href = resp.redirect;
        }));
    }
});