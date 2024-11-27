<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../public/appointments.css?$$REVISION$$" rel="stylesheet">
    <script src="../../public/appointments.js?$$REVISION$$"></script>
    <title>Your Appointments</title>
</head>

<body>
    <div>
        <?php 
            foreach ($appointments as $appointment) { 
                echo "<div class='appointment_cont' id='" . $appointment["id"] . "'>";
                echo "<div>Hospital Location<p>" . $appointment["county"] . "</p></div>";
                echo "<div>Medic<p>" . $appointment["medic_name"] . "</p></div>";
                echo "<div>Date<p contenteditable='true' id='date_" . $appointment["id"] . "'>" . $appointment["date"] . "</p></div>";
                echo "<div>Time<p contenteditable='true' id='time_" . $appointment["id"] . "'>" . $appointment["time"] . "</p></div>";
                echo "<div>Room<p>" . $appointment["room"] . "</p></div>";
                echo "<button class='cancel_app'>Cancel</button>";
                echo "<button class='edit_app'>Save Changes</button>";
                echo "</div>";   
            }?>
    </div>
</body>