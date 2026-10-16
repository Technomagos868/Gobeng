    <section class="tmg-section-wrapper profil">
      <div class="tmg-profil-nama">
        <h3>Nama : Tobby Evans Saputra</h3>
        <h3>Kelas : XI Berapa Kek</h3>
        <h3>Absen : 300 </h3>
        <h3>Angkatan : 38</h3>
      </div>

      <div class="tmg-profil-foto">
        <img src="<?php echo base_url('assets/image/fotoorang.jpg'); ?>" alt="Foto Profil">
      </div>
    </section>

    <section class="tmg-profil-wrapper agenda"> 
      <h2>Agenda Tugas & Ulangan</h2>
      <ul class="tmg-agenda-list">
        <li>
          <span class="judul">Matematika - Ulangan Bab 3</span>
          <span class="tanggal">10 Oktober 2025</span>
        </li>
        <li>
          <span class="judul">Bahasa Inggris - PR Halaman 12</span>
          <span class="tanggal">12 Oktober 2025</span>
        </li>
        <li>
          <span class="judul">Kimia - Laporan Praktikum</span>
          <span class="tanggal">15 Oktober 2025</span>
        </li>
      </ul>
        <li>
          <button class="tmg-agenda-addtugas" onclick="tampilkan('addtugas')"> + Add Tugas</button>
        </li>
      <div id="addtugas" class="isi-addtugas">
        <?php include "addtugas.php"; ?>
      </div>
    </section> 


    <script>
      function tampilkan(id) {
        let el = document.getElementById(id);
        if (el.style.display === "block") {
          el.style.display = "none";
        } else {
          el.style.display = "block";
        }
      }
    </script>
  </body>
</html>
