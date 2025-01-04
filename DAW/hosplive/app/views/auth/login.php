<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo Config\config_recaptcha["front_key"]?>"></script>
        <link rel="stylesheet" type="text/css" href="../../public/css/login.css?$$REVISION$$">
        <script src="../../public/js/login.js?$$REVISION$$" type="module"></script>
    </head>
    <body>
        <h1>This is a 2-step login process</h1>
        <h2>Step 1: Insert email and password and click on the button to receive a verification code via email</h2>
        <form id="login_form" method='post' action='/login'>
            <input type='text' id="email_input" name='email' placeholder='email' required>
            <input type='password' id="password_input" name='password' placeholder='Password' required>
            <input type="hidden" name="recaptcha_input" id="recaptcha_input" site_key="<?php echo Config\config_recaptcha["front_key"]?>">
            <input type='submit' value='Send verification code'>
        </form>
    </body>
</html>