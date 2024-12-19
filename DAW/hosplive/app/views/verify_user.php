<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../public/verify_user.css?$$REVISION$$" rel="stylesheet">
    <script src="../../public/verify_user.js?$$REVISION$$" type="module"></script>
    <title>Verify User</title>
</head>

<body>
    <div id="verif_user_cont" user_email="<?php echo $user_email?>">
        <input type="number" id="verif_code" name="verif_code" placeholder="verification code" required>
        <button type="submit" id="verify_btn" disabled>Verify user</button>
    </div>
</body>