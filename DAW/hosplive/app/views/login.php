<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
    </head>
    <link rel="stylesheet" type="text/css" href="../../public/login.css?$$REVISION$$">
    <script src="../../public/login.js?$$REVISION$$" type="module"></script>
    <body>
        <form id="login_form" method='post' action='/login'>
            <input type='text' id="email_input" name='email' placeholder='email'>
            <input type='password' id="password_input" name='password' placeholder='Password'>
            <input type='submit' name='login_btn' value='Login'>
        </form>
    </body>
</html>