<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Appointments</title>
</head>

<body>
    <div>
        <?php 
            foreach ($appointments as $appointment) { 
                echo "<div>";
                echo "<p>Hospital Location: " . $appointment["county"] . "</p>";
                echo "<p>Medic: " . $appointment["medic"] . "</p>";
                echo "<p>Date: " . $appointment["date"] . "</p>";
                echo "<p>Time: " . $appointment["time"] . "</p>";
                echo "<p>Room: " . $appointment["room"] . "</p>";
                echo "</div>";   
            }?>
    </div>
</body>