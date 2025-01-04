<!DOCTYPE html>

<html>
    <head>
        <title>Register Medic</title>
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo Config\config_recaptcha["front_key"]?>"></script>
        <script src="../../public/add_medic.js?$$REVISION$$" type="module"></script>
        <link href="../../public/add_medic.css?$$REVISION$$" rel="stylesheet">
    </head>
    <body>
        <h1>You have been verified succesfully!</h1>
        <h2>Step 3: Complete Medic relevant information below</h2>
        <form id="medic_form">
            <input type="text" placeholder="Specialization" id="spec_input" list="spec_list" name="specialization_id" autocomplete="false" required>
                <datalist id="spec_list">
                    <option disabled>Select Specialization</option>
                    <?php 
                        foreach ($data["specializations"] as $spec){
                            echo "<option value='{$spec->specialization_name}' id='" . strtolower($spec->specialization_name). "' spec_id='{$spec->specialization_id}' disabled>{$spec->specialization_name}</option>";
                        }?>
                </datalist>
                <input type="number" id="years_exp" name="years_exp" placeholder="years of experience" required>
                <input type="file" id="medic_cv" name="medic_cv" accept=".pdf" required>
                <input type="hidden" name="medic_id" id="medic_id" value="<?php echo $_SESSION["user_id"]?>">
                <input type="hidden" name="recaptcha_input" id="recaptcha_input" site_key="<?php echo Config\config_recaptcha["front_key"]?>">
                <input type="submit" id="add_medic_btn" value="Add Medic">
        </form>
    </body>
</html>