<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../public/css/appointments.css?$$REVISION$$" rel="stylesheet">
    <script src="../../public/js/appointment.js?$$REVISION$$" type="module"></script>
    <title>Your Appointments</title>
</head>

<body>
    <div>
        <?php 
            foreach ($appointments as $appointment) { 
                echo "<div class='appointment_cont' id='" . $appointment["id"] . "'>";
                echo "<div>Hospital Location<p id='hospital_" . $appointment["id"] . "' value='" . $appointment["hospital_id"] . "'>" . $appointment["county"] . "</p></div>";
                if ($_SESSION["user_role"] == "medic"){ //Medics see the patient's name
                    echo "<div>Patient<p id='patient_" . $appointment["id"] . "' value='" . $appointment["patient_id"] . "'>" . $appointment["patient_name"] . "</p></div>";
                    //Needed for fethcing the unavailable times, ugly but it works
                    echo "<div hidden>Medic<p id='medic_" . $appointment["id"] . "' value='" . $_SESSION["user_id"] . "'></p></div>";
                }
                else if($_SESSION["user_role"] == "patient") //Patients see the medic's name
                    echo "<div>Medic<p id='medic_" . $appointment["id"] . "' value='" . $appointment["medic_id"] . "'>" . $appointment["medic_name"] . "</p></div>";
                echo "<div>Date<input class='app_date' type='date' id='date_" . $appointment["id"] . "' value='". $appointment["date"] . "'> </div>";
                //The option is temporary, after all times are loaded this will be removed
                //It will be replaced with a copy that is placed in it's normal place(sorted asc)
                echo "<div>Time<select class='app_time' id='time_" . $appointment["id"] . "'><option selected value='" . $appointment["time"] . "'>" . $appointment["time"] . "</option></select></div>";
                echo "<div>Room<p>" . $appointment["room"] . "</p></div>";
                echo "<div>Duration<p id='duration_" . $appointment["id"] . "' value='" . $appointment["duration"] . "'> " . $appointment["duration"] . "</p></div>";
                //Viewing the summary is available for everybody
                if ($appointment["app_state"] === AppointmentState::HAS_SUMMARY)
                    echo "<button class='app_summary' app_state='" . $appointment['app_state']->name . "'>" .  $appointment['app_state']->value . "</button>";
                else if ($_SESSION["user_role"] == "medic") //Medics can write the summary of the appointment
                        echo "<button class='app_summary' app_state='" . $appointment['app_state']->name . "'>" .  AppointmentState::FINISHED->value . "</button>";
                echo "<button class='reset_changes'>Reset Changes</button>";
                echo "<button class='cancel_app'>Cancel</button>";
                echo "<button class='edit_app'>Save Changes</button>";
                echo "</div>";
            }
            echo "<input type='hidden' id='csrf_token_app' value='$csrf_token' name='csrf_token'>";
            ?>
    </div>
</body>