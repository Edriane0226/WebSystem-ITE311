<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <title>Navigation Bar</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid d-flex justify-content-between align-items-center">
         <a class="navbar-brand">NavBar</a>
            <ul class="navbar-nav flex-row ms-auto">
                <li class="nav-item mx-2">
                    <a href="#" class="nav-link"> <i class="fa-brands fa-github"></i> </a>
                </li>
                <li class="nav-item mx-2">
                    <a href="#" class="nav-link"> <i class="fa-brands fa-reddit"></i> </a>
                </li>
                <li class="nav-item mx-2">
                    <a href="#" class="nav-link"> <i class="fa-brands fa-stack-overflow"></i> </a>
                </li>
            </ul>

            <ul class="nav nav-underline">
                <li class="nav-item mx-4">
                    <a href="#" class="nav-link text-white" aria-current="page"> <i class="fas fa-home"></i> Home</a>
                </li>
                <li class="nav-item mx-4">
                    <a href="#" class="nav-link text-white"> <i class="fas fa-box"></i> Package</a>
                </li>
                <li class="nav-item mx-4">
                    <a href="#" class="nav-link text-white"> <i class="fa-solid fa-envelope"></i> Contact</a>
                </li>
            </ul>
                <ul class="navbar-nav">
                <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" id="Dropdown" role="button" data-bs-toggle="dropdown" 
                        aria-expanded="false"> <i class="fa-solid fa-user"></i> </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown">
                    <li><a href="#" class="dropdown-item">Account</a></li>
                    <li><a href="#" class="dropdown-item">Settings</a></li>
                    <li><a href="#" class="dropdown-item">Logout</a></li>

                </ul>
                </li>
                </ul>

        </div>
    </nav>

</body>
</html>