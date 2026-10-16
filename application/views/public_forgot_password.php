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
        margin: 100px auto;
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
            <div class="gs-grid-label sub uppercase">REQUEST RESET PASSWORD</div>
            <?php
            echo form_open('request_reset');
            if (isset($message_response)) {
                echo "<div class='gs-login-error'>" . $message_response . "</div>";
            }
            ?>
            <div class="golf-input-group">
                <input type="text" class="gs-text-input login" name="admin_email" placeholder="Email"
                    value="<?php echo isset($bounced) ? $bounced->admin_email : ''; ?>" autocomplete="off">
                <?= form_error("admin_email", "<span>", "</span>"); ?>
            </div>
            <div>
                <input type="submit" class="gs-login-button" name="login" value="Send Request">
            </div>
            <?php echo form_close(); ?>
            <div style="display:flex;justify-content:center; margin-top: 30px;">
                <a class="gs-table-link" href="<?php echo base_url('gemstone') ?>">Already have account?</a>
            </div>
        </div>
    </div>
</body>

</html>