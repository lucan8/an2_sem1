addEventListener("DOMContentLoaded", event => {
Array.from(document.getElementsByClassName("cancel_app")).forEach(btn => {
    btn.addEventListener("click", event => {
        let appointment_id = event.target.parentNode.id;
        let data = new FormData();
        data.append("appointment_id", appointment_id);

        //Sending a post request to cancel the appointment
        fetch("cancel_appointment", {
            method: "POST",
            body: data
        }).then(response => response.json().then(data => {
                if (response.ok){
                    event.target.parentNode.remove();
                }
                else{
                    alert("Error cancelling appointment");
                    console.log(data.error);
                }
          })
        );
    });
});

Array.from(document.getElementsByClassName("edit_app")).forEach(btn => {
    btn.addEventListener("click", event => {
        let appointment_id = event.target.parentNode.id;
        let data = new FormData();
        data.append("appointment_id", appointment_id);
        data.append("appointment_date", document.getElementById("date_" + appointment_id).innerText);
        data.append("appointment_time", document.getElementById("time_" + appointment_id).innerText);

        //Sending a post request to cancel the appointment
        fetch("edit_appointment", {
            method: "POST",
            body: data
        }).then(response => response.json().then(data => {
                if (!response.ok){
                    alert("Error editing appointment");
                    console.log(data.error);
                }
          })
        );
    });
});
});