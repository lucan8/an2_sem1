window.addEventListener("DOMContentLoaded", (event) => {
    let cv_cont = document.getElementById("cv_cont");
    //Getting the temporary url for the medic cv and setting it as the src of the iframe
    window.addEventListener("message", (event) => {
        if (event.data.cv_url){
            console.log("URL received");
            cv_cont.src = event.data.cv_url;
        }
        else
            console.log("No cv url found");
    });

    //Sending message back to parent before unloading
    window.addEventListener("beforeunload", (event) => {
        if (window.opener)
            window.opener.postMessage("childWindowClosed");
    });
});