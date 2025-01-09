<!DOCTYPE html>
<html>
    <head>
        <title>Job Applications</title>
        <link rel="stylesheet" type="text/css" href="../../public/css/appointments.css">
    </head>

    <body>
        <h1>These are your sent job applications</h1>
        <?php
            foreach ($applications as $application){
                echo "<div class='appointment_cont' id='application_" . $application["id"] . "'>";
                echo "<p>Hospital Location: " . $application["county"]. "</p>";
                echo "<p>Application Date: " . $application["date"] . "</p>";
                echo "<p>Status: " . $application["status"] . "</p>";
                echo "</div>";
            }
        ?>
    </body>

</html>