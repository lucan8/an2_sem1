<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../public/register.css?$$REVISION$$" rel="stylesheet">
    <script src="../../public/register.js?$$REVISION$$" type="module"></script>
    <title>Register</title>
</head>

<body>
    <h1>This is a 3-step registration process</h1>
    <h2>Step 1: Fill in the fields below and click on the button to receive a verification code via email</h2>
    <div>
        <div id="inputs_div">
            <!--The names of the fields should match the ones in the UsersData class-->
            <input type="text" id="user_name" name="user_name" placeholder="username" required>
            <input type="password" id="password" name="password" placeholder="password" required>
            <input type="email" id="email" name="email" placeholder="email" required>
            <input type="text" id="first_name" name="first_name" placeholder="first name" required>
            <input type="text" id="last_name" name="last_name" placeholder="last name" required>
            <input type="tel" id="phone_number" name="phone_number" placeholder="phone number" required>
            <input type="date" id="birth_date" name="birth_date" required>
            <select id="gender" name="gender_id" required>
                <option id='select_gender' value="" disabled selected>Select Gender</option>
                <?php
                    foreach ($genders as $gender) {
                        echo "<option value='" . $gender->gender_id .    "'>" . $gender->gender_name . "</option>";
                    }
                ?>
            </select>

            <select id="role" name="role_id" required>
                <option id='select_role' value="" disabled selected>Select Role</option>
                <?php
                    foreach ($roles as $role) {
                        echo "<option value='" . $role->role_id .    "'>" . $role->role_name . "</option>";
                    }
                ?>
            </select>
        </div>
        <button type="submit" id="send_verif_btn">Send verification code</button>
    </div>
    
</body>