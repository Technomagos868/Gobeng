<section class="tmg-section-wrapper-list">

  <div class="tmg-section-wrapper-list bidang">
    <div class="tmg-list-btn">
      <button onclick="tampilkan('Listkelas')">List Kelas</button>
      <button onclick="tampilkan('Listmapel')">List Mata Pelajaran</button>
    </div>
  </div>

  <div class="tmg-wrapper-list Kelas">
    <div class="tmg-kelas-dropdown" id="Listkelas" style="display:none;">

      <div class="tmg-list-btn">
        <button onclick="tampilkan('kelas10')">Kelas 10</button>
        <button onclick="tampilkan('kelas11')">Kelas 11</button>
        <button onclick="tampilkan('kelas12')">Kelas 12</button>
      </div>

      <div class="tmg-list-btn" id="kelas10" style="display:none;">
        <button onclick="tampilkan('X1')">X1</button>
        <button>X2</button><button>X3</button>
        <button>X4</button><button>X5</button>
        <button>X6</button><button>X7</button>
        <button>X8</button>
      </div>

      <div class="tmg-list-btn" id="kelas11" style="display:none;">
        <button>XI1</button><button>XI2</button>
        <button>XI3</button><button>XI4</button>
        <button>XI5</button><button>XI6</button>
        <button>XI7</button><button>XI8</button>
      </div>

      <div class="tmg-list-btn" id="kelas12" style="display:none;">
        <button>XII1</button><button>XII2</button>
        <button>XII3</button><button>XII4</button>
        <button>XII5</button><button>XII6</button>
        <button>XII7</button><button>XII8</button>
      </div>

    </div>
  </div>

  <div class="tmg-list-btn" id="Listmapel" style="display:none;">
    <button>Matematika</button><button>Ekonomi</button>
    <button>Biologi</button><button>Informatika</button>
    <button>Sosiologi</button><button>Bahasa Mandarin</button>
    <button>Kimia</button><button>Bahasa Jepang</button>
    <button>Bahasa Inggris TL</button><button>Bahasa Jerman</button>
  </div>

  <div class="tmg-list-tabelnama" id="X1" style="display:none;">
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Absen</th>
        </tr>
      </thead>

      <tbody>
        <?php for ($i = 1; $i <= 9; $i++): ?>
          <tr>
            <td><?= $i ?></td>
            <td>...</td>
            <td>...</td>
          </tr>
        <?php endfor; ?>
      </tbody>
    </table>
  </div>

</section>

<script>
  function tampilkan(id) {
    const element = document.getElementById(id);

    if (!element) return;

    element.style.display =
      (element.style.display === "block") ? "none" : "block";
  }
</script>
