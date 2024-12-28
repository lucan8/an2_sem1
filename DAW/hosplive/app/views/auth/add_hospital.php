<!DOCTYPE html>

<html>
    <head>
        <title>Register Hospital</title>
        <script src="../../public/add_hospital.js?$$REVISION$$" type="module"></script>
        <link href="../../public/add_hospital.css?$$REVISION$$" rel="stylesheet">
    </head>
    <body>
        <h1>You have been verified succesfully!</h1>
        <h2>Step 3: Complete hospital relevant information below</h2>
        <form id="hospital_form">
            <input type="text" placeholder="County" id="county_input" list="county_list" name="county_id" autocomplete="false" required>
            <datalist id="county_list">
                <option disabled>Select County</option>
                <?php 
                    foreach ($data["counties"] as $county){
                        echo "<option value='{$county->county_name}' id='{$county->county_name}' county_id='{$county->county_id}' disabled>{$county->county_name}</option>";
                    }?>
            </datalist>
            <input type="submit" id="add_hospital_btn" value="Add Hospital">
        </form>
    </body>
</html>