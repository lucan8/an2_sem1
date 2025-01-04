<!DOCTYPE html>
<html>
    <head>
        <title>Job Applications</title>
        <link rel="stylesheet" type="text/css" href="../../public/appointments.css?$$REVISION$$">
        <script src="../../public/js/hospital_applications.js?$$REVISION$$" type='module'></script>
    </head>

    <body>
        <h1>These are your received job applications</h1>
        <?php
            foreach ($applications as $application){
                echo "<div class='appointment_cont' applicant_id='". $application["applicant_id"] . "' id='" . $application["id"] . "'>";
                echo "<div>Applicant name: " . $application["medic_name"] . "</div>";
                echo "<div>Years of experience: " . $application["years_exp"] . "</div>"; 
                echo "<div>Specializaiton: " . $application["specialization"] . "</div>";
                echo "<div>Application Date: " . $application["date"] . "</div>";
                echo "<div>Status: <select class='app_status' id='status_" . $application["id"] . "'> <option selected status_id='" . $application["status_id"] . "' value='" . $application["status"]  . "'>" . $application["status"] . "</option></select></div>";
                echo "<button class='view_cv'>View CV</button>"; 
                echo "<button class='save_changes'>Save Changes</button>";
                echo "</div>";
            }
        ?>
    </body>

</html>