addEventListener("DOMContentLoaded", (event) =>{
    let login_form = document.getElementById("login_form");
    let recaptcha_input = document.getElementById("recaptcha_input");

    login_form.addEventListener("submit", (event) =>{
        //Prevent form submission
        event.preventDefault();

        //Making sure recaptcha library is loaded
        grecaptcha.ready(() => {
            //Getting the user activity representive token and sending the form
            grecaptcha.execute(recaptcha_input.getAttribute("site_key"), { action: 'login' }).then((token) => {
                recaptcha_input.value = token;
                sendLoginForm(event.target);
            });
        });
    });

    function sendLoginForm(login_form){
        //Create form object and send post request to the server
        let data = new FormData(login_form);
        fetch("login", {
            'method': 'POST',
            'body': data
        }).then((response) => response.json().then((resp) =>{
            if (!resp.ok)
                alert("Error loggin user in: " + resp.error);

            if (resp.redirect)
                window.location.href = resp.redirect;
        }));
    }
});
