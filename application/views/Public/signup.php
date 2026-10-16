<div class="gc-form-container">
    <h1>Registrasi </h1>

    <?php echo form_open('Students/signup'); ?>

    <div class="gc-input-group">
        <label for="Nama">Nama</label>
        <input class="gc-input" type="text" id="Nama" name="student_name" placeholder="Input name here.." required>
    </div>
    <div class="gc-input-group">
        <label for="Kelas">Kelas</label>
        <select class="gc-input" name="class_id" id="kelas" aria-placeholder="Pilih Kelas Anda" required>
            <?php
            foreach ($class_list as $class) {
                echo "<option value='{$class->class_id}'>{$class->class_name}</option>";
            }
            ?>
        </select>
    </div>
    <div class="gc-input-group">
        <label for="Absen">Nomor Presensi Kelas</label>
        <input class="gc-input" type="text" id="Absen" name="student_number" placeholder="Input student number here.."
            max="40" min="0" required>
    </div>
    <div class="gc-input-group">
        <label for="Email">Email</label>
        <input class="gc-input" type="email" id="Email" name="student_email" placeholder="Input email here.." required>
    </div>
    <div class="gc-input-group">
        <label for="Password">Password</label>
        <input class="gc-input" type="password" id="Password" name="student_password" required>
    </div>
    <div class="gc-input-group">
        <label for="mapelpilihan">Mata Pelajaran Pilihan</label>
        <div>
            <select class="gc-input" name="mapelpilihan" id="mapelpilihan" aria-placeholder="tentukan mata pemilihan"
                required>
                <option value="x1">Belum ada</option>
                <option value="BINDO">Bahasan indonesia TL</option>
                <option value="BJEP">Bahasa Jepang TL</option>
                <option value="BMAN">Bahasa Mandarin TL</option>
                <option value="BING">Bahasa Inggris TL</option>
                <option value="KIMIA">Kimia</option>
                <option value="FISIKA">Fisika</option>
                <option value="BIOLOGI">Biologi</option>
                <option value="MATEMATIKA">Matematika TL</option>
                <option value="EKO">Ekonomi </option>
                <option value="SOSIO">Sosiologi</option>
                <option value="BJER">Bahasa Jerman</option>
            </select>
            <select class="gc-input" name="mapelpilihan" id="mapelpilihan" aria-placeholder="tentukan mata pemilihan"
                required>
                <option value="x1">Belum ada</option>
                <option value="BINDO">Bahasan indonesia TL</option>
                <option value="BJEP">Bahasa Jepang TL</option>
                <option value="BMAN">Bahasa Mandarin TL</option>
                <option value="BING">Bahasa Inggris TL</option>
                <option value="KIMIA">Kimia</option>
                <option value="FISIKA">Fisika</option>
                <option value="BIOLOGI">Biologi</option>
                <option value="MATEMATIKA">Matematika TL</option>
                <option value="EKO">Ekonomi </option>
                <option value="SOSIO">Sosiologi</option>
                <option value="BJER">Bahasa Jerman</option>
            </select>
            <select class="gc-input" name="mapelpilihan" id="mapelpilihan" aria-placeholder="tentukan mata pemilihan"
                required>
                <option value="x1">Belum ada</option>
                <option value="BINDO">Bahasan indonesia TL</option>
                <option value="BJEP">Bahasa Jepang TL</option>
                <option value="BMAN">Bahasa Mandarin TL</option>
                <option value="BING">Bahasa Inggris TL</option>
                <option value="KIMIA">Kimia</option>
                <option value="FISIKA">Fisika</option>
                <option value="BIOLOGI">Biologi</option>
                <option value="MATEMATIKA">Matematika TL</option>
                <option value="EKO">Ekonomi </option>
                <option value="SOSIO">Sosiologi</option>
                <option value="BJER">Bahasa Jerman</option>
            </select>
            <select class="gc-input" name="mapelpilihan" id="mapelpilihan" aria-placeholder="tentukan mata pemilihan"
                required>
                <option value="x1">Belum ada</option>
                <option value="BINDO">Bahasan indonesia TL</option>
                <option value="BJEP">Bahasa Jepang TL</option>
                <option value="BMAN">Bahasa Mandarin TL</option>
                <option value="BING">Bahasa Inggris TL</option>
                <option value="KIMIA">Kimia</option>
                <option value="FISIKA">Fisika</option>
                <option value="BIOLOGI">Biologi</option>
                <option value="MATEMATIKA">Matematika TL</option>
                <option value="EKO">Ekonomi </option>
                <option value="SOSIO">Sosiologi</option>
                <option value="BJER">Bahasa Jerman</option>
            </select>
        </div>
        <form action="upload.php" method="POST" enctype="multipart/form-data" required>
            <label>Masukkan fotomu</label>
            <input type="file" name="foto" accept="image/*" required>
        </form>
    </div>
    <button class="gc-button submit" type="submit">Submit</button>
    
    <p class="gc-form-message">Already have an account? <a href="<?php echo base_url("coralis/login"); ?>">Log In</a></p>
    
    <?php echo form_close(); ?>
</div>