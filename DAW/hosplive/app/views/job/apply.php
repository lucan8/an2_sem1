<!DOCTYPE html>
<html>
    <head>
        <title>Apply</title>
        <script src="../../public/apply.js?$$REVESION$$"></script>
    </head>
    <body>
        <h1>Choose the county of the hospital you want to apply to!</h1>
        <form id="apply_form" autocomplete="off">
            <input type="text" placeholder="County" id="county_input" list="county_list" name="county_id" required>
            <datalist id="county_list">
                <option disabled>Select County</option>
                <?php 
                    foreach ($hospitals as $county => $hospital){
                        echo "<option value='{$county}' id='{$county}' hosp_user_id='{$hospital->user_id}' disabled>{$county}</option>";
                    }?>
            </datalist>
            <input type="submit" id="apply_btn" value="Apply">
        </form>
    </body>
</html>
