<?php
    // Temporary sa to see the sidebar in making the Design
    include  'app\Views\reusables\sideBar.php'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <title> Dashboard</title>
</head>
<body class="d-flex">
    <div class="container p-3">
        <h3>Dashboard</h3>
        <h4 class="mt-3 text-muted">Welcome, <?= esc($role) ?>!</h4>
    
        <div class="row">
            <?php if($role == 'student'): ?>

             <div class="col-md-4">
                <div class="card mt-5">
                    <div class="card-body">
                        <h4 class="card-title">Subject 1</h4>
                        <h6 class="card-subtitle mb-2 text-muted">Subtitle?</h6>
                    </div>
                </div>
            </div>
            <?php elseif($role == 'teacher'): ?>
            <div class="col-md-3 ms-5 mt-4">
                <h4>ahhhh</h4>
                </div>
            </div>
            <?php elseif($role == 'admin'): ?>
            <div class="col-md-3 ms-5 mt-4">
                <h4>ahhhh</h4>
                </div>
            <?php endif; ?>
        </div>
        

    </div>
</body>
</html>