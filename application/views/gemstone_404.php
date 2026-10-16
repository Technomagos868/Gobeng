<style>
    <?php
    $fontUrl = (isset($missing_method) ? 'gemstone_core/font/Montserrat-Regular.ttf' : '../gemstone_core/font/Montserrat-Regular.ttf');
    $fontUrlLite = (isset($missing_method) ? 'gemstone_core/font/Montserrat-Light.ttf' : '../gemstone_core/font/Montserrat-Light.ttf');
    ?>@font-face {
        font-family: gs;
        src: url(<?php echo $fontUrl; ?>);
    }

    @font-face {
        font-family: gs-lite;
        src: url(<?php echo $fontUrlLite ?>);
    }

    body {
        background-color: #333333;
    }

    .gs-404-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        align-items: center;
    }

    .gs-404-logo {
        text-align: center;
        margin: 10px auto;
        padding: 50px;
        border-radius: 4px;
    }

    .gs-404-logo img {
        width: 100%;
        max-width: 300px;
    }

    .gs-404-title {
        color: #fafafa;
        text-align: center;
        font-family: gs;
        font-size: 5rem;
    }

    .gs-404-text {
        color: #fafafa;
        text-align: center;
        font-family: gs-lite;
        margin: 10px 0;
        font-size: 2rem;
    }

    @media screen and (max-width: 425px) {
        .gs-404-container {
            display: grid;
            grid-template-columns: 1fr;
            padding: calc(5vh + 70px) 20px;
        }
    }
</style>
<?php require_once GS_HEADER; ?>
<div class="gs-404-container">
    <div>
        <div class="gs-404-title">
            <?php
            if (isset($logged_in) && $logged_in) {
                if (isset($obsolete) && $obsolete) {
                    echo "Invalid Data";
                } else {
                    echo "Forbidden";
                }
            } else {
                echo "Not Found";
            }
            ?>
        </div>
        <div class="gs-404-text">
            <?php
            if (isset($logged_in) && $logged_in) {
                if (isset($obsolete) && $obsolete) {
                    echo "The data you requested is invalid or had been removed from the system. Contact system administrator for more information.";
                } else {
                    echo "You are not authorized to use this function.";
                }
            } else {
                echo "Function you requested is not found.";
            }
            ?>
        </div>
    </div>
    <div class="gs-404-logo">
        <img alt="gs-logo" src="<?php echo base_url() . "/gemstone_core/image/ae-powered-logo-white.png" ?>">
    </div>
</div>
<?php exit(); ?>