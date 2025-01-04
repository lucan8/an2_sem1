addEventListener('DOMContentLoaded', (event) => {
    let verify_form = document.getElementById("verify_form");
    let resend_code_btn = document.getElementById("resend_code_btn");
    
    let remaining_resends = document.getElementById("remaining_resends");
    let remaining_tries = document.getElementById("remaining_tries");

    verify_form.addEventListener('submit', (event) =>{
        event.preventDefault();
        //Creating the form object and filling it with the user id and the verification code
        let data = new FormData(verify_form);

        fetch("verify_user",{
            method: "POST",
            body: data
        }).then(response => response.json().then(resp => {
            //If the response is ok, the user is redirected
            if(resp.ok)
                window.location.href = resp.redirect;
            else{
                alert("Error verifying user");
                remaining_tries.innerText = resp.remaining_tries;
                console.log(resp.error);
            }
        }));
    });

    resend_code_btn.addEventListener('click', (event) =>{
        fetch("resend_verif_code", {
            method: "POST"
        }).then(response => response.json().then(resp => {
            if(resp.ok)
                alert("Verification code sent");
            else{
                alert(resp.error);
                //Redirect user to login page if the user has no more resends
                if (resp.remaining_resends === 0)
                    window.location.href = "/login";
            }
            remaining_resends.innerText = resp.remaining_resends;
        }));
    });
});
