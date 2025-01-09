<!DOCTYPE html>
<html>
    <head>
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo Config\config_recaptcha["front_key"]?>"></script>
        <script src="../../public/js/add_summary.js?$$REVISION$$" type='module'></script>
    </head>

    <body>
        <h1>Add Summary</h1>
        <form id='summary_form' action="/appointments/add_summary" method="post">
            <input type="hidden" name="appointment_id" value="<?php echo $app->appointment_id; ?>">
            <textarea name="patient_reason" placeholder="Add patient reason for coming"></textarea>
            <textarea name="symptoms" placeholder="Add patient symptoms"></textarea>
            <textarea name="diagnosis" placeholder="Add diagnosis"></textarea>
            <textarea name="treatment" placeholder="Add treatment"></textarea>
            <textarea name="other_observations" placeholder="Add other_observations"></textarea>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="recaptcha_input" id="recaptcha_input" site_key="<?php echo Config\config_recaptcha["front_key"]?>">
            <input type="submit" value="Save">
        </form>
    </body>
</html>