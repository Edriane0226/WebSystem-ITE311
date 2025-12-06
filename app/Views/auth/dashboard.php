<div class="container p-3">
        <h3>Dashboard</h3>
        <h4 class="mt-3 text-muted">Welcome, <?= $name ?>!</h4>
    
        <div class="row">
            <?php if( $role == 'student' ): ?>
                <div class="col-md-8">
                    <div class="card mt-5">
                        <div class="card-body">
                            <h4 class="card-title">Available Courses</h4>
                            <div class="row">
                                <?php
                                $enrollmentByCourse = [];
                                foreach ($enrollments as $enroll) {
                                    $enrollmentByCourse[$enroll['course_id']] = $enroll['statusName'];
                                }
                                ?>
                                <?php foreach ($courses as $course):?>
                                    <div class="col-md-4">
                                        <!-- remove lng ang base64_encode ug decode sa controller kung i try ang CSRF Token -->
                                        <div class="card mt-5 courseCard" data-course_id="<?= base64_encode($course['courseID']) ?>">
                                            <div class="card-body">
                                                <h4 class="card-title"><?= $course['courseTitle'] ?></h4>
                                                <h6 class="card-subtitle mb-2 text-muted"><?= $course['courseCode'] ?></h6>
                                                <p><?= $course['courseDescription'] ?></p>
                                                <p class="text-muted small">School Year: <?= $course['schoolYear'] ?></p>
                                                <p class="text-muted small">Semester: <?= $course['semesterName'] ?? $course['Semester'] ?></p>
                                                <p class="text-muted small">Schedule: <?= $course['timeSlot'] ?? 'Not Set' ?></p>
                                                   
                                                    <?php $status = $enrollmentByCourse[$course['courseID']] ?? null;?>
                                                    <?php if ($status == 'Enrolled'): ?>
                                                    <button class="btn btn-primary" disabled>Enrolled</button>
                                                    <?php elseif ($status == 'Completed'): ?>
                                                        <button class="btn btn-secondary" disabled>Completed</button>
                                                    <?php elseif ($status == 'Dropped'): ?>
                                                        <button class="btn btn-danger" disabled>Dropped</button>
                                                    <?php else: ?>
                                                        <button class="btn btn-primary enroll">Enroll</button>
                                                    <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                <h4 class="mt-5">Download Materials</h4>

                    <?php foreach ($enrollments as $enrolled): ?>
                        <?php if ($enrolled['enrollmentStatus'] == 2 || $enrolled['enrollmentStatus'] == 3) continue;?>
                        <?php if (!empty($enrolled['materials'])): ?>
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h4 class="card-title"><?= $enrolled['courseTitle'] ?></h4>
                                    <?php if (!empty($enrolled['startDate']) || !empty($enrolled['endDate'])): ?>
                                        <p class="text-muted small mb-2">
                                            <?php if (!empty($enrolled['startDate'])): ?>
                                                Starts: <?= date('M j, Y', strtotime($enrolled['startDate'])) ?>
                                            <?php endif; ?>
                                            <?php if (!empty($enrolled['endDate'])): ?>
                                                <br>Ends: <?= date('M j, Y', strtotime($enrolled['endDate'])) ?>
                                            <?php endif; ?>
                                        </p>
                                    <?php endif; ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($enrolled['materials'] as $material): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?= $material['file_name'] ?>
                                                <a href="<?= base_url('materials/download/' . $material['id']) ?>" 
                                                class="btn btn-secondary btn-sm rounded-pill">
                                                    Download
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h4 class="card-title"><?= $enrolled['courseTitle'] ?></h4>
                                    <?php if (!empty($enrolled['startDate']) || !empty($enrolled['endDate'])): ?>
                                        <p class="text-muted small mb-2">
                                            <?php if (!empty($enrolled['startDate'])): ?>
                                                Starts: <?= date('M j, Y', strtotime($enrolled['startDate'])) ?>
                                            <?php endif; ?>
                                            <?php if (!empty($enrolled['endDate'])): ?>
                                                <br>Ends: <?= date('M j, Y', strtotime($enrolled['endDate'])) ?>
                                            <?php endif; ?>
                                        </p>
                                    <?php endif; ?>
                                    <p class="text-muted mb-0">No materials uploaded yet.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>


                <div class="col-md-8">
                    <div class="card mt-5">
                        <div class="card-body">
                            <h4 class="card-title">Enrolled Courses</h4>
                            <div class="row" id="enrolledCourses">
                                <?php foreach ($enrollments as $enrolled):?>
                                <?php if ($enrolled['enrollmentStatus'] == 2 || $enrolled['enrollmentStatus'] == 3) continue;?>
                                <div class="col-md-4">
                                    <div class="card mt-5">
                                        <div class="card-body">
                                            <h4 class="card-title"><?= $enrolled['courseTitle'] ?></h4>
                                            <h6 class="card-subtitle mb-2 text-muted">Term 1</h6>
                                            <p><?= $enrolled['courseDescription'] ?></p>
                                            <?php if (!empty($enrolled['startDate']) || !empty($enrolled['endDate'])): ?>
                                                <p class="text-muted small mb-0">
                                                    <?php if (!empty($enrolled['startDate'])): ?>
                                                        Starts: <?= date('M j, Y', strtotime($enrolled['startDate'])) ?>
                                                    <?php endif; ?>
                                                    <?php if (!empty($enrolled['endDate'])): ?>
                                                        <br>Ends: <?= date('M j, Y', strtotime($enrolled['endDate'])) ?>
                                                    <?php endif; ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

           <?php elseif ($role == 'teacher'): ?>
            <div class="col-md-8">
                <div class="card mt-5 shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-3">My Courses</h4>

                        <?php if (!empty($courses)): ?>
                            <div class="list-group">
                                <?php foreach ($courses as $course): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1 p-2"><?= esc($course['courseTitle']); ?></h5>
                                        </div>
                                        <div>
                                            <a href="<?= base_url('admin/course/' . $course['courseID'] . '/upload'); ?>" 
                                            class="btn btn-primary btn-sm rounded-pill">
                                                Add Material
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center mt-3">No courses assigned yet.</p>
                        <?php endif; ?>

                        <div class="text-center mt-4">
                            <a href="<?= base_url('teacher/add-course'); ?>" class="btn btn-primary rounded-pill">
                                Add New Course
                            </a>
                        </div>
                    </div>
                </div>
            </div>

                <div class="col-md-3 ms-5 mt-4">
                    <h4>Notifications</h4>
                </div>

                <div class="col-md-8">
                    <div class="card mt-5">
                        <div class="card-body">
                            <h4 class="card-title">Enrolled Students</h4>
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
                                <!-- retrieve all users -->
                                <?php foreach ($allUsers as $user): ?>
                                <tr>
                                    <td><?= esc($user['userID']); ?></td>
                                    <td><?= esc($user['name']); ?></td>
                                    <td><?= esc($user['role']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </table> 
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                <div class="card mt-5 shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Courses</h4>

                        <?php if (!empty($courses)): ?>
                            <div class="list-group">
                                <?php foreach ($courses as $course): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1 p-2"><?= esc($course['courseTitle']); ?></h5>
                                        </div>
                                        <div>
                                            <a href="<?= base_url('admin/course/' . $course['courseID'] . '/upload'); ?>" 
                                            class="btn btn-primary btn-sm rounded-pill">
                                                Add Material
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center mt-3">No courses assigned yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

                
            <?php endif; ?>
        </div>
        

    </div>
</html>
<script>
$(".enroll").click(function(e){
    e.preventDefault();

    var bttn = $(this);
    var courseCard = bttn.closest(".courseCard");
    var courseID = courseCard.data("course_id");

    // will get CSRF token name and the hash value from meta tags
    var csrfName = $('meta[name="csrf-token-name"]').attr('content');
    var csrfHash = $('meta[name="csrf-token-value"]').attr('content');

    $.ajax({
        url: "<?= base_url('/course/enroll') ?>",
        type: "POST",
        data: { course_id: courseID, 
               [csrfName]: csrfHash },
        dataType: "json",
        success: function(data) {
            if(data.success) {
                bttn.prop("disabled", true).text("Enrolled");
                courseCard.append("<div class='alert alert-success mt-3'>" + data.message + "</div>");

                var courseTitle = courseCard.find(".card-title").text();
                var courseDescription = courseCard.find("p").text();

                $("#enrolledCourses").append(`
                    <div class="col-md-4">
                        <div class="card mt-3">
                            <div class="card-body">
                                <h4 class="card-title">${courseTitle}</h4>
                                <h6 class="card-subtitle mb-2 text-muted">Term 1</h6>
                                <p>${courseDescription}</p>
                            </div>
                        </div>
                    </div>
                `);
            } else {
                courseCard.append("<div class='alert alert-danger mt-3'>" + data.message + "</div>");
                bttn.prop("disabled", true).text("Enrolled");
            }

            if (data.csrfHash) {
                $('meta[name="csrf-token-value"]').attr('content', data.csrfHash);
            }
            },
            error: function(xhr) {
                if (xhr.status === 403) {
                    alert("Session expired or invalid CSRF. Please refresh the page.");
                }
            }
    });
});
</script>