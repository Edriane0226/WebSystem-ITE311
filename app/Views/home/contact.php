<?php
    include 'app/Views/templates/headerOld.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
</head>
<body class="vh-100">
    <header class="py-5 ">
        <div class="container">
         <h1 class="fw-bold">Contact Us</h1>
        </div>
    </header>
        <div class="text-start container">
         <form action="">
            <div class="mb-3">
            <label for="Name" class="form-label">Name</label>
            <input type="email" class="form-control" id="Name">
            </div>
            <div class="mb-3">
            <label for="Email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email">
            </div>
            <div class="mb-3">
            <label for="Message" class="form-label">Message</label>
            <textarea class="form-control" rows="5" id=""></textarea>

            <button type="submit" class="btn btn-primary mt-3 float-end">Send</button>
            </div>
         </form>
        </div>
</body>
</html>