<!DOCTYPE html>

<html>
    <head>
        <title>Register Medic</title>
        <script src="../../public/add_medic.js?$$REVISION$$" type="module"></script>
        <link href="../../public/add_medic.css?$$REVISION$$" rel="stylesheet">
    </head>
    <body>
        <form id="medic_form">
            <input type="text" placeholder="Specialization" id="spec_input" list="spec_list" name="specialization_id" autocomplete="false" required>
                <datalist id="spec_list">
                    <option disabled>Select Specialization</option>
                    <?php 
                        foreach ($specializations as $spec){
                            echo "<option value='{$spec->specialization_name}' id='{$spec->specialization_name}' spec_id='{$spec->specialization_id}' disabled>{$spec->specialization_name}</option>";
                        }?>
                </datalist>
                <input type="number" id="years_exp" name="years_exp" placeholder="years of experience" required>
                <input type="submit" id="add_medic_btn" value="Add Medic">
        </form>
    </body>
</html>