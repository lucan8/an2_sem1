<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Register Patient</title>
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo Config\config_recaptcha["front_key"]?>"></script>
        <script src="../../public/add_pacient.js?$$REVISION$$" type="module"></script>
    </head>
    <h1>You have been verified succesfully!</h1>
    <h2>Step 3: Complete patient relevant information below</h2>
    <!--TODO: Ask for more information like medical history, allergies, etc.-->
    <body>
        <form id="patient_form">
            <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $_SESSION["user_id"]?>">
            <input type="hidden" name="recaptcha_input" id="recaptcha_input" site_key="<?php echo Config\config_recaptcha["front_key"]?>">
            <input type="submit" value="Add Patient" id="add_patient">
        </form>
    </body>
</html>