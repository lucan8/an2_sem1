<!DOCTYPE html>
<html>
    <head>
        <title>Change Password</title>
        <script src="../../public/js/change_password.js?$$REVISION$$" type='module'></script>
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo Config\config_recaptcha["front_key"]?>"></script>
    </head>

    <body>
        <h1>Change Password</h1>
        <form id="change_password_form" action="/hosplive/auth/change_password" method="post">
            <input type="password" name="old_password" id="old_password" placeholder="Old password" required>
            <input type="password" name="new_password" id="new_password" placeholder="New password" required>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" required>
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="recaptcha_input" id="recaptcha_input" site_key="<?php echo Config\config_recaptcha["front_key"]?>">
            <input type="submit" value="Change Password">
        </form>
    </body>
</html>