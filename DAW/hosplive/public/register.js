addEventListener('DOMContentLoaded', function(event) {
    let send_verif_btn = document.getElementById('send_verif_btn');
    let inputs = document.getElementById("inputs_div").children;

    send_verif_btn.addEventListener('click', (event) =>{
        //Creating form object and filling it with the data from the inputs
        let data = new FormData();
        
        Array.from(inputs).forEach(input => data.append(input.name, input.value));
        fetch("add_user", {
            method: "POST",
            body: data
        }).then(response => response.json().then(resp => {
            //If the response is ok, the user is redirected to the verification page
            if (resp.ok)
                window.location.href = resp.redirect;
            else{
                alert("Error adding user");
                console.log(resp.error);
            }
        }));
    });
});