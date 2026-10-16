<!DOCTYPE html>
<html lang="en">

<head>
    <title>Gemstone Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?php echo base_url() ?>gemstone_core/image/favicon.png">
    <link rel="stylesheet" href="<?php echo base_url() ?>gemstone_core/css/gemstone.css">
    <script type="text/javascript" src="<?php echo base_url() ?>gemstone_core/js/jquery-3.4.0.min.js"></script>
</head>
<style>
    .gs-register-form {
        text-align: center;
        max-width: 500px;
        margin: 50px auto;
        background-color: var(--color-card);
        border: solid 2px var(--color-primary);
        padding: 25px;
        border-radius: var(--size-card-radius);
    }

    .gs-table-link {
        color: white;
        text-decoration: none;
    }

    .gs-text-input.login {
        margin: 0;
    }

    .golf-input-group,
    .recaptcha-input-group {
        display: flex;
        align-items: center;
        flex-direction: column;
        margin: 20px 0;
    }
</style>

<body class="gs-page">
    <div class="gs-grid-content">
        <div class="gs-register-form">
            <div class="gs-grid-label sub uppercase">RESET PASSWORD</div>
            <?php if (isset($valid) && $valid === TRUE) { ?>
                <?php
                echo form_open('process_password_reset');
                if (isset($message_response)) {
                    echo "<div class='gs-login-error'>" . $message_response . "</div>";
                }
                ?>
                <input type="hidden" name="token" value="<?php echo isset($bounced) ? $bounced->token : $token; ?>">
                <div class="golf-input-group">
                    <input type="password" class="gs-text-input login" name="new_password" placeholder="New Password" autocomplete="off">
                    <?= form_error("new_password", "<span>", "</span>"); ?>
                </div>
                <div class="golf-input-group">
                    <input type="password" class="gs-text-input login" name="confirm_password" placeholder="Confirm Password" autocomplete="off">
                    <?= form_error("confirm_password", "<span>", "</span>"); ?>
                </div>
                <div>
                    <input type="submit" class="gs-login-button" name="login" value="Reset">
                </div>
                <?php echo form_close(); ?>
                <div style="display:flex;justify-content:center; margin-top: 30px;">
                    <a class="gs-table-link" href="<?php echo base_url('gemstone') ?>">Already have account?</a>
                </div>
            <?php } else { ?>
                <div class="row" style="justify-content: center;">
                    <h1>Invalid Link</h1>
                    <p>The link you provided is invalid or had already expired.</p>
                </div>
            <?php } ?>
        </div>
    </div>
</body>

</html>