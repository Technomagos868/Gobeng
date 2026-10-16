<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
    <style>
        @font-face {
            font-family: gs;
            src: url("<?php echo base_url(); ?>/gemstone_core/font/Montserrat-Regular.ttf");
        }

        @font-face {
            font-family: gs-bold;
            src: url("<?php echo base_url(); ?>/gemstone_core/font/Montserrat-SemiBold.ttf");
        }

        @font-face {
            font-family: gs-lite;
            src: url("<?php echo base_url(); ?>/gemstone_core/font/Montserrat-Light.ttf");
        }

        .coming-soon-title{
            font-family: gs-bold;
            font-size: 2.4rem;
            color: #424242;
            margin: 10% 5%;
            font-weight: bold;
            text-align: center;
        }
        .coming-soon-sub-title{
            font-family: gs-lite;
            font-size: 1.2rem;
            color: #424242;
            margin: 0 100px;
            text-align: center;
        }
        .footer-text{
            position: fixed;
            bottom: 30px;
            width: 100%;
            text-align: center;
            font-family: gs-lite;
            font-size: 1rem;
            color: #424242;
        }
        .logo{
            display: block;
            width: 100%;
            padding: 25% 20% 10%;
            box-sizing: border-box;
            text-align: center;
        }
        .logo img{
            width:60%;
        }

        @media screen and (min-width: 1201px){
            .coming-soon-title{
                font-size: 4rem;
                margin: 50px auto;
            }
            .coming-soon-sub-title{
                font-size: 2rem;
            }
            .footer-text{
                font-size: 1.2rem;
            }
            .logo{
                padding: 5% 25%;
            }
            .logo img{
                width: 30%;
            }
        }
    </style>
    <head>
        <meta charset="utf-8">
        <title>404 Page Not Found</title>
        <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width">
        <link rel="icon" type="image/png" href="<?php echo base_url() . "/gemstone_core/image/favicon.png"; ?>">
    </head>
    <body style="margin: 0;padding: 0;font-size:0;">
        <div class="logo"><img src="<?php echo base_url() . "gemstone_core/image/coralis-logo.png"; ?>"></div>
        <div class="coming-soon-title"><?php echo $heading; ?></div>
        <div class="coming-soon-sub-title"><?php echo $message; ?></div>
        <a href="http://coralisstudio.com"><div class="footer-text">&copy 2018 Coralis Studio</div></a>
    </body>
</html>