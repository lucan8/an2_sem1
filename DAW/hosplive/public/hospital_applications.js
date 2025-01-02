import * as utils from "./utils_appointments.js"

addEventListener("DOMContentLoaded", (event) => {
    //Setting the event listeners for the save_changes buttons
    Array.from(document.getElementsByClassName("save_changes")).forEach((btn) => {
        btn.addEventListener("click", (event) => {
            let application_id = event.target.parentNode.id;
            let applicant_user_id = event.target.parentNode.getAttribute('applicant_user_id');

            let status = document.getElementById("status_" + application_id);
            let sel_status = status.children[status.selectedIndex];

            let contract = document.getElementById("hiring_contract_" + application_id);

            let data = new FormData();
            data.append("application_id", application_id);
            data.append("applicant_user_id", applicant_user_id)
            data.append("new_status_id", sel_status.getAttribute("status_id"))
            data.append("new_status_name", sel_status.value);
            //Adding the contract file to the form data if it actually exists
            if (contract)
                data.append("hiring_contract", contract.files[0]);

            fetch("change_status", {
                "method": "POST",
                "body": data
            }).then(response => response.json().then(resp => {
                if (!resp.ok){
                    alert("Error changing status!");
                    console.log(resp.error);
                }
                else{
                    alert("Status changed succesfully!");
                    //Removing the contract if it exists
                    if (contract) 
                        contract.remove();
                }
            }));
        });
    });

    //For every status select adding an event listener that on change adds a file input for contract
    //If the changed status is "Hired" otherwise removes the file input
    Array.from(document.getElementsByClassName("app_status")).forEach((status_sel) =>{
        status_sel.addEventListener('change', (event) => {
            //Getting the application container and the application id
            let application_cont = event.target.parentNode.parentNode
            let application_id = application_cont.id;

            //Add file input for "hired"
            if (event.target.value == "Hired"){
                alert("Please add the contract file!");
                let contract_input = document.createElement('input');

                contract_input.type = 'file';
                contract_input.accept = ".pdf";

                contract_input.name = "hiring_contract";
                contract_input.id = "hiring_contract_" + application_id;

                application_cont.appendChild(contract_input);
            }
            else{ //Remove the input element if needed
                let hiring_contract = document.getElementById("hiring_contract_" + application_id);
                if (hiring_contract)
                    hiring_contract.remove();
            }
        });

        //First time fill the status select
        status_sel.addEventListener("click", (event) => {
            fillStatusesOptions(event.target);
        }, {once: true});
    })

    async function fillStatusesOptions(stat_cont){
        if (stat_cont.childElementCount > 1)
            return false;

        let selected_status = stat_cont.value;
        let statuses = await getStatuses();

        statuses.forEach((status) => {
            //Skipping the already existing status
            if (status.application_status_name != selected_status)
                addStatusOption(stat_cont, status.application_status_name, status.application_status_id);
        });
    }

    Array.from(document.getElementsByClassName("view_cv")).forEach((btn) => 
        btn.addEventListener("click", async (event) => {
            let applicant_user_id = event.target.parentNode.getAttribute("applicant_user_id");
            let cv_url = await getMedicCVURL(applicant_user_id);
            let child_window = window.open("/public/cv.html");

            //Sending the cv temp url to the child window 
            child_window.addEventListener("load", (event) => {
                child_window.postMessage({"cv_url":cv_url});
                console.log("Temp URL sent!");
            });

            //Freeing resources if the child window is closed
            window.addEventListener("message", (event) => {
                console.log(event.data);
                if (event.data == "childWindowClosed"){
                    URL.revokeObjectURL(cv_url);
                    console.log("Freed temporary URL");
                }
                console.log("CHILD DEAD?");
            });
        })
    );

    async function getMedicCVURL(applicant_user_id){
        return fetch("get_medic_cv?applicant_user_id=" + applicant_user_id).
            then(async response => {return response.blob().then((resp) => {
                return URL.createObjectURL(resp);
        })});
    }

    //Creates and adds option to that container
    function addStatusOption(stat_cont, stat_name, stat_id){
        let opt = utils.addOption(stat_cont, stat_name, stat_name);
        opt.setAttribute("status_id", stat_id);

        return opt;
    }

    //Makes get request for statuses and returns the promise
    async function getStatuses(){
        return fetch("/hosplive/get_statuses").then(async response => {return response.json().then(resp => {
            if (!resp.ok){
                alert("Error getting statuses!");
                console.log(resp.error);
                return;
            }
            return resp["data"]["statuses"];
        })});
    }

});