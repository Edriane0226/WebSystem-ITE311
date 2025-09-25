<?php
    include 'app/Views/templates/headerOld.php';
?>

<body>
<div class="container-fluid vh-100">
  <div class="row h-100">

    <div class="col-md-6 d-flex align-items-center justify-content-center bg-light">
      <?php if(isset($validation)): ?>
        <div class="alert alert-danger"><?= $validation->listErrors()?></div>
      <?php endif;?>
    </div>

    <div class="col-md-6 d-flex flex-column justify-content-center p-5 bg-white">
      <h3 class="text-center mb-4">Register</h3>

      <form action="<?= base_url('register') ?>" method="post">
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
