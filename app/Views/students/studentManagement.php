<?php $userRole = session()->get('role'); ?>

<div class="container-fluid p-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1">
                        <i class="bi bi-people me-2"></i>Student Management
                    </h2>
                    <p class="text-muted mb-0">
                        <?= $userRole === 'admin' ? 'Manage all students' : 'Manage enrollment for your courses' ?>
                    </p>
                </div>
                <?php if ($userRole === 'admin'): ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="bi bi-plus-lg me-1"></i>Add Student
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('message') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <?php if ($userRole === 'admin'): ?>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                    <i class="bi bi-people text-primary fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Students</h6>
                                <h4 class="mb-0"><?= isset($students) ? count($students) : 0 ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Active Enrollments</h6>
                            <h4 class="mb-0"><?= isset($activeEnrollments) ? $activeEnrollments : 0 ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Search students..." id="searchInput">
                    </div>
                </div>
                <?php if ($userRole === 'teacher'): ?>
                    <div class="col-md-6">
                        <select class="form-select" id="courseFilter">
                            <option value="">All My Courses</option>
                            <?php if (isset($teacherCourses)): ?>
                                <?php foreach ($teacherCourses as $course): ?>
                                    <option value="<?= $course['courseID'] ?>"><?= esc($course['courseTitle']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <?= $userRole === 'admin' ? 'All Students' : 'Students in My Courses' ?>
            </h5>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="studentsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 ps-4">Student ID</th>
                            <th class="border-0">Name</th>
                            <th class="border-0">Email</th>
                            <?php if ($userRole === 'admin'): ?>
                                <th class="border-0">Status</th>
                                <th class="border-0">Created</th>
                            <?php endif; ?>
                            <th class="border-0">Enrolled Courses</th>
                            <th class="border-0 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($students) && !empty($students)): ?>
                            <?php foreach ($students as $student): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold"><?= esc($student['studentID'] ?? $student['userID']) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            <?= strtoupper(substr($student['firstName'], 0, 1) . substr($student['lastName'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-medium"><?= esc($student['firstName'] . ' ' . $student['lastName']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= esc($student['email']) ?></td>
                                <?php if ($userRole === 'admin'): ?>
                                    <td>
                                        <span class="badge <?= $student['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= ucfirst($student['status'] ?? 'active') ?>
                                        </span>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($student['created_at'] ?? '')) ?></td>
                                <?php endif; ?>
                                <td>
                                    <small class="text-muted"><?= $student['enrolledCourses'] ?? 0 ?> courses</small>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <?php if ($userRole === 'admin'): ?>
                                            <button class="btn btn-sm btn-outline-primary" onclick="editStudent(<?= htmlspecialchars(json_encode($student)) ?>)" title="Edit Student">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-outline-info" onclick="manageEnrollment(<?= $student['userID'] ?>, '<?= esc($student['firstName'] . ' ' . $student['lastName']) ?>')" title="Manage Enrollment">
                                            <i class="bi bi-bookmark-plus"></i>
                                        </button>
                                        <?php if ($userRole === 'admin'): ?>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteStudent(<?= $student['userID'] ?>)" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= $userRole === 'admin' ? '7' : '5' ?>" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-people fa-3x mb-3"></i>
                                        <h5>No students found</h5>
                                        <p><?= $userRole === 'admin' ? 'Add your first student.' : 'No students enrolled in your courses.' ?></p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Student Modal (Admin Only) -->
<?php if ($userRole === 'admin'): ?>
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('students/save') ?>" method="post" id="studentForm">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="firstName" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="lastName" class="form-control" required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Save Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Student Modal (Admin Only) -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('students/update') ?>" method="post" id="editStudentForm">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" name="userID" id="edit_userID">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="firstName" id="edit_firstName" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="lastName" id="edit_lastName" class="form-control" required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Update Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Manage Enrollment Modal -->
<div class="modal fade" id="enrollmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enrollmentModalTitle">Manage Enrollment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="enrollment_studentID">
                
                <div class="row g-3">
                    <div class="col-12">
                        <h6>Available Courses</h6>
                        <div id="availableCourses">
                            <!-- Will be populated via AJAX -->
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <h6>Current Enrollments</h6>
                        <div id="currentEnrollments">
                            <!-- Will be populated via AJAX -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#studentsTable tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    <?php if ($userRole === 'admin'): ?>
    // Edit student function
    function editStudent(student) {
        document.getElementById('edit_userID').value = student.userID;
        document.getElementById('edit_firstName').value = student.firstName;
        document.getElementById('edit_lastName').value = student.lastName;
        document.getElementById('edit_email').value = student.email;
        document.getElementById('edit_status').value = student.status || 'active';
        
        new bootstrap.Modal(document.getElementById('editStudentModal')).show();
    }

    // Delete student function
    function deleteStudent(studentId) {
        if (confirm('Are you sure you want to delete this student? This action cannot be undone.')) {
            window.location.href = `<?= base_url('students/delete/') ?>${studentId}`;
        }
    }
    <?php endif; ?>

    // Manage enrollment function
    function manageEnrollment(studentId, studentName) {
        document.getElementById('enrollment_studentID').value = studentId;
        document.getElementById('enrollmentModalTitle').textContent = `Manage Enrollment - ${studentName}`;
        
        // Load enrollment data via AJAX
        fetch(`<?= base_url('students/getEnrollmentData/') ?>${studentId}`)
            .then(response => response.json())
            .then(data => {
                // Populate available courses
                let availableHTML = '';
                data.availableCourses.forEach(course => {
                    availableHTML += `
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <strong>${course.courseTitle}</strong>
                                <small class="text-muted d-block">${course.courseCode}</small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" onclick="enrollStudent(${studentId}, ${course.courseID})">
                                Enroll
                            </button>
                        </div>
                    `;
                });
                document.getElementById('availableCourses').innerHTML = availableHTML || '<p class="text-muted">No available courses</p>';
                
                // Populate current enrollments
                let enrolledHTML = '';
                data.enrolledCourses.forEach(enrollment => {
                    enrolledHTML += `
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <strong>${enrollment.courseTitle}</strong>
                                <small class="text-muted d-block">Enrolled: ${enrollment.enrollmentDate}</small>
                            </div>
                            <?php if ($userRole === 'admin' || $userRole === 'teacher'): ?>
                            <button class="btn btn-sm btn-outline-danger" onclick="unenrollStudent(${enrollment.enrollmentID})">
                                Unenroll
                            </button>
                            <?php endif; ?>
                        </div>
                    `;
                });
                document.getElementById('currentEnrollments').innerHTML = enrolledHTML || '<p class="text-muted">No current enrollments</p>';
            });
        
        new bootstrap.Modal(document.getElementById('enrollmentModal')).show();
    }

    // Enroll student function
    function enrollStudent(studentId, courseId) {
        fetch('<?= base_url('students/enroll') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: JSON.stringify({
                studentId: studentId,
                courseId: courseId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }

    // Unenroll student function
    function unenrollStudent(enrollmentId) {
        if (confirm('Are you sure you want to unenroll this student?')) {
            fetch(`<?= base_url('students/unenroll/') ?>${enrollmentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }

    // Auto-hide alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>

<style>
    .avatar {
        font-size: 14px;
        font-weight: 600;
    }
    
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
</style>