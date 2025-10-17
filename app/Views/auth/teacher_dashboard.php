<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <!-- same lng sa admin_dashboard.php -->
  <div class="container mt-5">
    <?php if(session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
      </div>
    <?php endif; ?>

    <div class="card shadow-sm">
      <div class="card-body text-center p-5">
        <h1 class="fw-bold">Welcome</h1>
        <h3 class="mt-3 text-muted">Hello, <?= session()->get('name') ?>!</h3>
        <a  href="<?= base_url('logout')?>" class="btn btn-danger rounded-pill">Logout</a>
      </div>
    </div>
  </div>

</body>
</html>
