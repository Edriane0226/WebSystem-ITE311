<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LMS Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex">

  <!-- Sidebar -->
  <div class="d-flex flex-column flex-shrink-0 p-3 bg-dark w- vh-100">
    <h4 class="mt-3 text-white">Learning Management</h4>
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <a href="#" class="nav-link active mt-4">Dashboard</a>
      </li>
    </ul>
    <hr>
    <a href="<?= base_url('logout')?>" class="nav-link text-danger">Logout</a>
  </div>
</body>
</html>
