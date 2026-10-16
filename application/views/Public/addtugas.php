<html>

<head>
    <style>
  * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Lucida Sans', 'Lucida Grande', Verdana, sans-serif;
}


/* FORM WRAPPER */
.Nasi {
  background-color: var(--color-orange);
  max-width: 500px;    
  width: 100%;
  margin: 40px auto;       
  padding: 20px;
  border-radius: 12px;
  display: grid;
  gap: 15px;
}

h1 {
  text-align: center;
  padding: 10px;
  border-radius: 20px;
  margin-bottom: 10px;
}

/* INPUTS */
input[type=email],
input[type=password],
select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-family: Arial, Helvetica, sans-serif;
}

/* SUBMIT BUTTON */
.tmg-submit {
  background-color: #00cc00;
  border: none;
  color: white;
  padding: 14px 24px;
  font-size: 16px;
  border-radius: 25px;
  cursor: pointer;
  transition: 0.3s;
  width: 100%;
  margin-top: 10px;
}

.tmg-submit:hover {
  opacity: 0.7;
}

@media (max-width: 600px) {
  .tmg-signin-body{
    padding: 15px;
  }

  h1 {
    font-size: 1.2rem;
  }
}


    </style>
</head>

<body>
    <div class="tmg-signin-body">
        <h1>Tambah Assignment</h1>
            <?php
            echo form_open('Students/addtugas');
                ?>
                <label for="judul">Judul Assignment</label>
                <input type="text" id="assignment_name" name="assignment_name" placeholder="Tugas" required  style="width: 200pxpx; height: 20px;">
                <label for="tanggal"> Tanggal Assignment</label>
                <input type="date" id="tanggal" name="assignment_due" required >
                <label for="mapel" id="mapel" >Matapelajaran</label>
                <select class="gc-input" name="subject_id" id="subject_id" aria-placeholder="Tentukan MataPelajaran Tugas"
                required>
                <option value="x1">Belum ada</option>
                <option value="Bahasa Indonesia (TL)">Bahasan indonesia TL</option>
                <option value="Bahasa Jepang">Bahasa Jepang TL</option>
                <option value="Bahasa Mandarin">Bahasa Mandarin TL</option>
                <option value="Bahasa Inggris (TL)">Bahasa Inggris TL</option>
                <option value="Kimia">Kimia</option>
                <option value="Fisika">Fisika</option>
                <option value="Biologi">Biologi</option>
                <option value="Matematika (TL)">Matematika TL</option>
                <option value="Ekonomi">Ekonomi </option>
                <option value="Sosiologi">Sosiologi</option>
                <option value="Bahasa Jerman">Bahasa Jerman</option>
                </select>
                <label for="keterangan">Keterangan</label>
                <input type="text" id="assignment_description" nama="assignment_description" required  style="width: 300px; height: 80px;"><br>
                <button type="submit"><div class="tmg-submit">Submit</div></button>
            <?php echo form_close(); ?>
        </body>
</html>