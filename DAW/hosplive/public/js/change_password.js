addEventListener("DOMContentLoaded", event => {
    let change_pass_form = document.getElementById("change_password_form");
    let recaptcha_input = document.getElementById("recaptcha_input");

    change_pass_form.addEventListener("submit", event => {
        event.preventDefault();

        grecaptcha.ready(() => {
            //Getting the user activity representive token and sending the form
            grecaptcha.execute(recaptcha_input.getAttribute("site_key"), { action: 'change_password' }).then((token) => {
                recaptcha_input.value = token;
                sendPassChangeForm(change_pass_form);
            });
        });
    });

    function sendPassChangeForm(change_pass_form){
        let data = new FormData(change_pass_form);
        fetch("change_password", {
            method: "POST",
            body: data
        }).then((response) => response.json().then((resp) => {
            if (resp.ok)
                alert("Password changed successfully");
            else
                alert("Error: " + resp.error);

            if (resp.redirect)
                window.location.href = resp.redirect;
        }));

    }
});