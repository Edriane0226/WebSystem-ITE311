<?php
    include 'app/Views/reusables/sideBar.php';
?>
<header>
    <?php if(session()->getFlashdata('success')): ?>
         <div class="alert alert-success ">
              <?= session()->getFlashdata('success') ?>
         </div>
    <?php endif; ?>
</header>
<body>
    <h2 class="p-3">Dashboard</h2>
</body>