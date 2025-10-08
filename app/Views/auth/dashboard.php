<?php dd($courses); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Tanggal nako CDN kay naa naman sa header.php-->
    <title> Dashboard</title>
</head>
<body class="d-flex">
    <div class="container p-3">
        <h3>Dashboard</h3>
        <h4 class="mt-3 text-muted">Welcome, <?= $name ?>!</h4>
    
        <div class="row">
            <?php if( $role == 'student' ): ?>
                <div class="col-md-8">
                    <div class="card mt-5">
                        <div class="card-body">
                            <h4 class="card-title">Available Courses</h4>
                            
                            <?php foreach ($courses as $course):?>
                            <div class="col-md-4">
                            <div class="card mt-5">
                                    <div class="card-body">
                                        <h4 class="card-title"><?= $course['courseTitle'] ?></h4>
                                        <h6 class="card-subtitle mb-2 text-muted">Term 1</h6>
                                        <p><?= $course['courseDescription'] ?></p>
                                        <button class="btn btn-primary">Enroll</button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 , mt-5">
                            <h5 class="mb-2 text-muted">Assignments</h5>
                        </div>
                    </div>
                </div>
            <?php elseif( $role == 'teacher' ): ?>

                <div class="col-md-8">
                    <div class="card mt-5">
                        <div class="card-body">
                            <h4 class="card-title">Subjects</h4>
                            <!-- br sa ahahha -->
                            <br><br><br><br><br><br> 
                            <button class="btn btn-primary">Add Subject</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 ms-5 mt-4">
                    <h4>Notifications</h4>
                </div>

                <div class="col-md-8">
                    <div class="card mt-5">
                        <div class="card-body">
                            <h4 class="card-title">Courses</h4>
                            <!-- br sa ahahha -->
                            <br><br><br><br><br><br> 
                            <button class="btn btn-primary">Add Course</button>
                        </div>
                    </div>
                </div>

            <?php elseif( $role == 'admin' ): ?>
                
                <h2 class="mt-3">Statistics</h2>
                <div class="col-md-6">
                    <div class="card mt-2">
                        <div class="card-body">
                            <h4 class="card-title">Total Users</h4>
                            <table class="table">
                                <tr>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                </tr>
                            </table> 
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mt-2">
                        <div class="card-body">
                            <h4 class="card-title">Total Courses</h4>
                            <table class="table">
                                <tr>
                                    <th>Course ID</th>
                                    <th>Name</th>
                                    <th>Date Published</th>
                                </tr>
                            </table> 
                        </div>
                    </div>
                </div>
                
            <?php endif; ?>
        </div>
        

    </div>
</body>
</html>