<?php
/* 
    Bootstrap
    Flex - .d-flex .align-items-center .justify-content-center
    Sizing - vh-100, w-25/50/75/100
    Spacing - m, p, mt, mb, ms, me, mx, my
    Grid - .container, .row, .col
    Text - text-start, text-center, text-end
    Borders - border, border-primary, rounded
    Cards - <div class="card">
    Alerts - alert, alert-success, alert-danger
    Forms - form-control
*/
?>

<body class="vh-100">

<div class="container-fluid vh-100">
  <div class="row h-100">
    <div class="col-md-6 d-flex flex-column justify-content-center p-5 bg-white">
      <h2 class="mb-4 text-center">Login</h2>

      <form action="<?= base_url('login')?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= set_value('email') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control">
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

        <button type="submit" class="btn btn-primary w-100">Login</button>

        <p class="mt-3 text-center">
          Don’t have an account? 
          <a href="<?= base_url('register') ?>">Register</a>
        </p>
      </form>
    </div>

    <div class="col-md-6 d-flex align-items-center justify-content-center bg-light">
      <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
          <?= session()->getFlashdata('success') ?>
        </div>
      <?php endif; ?>

      <?php if(session()->getFlashdata('error')): ?>
          <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
          </div>
      <?php endif; ?>

      <?php if(isset($validation)): ?>
        <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
      <?php endif; ?>
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