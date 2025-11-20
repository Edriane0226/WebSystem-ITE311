
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Relocated CSRF Token in header -->
  <meta name="csrf-token-name" content="<?= csrf_token() ?>">
  <meta name="csrf-token-value" content="<?= csrf_hash() ?>">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"> 
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

  <!-- Sidebar gyud siya sir sayang man -->
  <?php if(session()->get('isLoggedIn') == true):?>
  <div class="d-flex flex-column flex-shrink-0 p-3 bg-dark" style="width: 160px;">
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

  <div class="bg-light flex-grow-1 p-3">
    <ul class="navbar-nav ms-auto">
      <li class="nav-item dropdown position-relative">
        <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-bell fs-4"></i>
          <span id="notifBadge" class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" style="display: none;">0</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationDropdown" id="notifDropdown">
          <li class="dropdown-header text-center fw-bold">Notifications</li>
          <li><hr class="dropdown-divider"></li>
          <div id="notifList" class="px-2" style="max-height: 300px; overflow-y: auto;">
            <p class="text-center text-muted mb-0">No new notifications</p>
          </div>
        </ul>
      </li>
    </ul>
  </div>

  <script>
    $(document).ready(function() {

      function loadNotifications() {
        //get siyag nitfication
        $.get("<?= base_url('notifications') ?>", function(data) {
          const notifList = $("#notifList");
          const badge = $("#notifBadge");

          notifList.empty();

          if (data.notifications.length > 0) {
            // Unread Counter sa badge
            if (data.count > 0) {
              badge.text(data.count).show();
            } else {
              badge.hide();
            }

            // put unread and read notifs in separate arrays
            const unreadNotifs = data.notifications.filter(n => n.is_read == 0);
            const readNotifs = data.notifications.filter(n => n.is_read == 1);

            // display unread notifs always at the top of read notifs
            unreadNotifs.forEach(function(n) {
              notifList.append(`
                <div class="alert alert-info d-flex justify-content-between align-items-center py-2 px-3 mb-2">
                  <span>${n.message}</span>
                  <button class="btn btn-sm btn-outline-danger mark-read" data-id="${n.id}">
                    Mark as Read
                  </button>
                </div>
              `);
            });

            // then show already read notifs
            readNotifs.forEach(function(n) {
              notifList.append(`
                <div class="alert alert-secondary d-flex justify-content-between align-items-center py-2 px-3 mb-2">
                  <span>${n.message}</span>
                  <small class="text-muted">Read</small>
                </div>
              `);
            });

          } else {
            badge.hide();
            notifList.html('<p class="text-center text-muted mb-0">No notifications yet</p>');
          }
        });
      }
      //on click sa mark as read button
     $(document).on("click", ".mark-read", function() {
        const notifId = $(this).data("id");

        $.ajax({
          url: "<?= base_url('notifications/mark_read') ?>/" + notifId,
          type: "POST",
          data: {
            //Added the token and CSRF hash para dli nako mag excecpt sa filters
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>' 
          },
          success: function() {
            loadNotifications();
          },
          error: function(xhr) {
            console.error("Samthing went whrong:", xhr.responseText);
          }
        });
      });

      setInterval(loadNotifications, 5000); // 60s dapat gi 5s lang for nako for testing
    });

  </script>

<?php elseif(session()->get('isLoggedIn') == false):?>
<div>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid d-flex justify-content-between align-items-center">
         <a class="navbar-brand">Learning Management System</a>
            <!-- <ul class="navbar-nav flex-row ms-auto">
                <li class="nav-item mx-2">
                    <a href="#" class="nav-link"> <i class="fa-brands fa-github"></i> </a>
                </li>   
                <li class="nav-item mx-2">
                    <a href="#" class="nav-link"> <i class="fa-brands fa-reddit"></i> </a>
                </li>
                <li class="nav-item mx-2">
                    <a href="#" class="nav-link"> <i class="fa-brands fa-stack-overflow"></i> </a>
                </li>
            </ul> -->

            <ul class="nav nav-underline">
                <li class="nav-item mx-4">
                    <a href="<?= base_url('home')?>" class="nav-link text-white"> <i class="fas fa-home"></i> Home</a>
                </li>
                <li class="nav-item mx-4">
                    <a href="<?= base_url('about')?>" class="nav-link text-white"> <i class="fa-solid fa-info"></i> About</a>
                </li>
                <li class="nav-item mx-4">
                    <a href="<?= base_url('contact')?>" class="nav-link text-white"> <i class="fa-solid fa-envelope"></i> Contact</a>
                </li>
                <li class="nav-item mx-4">
                    <a class="btn btn-outline-light" href="<?= base_url('login') ?>" role="button">Login</a>
                </li>
            </ul>
                <!-- <ul class="navbar-nav">
                <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" id="Dropdown" role="button" data-bs-toggle="dropdown" 
                        aria-expanded="false"> <i class="fa-solid fa-user"></i> </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown">
                    <li><a href="#" class="dropdown-item">Account</a></li>
                    <li><a href="#" class="dropdown-item">Settings</a></li>
                    <li><a href="#" class="dropdown-item">Logout</a></li>

                </ul>
                </li>
                </ul> -->
        </div>
    </nav>
</div>
<?php endif; ?>
</body>
</html>
