<!DOCTYPE html>
<html>
    <head>
        <title>Apply</title>
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo Config\config_recaptcha["front_key"]?>"></script>
        <script src="../../public/js/apply.js?$$REVESION$$"></script>
    </head>
    <body>
        <h1>Choose the county of the hospital you want to apply to!</h1>
        <form id="apply_form" autocomplete="off">
            <input type="text" placeholder="County" id="county_input" list="county_list" name="county_id" required>
            <datalist id="county_list">
                <option disabled>Select County</option>
                <?php 
                    foreach ($hospitals as $county => $hospital){
                        echo "<option value='{$county}' id='" . strtolower($county) . "' hospital_id='{$hospital->hospital_id}' disabled>{$county}</option>";
                    }?>
            </datalist>
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $csrf_token?>">
            <input type="hidden" name="recaptcha_input" id="recaptcha_input" site_key="<?php echo Config\config_recaptcha["front_key"]?>">
            <input type="submit" id="apply_btn" value="Apply">
        </form>
    </body>
</html>
