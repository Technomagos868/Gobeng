<!DOCTYPE html>
<html lang="en">

<head>
    <title>Coralis Studio - Powered by Archelon Engine</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?php echo base_url() ?>gemstone_core/image/archelon_logo-only-blue.png">
    <link rel="stylesheet" href="<?php echo base_url() ?>gemstone_core/css/gemstone.css">
    <script type="text/javascript" src="<?php echo base_url() ?>gemstone_core/js/jquery-3.4.0.min.js"></script>
</head>
<style>
    .gs-table-link {
        color: white;
        text-decoration: none;
    }

    .text-left {
        text-align: left;
    }

    .text-right {
        text-align: right;
    }
</style>

<body class="gs-page">
    <div class="gs-grid-content">
        <div class="gs-login-form">
            <div class="ae-company-logo">
                <img alt="Coralis Studio" src="<?php echo base_url("assets/image/company-logo-white.png"); ?>">
            </div>
            <?php
            echo form_open('gemstone/login');
            if (isset($error)) {
                echo "<div class='gs-login-error'>" . $error . "</div>";
            }
            if ($this->session->flashdata('message_response')) {
                echo "<div class='gs-login-error'>" . $this->session->flashdata('message_response') . "</div>";
            }
            ?>
            <input type="text" class="gs-text-input login" name="username" placeholder="Username" autocomplete="off">
            <input type="password" class="gs-text-input login" name="password" placeholder="Password"
                autocomplete="off">
            <div>
                <input type="submit" class="gs-login-button" name="login" value="Log in">
            </div>
            <?php echo form_close(); ?>
            <div style="display:flex;justify-content:center; margin-top: 30px;">
                <a class="gs-table-link text-right" href="<?php echo base_url('/forgot_password') ?>">Forgot
                    password?</a>
            </div>
        </div>
    </div>
</body>

</html>