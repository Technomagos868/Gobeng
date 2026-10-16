   <div class="tmg-signin-body">
       <h1>Login</h1>
       <?php
        echo form_open('Students/login');
        ?>
       <label for="login">Email</label>
       <input class="gc-input" type="email" id="login" name="student_email" placeholder="mail@gonzaga.sch.id" required>
       <label for="password">Password</label>
       <input class="gc-input" type="password" id="password" name="student_password" required><br>
       <button class="gc-button" type="submit">Login</button>
       <?php echo form_close(); ?>
   </div>