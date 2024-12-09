<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../public/appointments.css?$$REVISION$$" rel="stylesheet">
    <script src="../../public/appointment.js?$$REVISION$$"></script>
    <title>Your Appointments</title>

    <div id="constants_appointments" hidden>
        <option id="opening_time_appointments" value="<?= Hospitals :: OPENING_TIME ?>"><?= Hospitals :: OPENING_TIME ?></option>
        <option id="closing_time_appointments" value="<?= Hospitals :: CLOSING_TIME ?>"><?= Hospitals :: CLOSING_TIME ?></option>
        <option id="app_duration_appointments" value="<?= Appointments :: DEFAULT_DURATION ?>"><?= Appointments :: DEFAULT_DURATION ?></option>
    </div>
</head>

<body>
    <div>
        <?php 
            foreach ($appointments as $appointment) { 
                echo "<div class='appointment_cont' id='" . $appointment["id"] . "'>";
                echo "<div>Hospital Location<p>" . $appointment["county"] . "</p></div>";
                echo "<div>Medic<p>" . $appointment["medic_name"] . "</p></div>";
                echo "<div>Date<input class='app_date' type='date' id='date_" . $appointment["id"] . "' value='". $appointment["date"] . "'> </div>";
                //The option is temporary, after all times are loaded this will be removed
                //It will be replaced with a copy that is placed in it's normal place(sorted asc)
                echo "<div>Time<select class='app_time' id='time_" . $appointment["id"] . "'><option selected value='" . $appointment["time"] . "'>" . $appointment["time"] . "</option></select></div>";
                echo "<div>Room<p>" . $appointment["room"] . "</p></div>";
                echo "<button class='cancel_app'>Cancel</button>";
                echo "<button class='edit_app'>Save Changes</button>";
                //Hidden inputs to store the ids
                echo "<input type='hidden' id='medic_id_" . $appointment["id"] . "' value='" . $appointment["medic_id"] . "'>";
                echo "<input type='hidden' id='hospital_id_" . $appointment["id"] . "'value='" . $appointment["hospital_id"] . "'>";
                echo "</div>";
            }?>
    </div>
</body>