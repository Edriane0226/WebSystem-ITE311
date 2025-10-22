<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"> 
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="d-flex">

  <!-- Sidebar gyud siya sir sayang man -->
  <div class="d-flex flex-column flex-shrink-0 p-3 bg-dark vh-100" style="width: 160px;">
    <h4 class="text-white text-center mb-5">LMS</h4>

    <ul class="nav nav-pills flex-column mb-auto">
      <?php if ($role == 'admin'): ?>
        <li class="nav-item">
          <a href="<?= base_url('dashboard') ?>" class="nav-link text-white bg-info rounded px-3">
            Dashboard
          </a>
        </li>

      <?php elseif ($role == 'teacher'): ?>
        <li class="nav-item">
          <a href="<?= base_url('dashboard') ?>" class="nav-link text-white bg-info rounded px-3">
            Dashboard
          </a>
        </li>

      <?php elseif ($role == 'student'): ?>
        <li class="nav-item">
          <a href="<?= base_url('dashboard') ?>" class="nav-link text-white bg-info rounded px-3">
            Dashboard
          </a>
        </li>
      <?php endif; ?>

      <?php if ($role == 'admin'): ?>
        <li class="nav-item">
          <!-- # sa -->
          <a href="#" class="nav-link text-white bg-info rounded px-3 mt-3">
            Manage Users
          </a>
        </li>

      <?php elseif ($role == 'teacher'): ?>
        <li class="nav-item">
          <!-- # sa -->
          <a href="#" class="nav-link text-white bg-info rounded px-3 mt-3">
             Manage Students
          </a>
        </li>
      <?php endif; ?>
    </ul>

    <hr>
    <a href="<?= base_url('logout')?>" class="nav-link text-danger">Logout</a>
  </div>

  <div class="bg-light" style="width: 200px;">
    <ul class="navbar-nav ms-auto">
          <li class="nav-item position-relative me-3 mt-3">
            <!-- # temporary sa -->
            <a class="nav-link" href="#">
              <i class="bi bi-bell fs-5 p-3"></i>
              <?php if (!empty($notificationCount) && $notificationCount > 0): ?>
                <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">
                  <?= $notificationCount ?>
                </span>
              <?php endif; ?>
            </a>
          </li>
        </ul>
  </div>

</body>
</html>
