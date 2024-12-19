addEventListener("DOMContentLoaded", (event) =>{
    let login_form = document.getElementById("login_form");

    login_form.addEventListener("submit", (event) =>{
        //Prevent form submission
        event.preventDefault();

        //Create form object and send post request to the server
        let data = new FormData(login_form);
        fetch("login", {
            'method': 'POST',
            'body': data
        }).then((response) => response.json().then((resp) =>{
            if (resp.ok)
                window.location.href = resp.redirect;
            else{
                alert("Error loggin user in");
                console.log(resp.error);
            }
         }));
    });
});
