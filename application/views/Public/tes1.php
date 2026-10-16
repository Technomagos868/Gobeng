<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/theme.css">
  <meta charset="UTF-8">
  <title>Navbar</title>
  <style>
    /* ====== STYLE DASAR ====== */
    body {
      margin: 0;
      font-family: sans-serif;
    }

    .navbar {
      background-color: var(--color-orange);
      overflow: hidden;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
    }

    .navbar .logo {
      color: white;
      font-size: 20px;
      font-weight: bold;
      text-decoration: none;
    }

    .navbar ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
    }

    .navbar li {
      margin-left: 20px;
    }

    .navbar a {
      text-decoration: none;
      color: white;
      padding: 8px 12px;
      transition: background 0.3s;
    }

    .navbar a:hover {
      background-color: #555;
      border-radius: 4px;
    }

    /* ====== RESPONSIVE (opsional) ====== */
    @media (max-width: 600px) {
      .navbar ul {
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
      }

      .navbar li {
        margin: 10px 0;
      }
    }
  </style>
</head>
<body>

  <div class="navbar">
    <a href="#" class="logo">GonzCalender</a>
    <ul>
      <li><a href="#">Beranda</a></li>
      <li><a href="#">Tentang</a></li>
      <li><a href="#">Layanan</a></li>
      <li><a href="#">Kontak</a></li>
    </ul>
  </div>

</body>
</html>