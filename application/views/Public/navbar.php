<html>

<head>
  <link rel="icon" href="../assets/image/logo_techomagos.jpeg">
  <title><?php echo empty($page_title) ? "Te" : $page_title; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/theme.css">
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/navbar.css">
  <?php
  if (isset($css_list)) {
    foreach ($css_list as $css) {
      echo "<link rel='stylesheet' href='" . base_url("assets/css/" . $css) . "'>";
    }
  }
  ?>
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/footer.css">
</head>

<body>
  <nav class="tmg-section-wrapper navbar">
    <div class="navbar-container">
      <div class="navbar-logo">
        <a class="item" href="<?php echo base_url(); ?>">
          <img src="<?php echo base_url('assets/image/logo_techomagos.jpeg'); ?>" alt="Technomagos Logo">
        </a>
      </div>
      <div class="tmg-navbar-button-group">
        <a href="<?php echo base_url("coralis/list"); ?>">What is GOBENG?</a>
        <a href="<?php echo base_url("coralis/tentang"); ?>">Ride List</a>
        <a href="<?php echo base_url("coralis/login"); ?>">Host a Ride</a>
      </div>
    </div>
  </nav>