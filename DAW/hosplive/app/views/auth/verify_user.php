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
    <?php
        if ($_SESSION["from"] == SessionCreatedFrom :: register){
            echo "<h1>You have been registered succesfully</h1>";
        }
        else if ($_SESSION["from"] == SessionCreatedFrom :: login){
            echo "<h1>You have logged in succesfully</h1>";
        }
        echo "<h2>Step 2: Check your email for a verification code(don't forget about spam)</h2>";
    ?>
    <form id="verify_form" action="/verify_user" method="POST">
        <input type="number" id="verif_code" name="verif_code" placeholder="verification code" required>
        <input type="submit" id="verify_btn" value="Verify user">
    </form>
    <button id="resend_code_btn">Resend verification code</button>
    <div id="remaining_cont">
        <div class="remaining">Remaining resends: <span id="remaining_resends"><?php echo $remaining_resends ?></span></div>
        <div class="remaining">Remaining tries: <span id="remaining_tries"><?php echo $remaining_tries ?></span></div>
    </div>
</body>