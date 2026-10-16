<!DOCTYPE html>
<html lang="en">

<head>
    <title>Gemstone Management System</title>
</head>

<body style="background-color: #fefefe; color: #262626; text-align: center; line-height: 20px;">
    <h1>Password Reset Token</h1>
    <h3>Hi, <?php echo $user_info; ?>. We received a request for a password reset a moment ago.</h3>
    <p>If you requested this, you can change your password from the following link:</p>
    <a target="_blank" href="<?php echo $link; ?>" style="display: inline-block; color: #fff; background-color: #2070b7; padding: 10px; text-decoration: none;">Change Password</a>
    <p>If the link does not work, you can copy and paste the following to your browser address bar:</p>
    <p style="padding: 10px;"><?php echo $link; ?></p>
    <p>The link will be valid for 24 hours from the time a request is received by our system. After 24 hours, the link will expire and you will need to request for a new link.</p>
    <p>If you did not request a password change, just ignore this email.</p>
</body>

</html>