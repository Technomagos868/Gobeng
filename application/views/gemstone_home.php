<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo GEMSTONE_VERSION_TITLE; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?php echo base_url() ?>gemstone_core/image/archelon_logo-only-blue.png">
    <link rel="stylesheet" href="<?php echo base_url() ?>gemstone_core/css/chartist.css?v=<?php echo GEMSTONE_VERSION_NUMBER; ?>">
    <link rel="stylesheet" href="<?php echo base_url() ?>gemstone_core/css/chartist-plugin-tooltip.css?v=<?php echo GEMSTONE_VERSION_NUMBER; ?>">
    <link rel="stylesheet" href="<?php echo base_url() ?>gemstone_core/css/all.css?v=<?php echo GEMSTONE_VERSION_NUMBER; ?>">
    <link rel="stylesheet" href="<?php echo base_url() ?>gemstone_core/css/gemstone.css?v=<?php echo GEMSTONE_VERSION_NUMBER; ?>">
    <?php
    if (isset($gemstone_css_list) && is_array($gemstone_css_list)) {
        foreach ($gemstone_css_list as $css) {
            echo "<link rel='stylesheet' href='" . base_url() . $css . "?v=" . PROJECT_BUILD_VERSION . "'>";
        }
    }
    ?>
</head>

<body>
    <?php
    $client_custom_url = $_ENV["CLIENT_CUSTOM_URL"];
    $client_base_url = $_ENV["CLIENT_BASE_URL"];
    ?>
    <?php include_once "gemstone_sidebar.php"; ?>
    <div class="gs-prime-wrapper">
        <div id="token"></div>
        <div id="msg"></div>
        <div id="notis"></div>
        <div id="err"></div>
        <div class="gs-prime-container" id="gs-content"></div>
    </div>
    <?php
    if (isset($gemstone_browser_warning) && $gemstone_browser_warning === true) {
        echo draw_browser_warning();
    }
    ?>
    <script type="text/javascript">
        var CLIENT_BASE_URL = '<?php echo $client_base_url; ?>';
        var CLIENT_CUSTOM_URL = '<?php echo $client_custom_url; ?>';
    </script>
    <script type="text/javascript">
        var dt = new Date();
        var tabToken = "tt" + dt.getHours() + dt.getMinutes() + dt.getSeconds() + dt.getMilliseconds() + Math.floor(Math.random() * 1000).toString();
    </script>
    <script type="text/javascript" src="<?php echo base_url() ?>gemstone_core/js/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>gemstone_core/js/chartist.min.js?v=<?php echo GEMSTONE_VERSION_NUMBER; ?>"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>gemstone_core/js/chartist-plugin-tooltip.js?v=<?php echo GEMSTONE_VERSION_NUMBER; ?>"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>gemstone_core/js/core.js?v=<?php echo GEMSTONE_VERSION_NUMBER; ?>"></script>
    <script type="module" src="<?php echo base_url() ?>gemstone_core/js/gs-firebase.js?v=<?php echo GEMSTONE_VERSION_NUMBER; ?>"></script>
    <?php
    if (isset($gemstone_js_list) && is_array($gemstone_js_list)) {
        foreach ($gemstone_js_list as $js) {
            echo "<script type='text/javascript' src='" . base_url() . $js . "?v=" . PROJECT_BUILD_VERSION . "'></script>";
        }
    }
    ?>
    <script type="text/javascript">
        $(document).ready(function() {
            let viewPage = "view_dashboard?first_page=true";
            let lastPage = "<?php echo $this->session->last_page; ?>";

            let page = viewPage;
            if (lastPage != "") {
                page = lastPage;
            }
            Gemstone.navigate(page);
        });
    </script>
</body>

</html>