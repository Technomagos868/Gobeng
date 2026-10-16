<section class="tmg-section-wrapper footer">
    <div class="tmg-footer-map">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.238389575536!2d106.8198677!3d-6.2777203!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f2218fc4eb3b%3A0xe0ad8eff68ec4b59!2sSMA%20Kolese%20Gonzaga!5e0!3m2!1sen!2sid!4v169234567890"
            width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy">
        </iframe>
    </div>
    <div class="tmg-footer-kontak">
        <h3>Kontak Kami</h3>
        <p><i class="fas fa-envelope"></i> Tobyevans@gmail.com</p>
        <p><i class="fas fa-envelope"></i> Joel.abner0005@gmail.com</p>
        <p><i class="fas fa-envelope"></i> Patrick.edgar@gmail.com</p>
        <p><b><i class="fas fa-map-marker-alt"></i> Jl. Pejaten Barat, Ragunan, Jakarta Selatan</b></p>
    </div>
    <div class="tmg-footer-sosmed">
        <h3>Ikuti Kami</h3>
        <a href="https://www.gonzaga.sch.id/"><i class="foot-Gonzaga"><img src="<?php echo base_url("assets/image/logo-instagram.png"); ?>" alt=""></i></a>
        <a href="https://www.instagram.com/technomagos.gonzaga/"><img src="<?php echo base_url("assets/image/logo-Gonzaga.webp"); ?>" alt=""><i class="foot-instagram"></i></a>
    </div>
</section>
<?php
if (isset($js_list)) {
    foreach ($js_list as $js) {
        echo "<script type='text/javascript' src='" . base_url("assets/js/" . $js) . "'>";
    }
}
?>
</body>

</html>