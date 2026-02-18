<body>
<div class="container-fluid vh-100">
  <div class="row h-100">

    <div class="col-md-6 d-flex align-items-center justify-content-center bg-light">
      <?php if(isset($validation)): ?>
        <div class="alert alert-danger"><?= $validation->listErrors()?></div>
      <?php endif;?>

       <!-- ADD THIS -->
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    </div>

    <div class="col-md-6 d-flex flex-column justify-content-center p-5 bg-white">
      <h3 class="text-center mb-4">Register</h3>

      <form action="<?= base_url('register') ?>" method="post">
        <?= csrf_field() ?>
          <!-- <div class="mb-3">
              <label class="form-label">First Name</label>
              <input type="text" name="FirstName" class="form-control" value="<?= set_value('FirstName') ?>">
          </div>
          <div class="mb-3">
              <label class="form-label">Last Name</label>
              <input type="text" name="LastName" class="form-control" value="<?= set_value('LastName') ?>">
          </div>
          <div class="mb-3">
              <label class="form-label">Middle Name</label>
              <input type="text" name="MiddleName" class="form-control" value="<?= set_value('MiddleName') ?>">
          </div> -->

          <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="name" id="name" class="form-control" value="<?= set_value('name') ?>">
          </div>
          <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" id="email" class="form-control" value="<?= set_value('email') ?>">
          </div>
          <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
              <label class="form-label">Confirm Password</label>
              <input type="password" name="password_confirm" class="form-control" required>
          </div>

          <!-- CAPTCHA -->
          <div class="mb-3 text-center">
              <canvas id="captchaCanvas" width="200" height="70"></canvas>
          </div>

          <div class="mb-3">
              <input type="text" name="captcha_input" id="captchaInput" 
                    class="form-control" placeholder="Type the Letters and Numbers you see" required>
          </div>

          

          <div class="text-center mb-3">
              <button type="button" onclick="generateCaptcha()" class="btn btn-secondary btn-sm">
                  Refresh CAPTCHA
              </button>
          </div>
          <!-- <div class="mb-3">
              <label class="form-label">Category</label>
              <select name="role" class="form-control">
                  <option value="">-- Select Role --</option>
                  <option value="student" >Student</option>
                  <option value="admin" >Admin</option>
              </select>
          </div> -->
          <div class="d-grid">
              <button type="submit" class="btn btn-primary">Register</button>
          </div>
          <div class="text-center mt-3">
              <a href="<?= base_url('login') ?>" class="btn btn-link">Already have an account?</a>
          </div>
      </form>
    </div>

  </div>
</div>
</body>
</html>

<script>
  let captchaCode = "";

  function generateCaptcha() {
      fetch("<?= base_url('generate-captcha') ?>")
          .then(response => response.json())
          .then(data => {
              captchaCode = data.captcha;
              drawCaptcha(captchaCode);
          });
  }

  function drawCaptcha(code) {
    const canvas = document.getElementById("captchaCanvas");
    const ctx = canvas.getContext("2d");

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = "#f2f2f2";
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    ctx.font = "20px Arial";

    for (let i = 0; i < code.length; i++) {
        ctx.save();

        //Move to letter position
        ctx.translate(20 + i * 30, 40);

        // Slight wavy distortion
        const dx = Math.random() * 2;
        const dy = Math.random() * 2;

        // Random dark color
        ctx.fillStyle = "#aeaeaeff";

        // Draw letter slightly off-center
        ctx.fillText(code[i], dx, dy);

        ctx.restore();
    }

    // Add random lines
    for (let i = 0; i < 5; i++) {
        ctx.strokeStyle = "#aeaeaeff";
        ctx.beginPath();
        ctx.moveTo(Math.random() * canvas.width, Math.random() * canvas.height);
        ctx.lineTo(Math.random() * canvas.width, Math.random() * canvas.height);
        ctx.stroke();
    }

    ctx.filter = "blur(500px)";
  }

  generateCaptcha();
</script>