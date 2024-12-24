<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../public/make_appointments.js?$$REVISION$$" type='module'></script>
    <title>Make Appointments</title>
</head>
<body>
    <div>
        <div id="county_div">
            <input type="text" placeholder="County" id="county_input" list="county_list" autocomplete="false" required>
            <datalist id="county_list">
                <option disabled>Select County</option>
                <?php 
                    foreach ($counties as $county){
                        echo "<option value='{$county->county_name}' id='{$county->county_name}' name='{$county->county_id}' disabled>{$county->county_name}</option>";
                    }?>
            </datalist>
        </div>

        <div id="spec_div">
            <input type="text" placeholder="Specialization" id="specialization_input" list="spec_list" disabled autocomplete="false" required>
            <datalist id="spec_list">
                <option disabled>Select Specialization</option>
                <?php 
                    foreach ($specializations as $spec){
                        echo "<option value='{$spec->specialization_name}' id='{$spec->specialization_name}' name='{$spec->specialization_id}' disabled>{$spec->specialization_name}</option>";
                    }?>
            </datalist>
        </div>

        <select id="medic_select" disabled required>
            <option id="select_medic" selected disabled>Select medic</option>
        </select>

        <!--Better solution here-->
        <!--https://stackoverflow.com/questions/19655250/is-it-possible-to-disable-input-time-clear-button-->
        <input type="date" id="date_input" disabled required>

        <div>
            <select id='time_select' disabled required>
                <option id="select_time" selected disabled>Select Time</option>
            </select>
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
        <input type="number" id="duration" name="duration">
    </form>
</body>
</html>
