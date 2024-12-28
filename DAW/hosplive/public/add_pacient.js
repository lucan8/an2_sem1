addEventListener("DOMContentLoaded", (event) =>{
    let pacient_form = document.getElementById("pacient_form");
    
    pacient_form.addEventListener("submit", (event) => {
        event.preventDefault();
        let form_data = new FormData(pacient_form);
        
        fetch("specialize_user", {
            method: "POST",
            body: form_data
        }).then((response) => response.json().then(resp => {
            if (resp.ok) 
                window.location.href = resp.redirect;
            else {
                alert("Error specializing user")
                console.log(resp.error)
            }
        }))
    })
});