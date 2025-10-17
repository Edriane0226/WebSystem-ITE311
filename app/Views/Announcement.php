<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcements</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

  <div class="container mt-5">
    <div class="card shadow-sm">
      <div class="card-body text-center p-5">
        <h1 class="fw-bold">Welcome</h1>
        <h3 class="mt-3 text-muted">Hello, <?= session()->get('name') ?>!</h3>
        <a  href="<?= base_url('logout')?>" class="btn btn-danger rounded-pill">Logout</a>
        <h1 class="mb-4">Announcements</h1>
        <?php if (!empty($announcements)): ?>
            <?php foreach ($announcements as $announcement): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?= esc($announcement['title']) ?></h5>
                        <p class="card-text"><?= esc($announcement['content']) ?></p>
                        <p class="card-text"><small class="text-muted">Posted on <?= date('F j, Y, g:i a', strtotime($announcement['created_at'])) ?></small></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">
                No announcements available.
            </div>
        <?php endif; ?>
    </div>
  </div>

</body>
</html>