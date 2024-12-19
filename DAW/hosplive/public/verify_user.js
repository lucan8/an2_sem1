addEventListener('DOMContentLoaded', (event) => {
    let verif_code_input = document.getElementById('verif_code');
    let verify_btn = document.getElementById('verify_btn');
    let user_email = document.getElementById('verif_user_cont').getAttribute("user_email");

    verify_btn.addEventListener('click', (event) =>{
        //Creating the form object and filling it with the user id and the verification code
        let data = new FormData();
        data.append("email", user_email);
        data.append("verif_code", verif_code_input.value);

        fetch("verify_user",{
            method: "POST",
            body: data
        }).then(response => response.json().then(resp => {
            //If the response is ok, the user is redirected to the index page
            if(resp.ok)
                window.location.href = resp.redirect;
            else{
                alert("Error verifying user");
                console.log(resp.error);
            }
        }));
    });
});