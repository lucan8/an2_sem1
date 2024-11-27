<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../public/make_appointment.js?$$REVISION$$"></script>
    <title>Make Appointments</title>
</head>
<body>
    <div>
        <div id="county_div">
            <input type="text" placeholder="County" id="county_input" list="county_list" autocomplete="false">
            <datalist id="county_list">
                <option disabled>Select County</option>
                <?php 
                    foreach ($counties as $county){
                        echo "<option value='{$county->county_name}' id='{$county->county_id}' name='{$county->county_name}' disabled>{$county->county_name}</option>";
                    }?>
            </datalist>
        </div>

        <div id="spec_div">
            <input type="text" placeholder="Specialization" id="specialization_input" list="spec_list" disabled autocomplete="false">
            <datalist id="spec_list">
                <option disabled>Select Specialization</option>
                <?php 
                    foreach ($specializations as $spec){
                        echo "<option value='{$spec->specialization_name}' id='{$spec->specialization_id}' name='{$spec->specialization_name}' disabled>{$spec->specialization_name}</option>";
                    }?>
            </datalist>
        </div>

        <select id="medic_select" disabled>
            <option selected disabled>Select medic</option>
        </select>

        <!--Better solution here-->
        <!--https://stackoverflow.com/questions/19655250/is-it-possible-to-disable-input-time-clear-button-->
        <input type="date" id="date_input" disabled>

        <div>
            <input type="time" required id="time_input" list="time_list" min=<?= Hospitals :: OPENING_TIME ?> max=<?= Hospitals :: CLOSING_TIME ?> step=<?= Appointments :: DEFAULT_DURATION * 60 ?> disabled>
            <datalist id="time_list">
                <option disabled>Select Time</option>
            </datalist>
        </div>
        <input type="submit" value="Make Appointment" id="fill_form_btn">
    </div>

    <!-- Form with actual data -->
    <form id="appointment_form" hidden>
        <!--Temporary solution-->
        <input type="number" id="user_id" name="user_id" value="1">
        <input type="number" id="hospital_id" name="hospital_id">
        <input type="number" id="medic_id" name="medic_id">
        <input type="date" id="appointment_date" name="appointment_date">
        <input type="time" id="appointment_time" name="appointment_time">
        <input type="number" id="room_id" name="room_id">
    </form>
</body>
</html>
