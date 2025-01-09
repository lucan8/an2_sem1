addEventListener("DOMContentLoaded", (event) => {
    let summary_form = document.getElementById("summary_form");
    let recaptcha_input = document.getElementById("recaptcha_input");

    summary_form.addEventListener("submit", (event) => {
        event.preventDefault();
        //Making sure recaptcha library is loaded
        grecaptcha.ready(() => {
            //Getting the user activity representive token and sending the form
            grecaptcha.execute(recaptcha_input.getAttribute("site_key"), { action: 'add_summary' }).then((token) => {
                recaptcha_input.value = token;
                sendSummaryForm(summary_form);
            });
        });

    });

    function sendSummaryForm(summary_form){
        let data = new FormData(summary_form);

        fetch("add_summary", {
            method: "POST",
            body: data
        }).then((response) => response.json().then((resp) => {
            if (resp.ok)
                alert("Summary saved successfully, a copy was sent to your email");
            else{
                alert("Error saving summary");
                console.log(resp.error);
            }

            if (resp.redirect)
                window.location.href = resp.redirect;
        })); 
    }
});