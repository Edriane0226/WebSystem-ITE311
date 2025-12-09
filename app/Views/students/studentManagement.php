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
                        <i class="bi bi-plus-lg me-1"></i>Add Users
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

    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach ((array) $errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
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
                                <h4 class="mb-0"><?= $studentCount ?? 0 ?></h4>
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
                            <h4 class="mb-0"><?= $activeEnrollments ?? 0 ?></h4>
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
                <?php $isAdmin = ($userRole === 'admin'); ?>
                <?php $records = $users ?? []; ?>
                <?php $columnCount = $isAdmin ? 7 : 5; ?>
                <table class="table table-hover mb-0" id="studentsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 ps-4"><?= $isAdmin ? 'User ID' : 'Student ID' ?></th>
                            <th class="border-0">Name</th>
                            <th class="border-0">Email</th>
                            <?php if ($isAdmin): ?>
                                <th class="border-0">Role</th>
                                <th class="border-0">Created</th>
                            <?php endif; ?>
                            <th class="border-0"><?= $isAdmin ? 'Enrolled Courses' : 'Courses in My Classes' ?></th>
                            <th class="border-0 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): ?>
                            <?php foreach ($records as $user): ?>
                                <?php
                                    $roleName = isset($user['role_name']) ? strtolower($user['role_name']) : 'student';
                                    $canManageEnrollment = $roleName === 'student';
                                    $canEditUser = $isAdmin;
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold"><?= esc($user['userID']) ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-primary text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                <?= strtoupper(substr($user['name'] ?? '', 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-medium"><?= esc($user['name'] ?? 'N/A') ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= esc($user['email'] ?? 'N/A') ?></td>
                                    <?php if ($isAdmin): ?>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?= esc(ucfirst($roleName)) ?></span>
                                        </td>
                                        <td>
                                            <?= isset($user['created_at']) && $user['created_at'] ? date('M j, Y', strtotime($user['created_at'])) : '—' ?>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <small class="text-muted"><?= (int) ($user['enrolledCourses'] ?? 0) ?> courses</small>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group" role="group">
                                            <?php if ($canEditUser): ?>
                                                <button class="btn btn-sm btn-outline-primary" onclick="editStudent(<?= htmlspecialchars(json_encode($user)) ?>)" title="Edit User">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-outline-info" data-student-id="<?= (int) $user['userID'] ?>" data-student-name="<?= esc($user['name'] ?? '') ?>" onclick="manageEnrollment(this.dataset.studentId, this.dataset.studentName)" title="Manage Enrollment" <?= $canManageEnrollment ? '' : 'disabled' ?>>
                                                <i class="bi bi-bookmark-plus"></i>
                                            </button>
                                            <?php if ($isAdmin): ?>
                                                <button class="btn btn-sm btn-outline-danger" data-student-id="<?= (int) $user['userID'] ?>" onclick="deleteStudent(this.dataset.studentId)" title="Delete User" <?= $canManageEnrollment ? '' : 'disabled' ?>>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= $columnCount ?>" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-people fa-3x mb-3"></i>
                                        <h5><?= $isAdmin ? 'No users found' : 'No students found' ?></h5>
                                        <p><?= $isAdmin ? 'Add your first user.' : 'No students enrolled in your courses.' ?></p>
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

<!-- Add Users Modal For Admin -->
<?php if ($userRole === 'admin'): ?>
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('students/addStudent') ?>" method="post" id="studentForm">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="firstName" class="form-control" required minlength="2" maxlength="50" pattern="[A-Za-z][A-Za-z\s'-]*" title="Use letters, spaces, apostrophes, or hyphens only." value="<?= esc(old('firstName')) ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="lastName" class="form-control" required minlength="2" maxlength="50" pattern="[A-Za-z][A-Za-z\s'-]*" title="Use letters, spaces, apostrophes, or hyphens only." value="<?= esc(old('lastName')) ?>">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required maxlength="100" value="<?= esc(old('email')) ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required minlength="6" maxlength="64">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="">Select Role</option>
                                <?php if (!empty($roleOptions)): ?>
                                    <?php foreach ($roleOptions as $roleOption): ?>
                                        <option value="<?= (int) $roleOption['roleID'] ?>" <?= (string) $roleOption['roleID'] === (string) old('role') ? 'selected' : '' ?>>
                                            <?= esc(ucfirst($roleOption['role_name'])) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div id="addUserValidationAlert" class="alert alert-danger d-none" role="alert"></div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Save User
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
                        
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" id="edit_role" class="form-select" required>
                                <?php if (!empty($roleOptions)): ?>
                                    <?php foreach ($roleOptions as $roleOption): ?>
                                        <option value="<?= (int) $roleOption['roleID'] ?>"><?= esc(ucfirst($roleOption['role_name'])) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
                            
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <h6>Current Enrollments</h6>
                        <div id="currentEnrollments">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const isAdmin = <?= $userRole === 'admin' ? 'true' : 'false' ?>;
    const isTeacher = <?= $userRole === 'teacher' ? 'true' : 'false' ?>;
    const currentTeacherId = <?= $userRole === 'teacher' ? (int) session()->get('userID') : 'null' ?>;
    const STATUS_ENROLLED = 1;
    const STATUS_PENDING = 4;
    const STATUS_DROPPED = 3;

    function escapeHtml(value) {
        const div = document.createElement('div');
        const normalized = value === undefined || value === null ? '' : value;
        div.textContent = normalized;
        return div.innerHTML;
    }

    // Client-side search 
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#studentsTable tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    <?php if ($userRole === 'admin'): ?>
    // Edit user function
    function editStudent(user) {
        document.getElementById('edit_userID').value = user.userID;
        const fullName = (user.name || '').trim();
        const [firstName, ...restOfName] = fullName.split(/\s+/);
        document.getElementById('edit_firstName').value = firstName || '';
        document.getElementById('edit_lastName').value = restOfName.join(' ') || '';
        document.getElementById('edit_email').value = user.email || '';
        const roleSelect = document.getElementById('edit_role');
        if (roleSelect) {
            const dynamicAdminOption = roleSelect.querySelector('option[data-dynamic-admin="true"]');
            if (dynamicAdminOption) {
                dynamicAdminOption.remove();
            }

            const roleValue = user.role !== undefined && user.role !== null ? String(user.role) : '';
            const normalizedRoleName = (user.role_name || '').toString();
            let hasOption = Array.from(roleSelect.options).some(option => option.value === roleValue);

            if (!hasOption && roleValue) {
                const option = document.createElement('option');
                option.value = roleValue;
                const labelSource = normalizedRoleName || 'Admin';
                option.textContent = labelSource.charAt(0).toUpperCase() + labelSource.slice(1);
                option.dataset.dynamicAdmin = 'true';
                roleSelect.appendChild(option);
                hasOption = true;
            }

            roleSelect.value = hasOption ? roleValue : (roleSelect.options[0] ? roleSelect.options[0].value : '');

            const isAdminRole = normalizedRoleName.toLowerCase() === 'admin';
            roleSelect.disabled = isAdminRole;
            if (isAdminRole) {
                roleSelect.title = 'Admin role cannot be changed.';
            } else {
                roleSelect.removeAttribute('title');
            }
        }
        
        new bootstrap.Modal(document.getElementById('editStudentModal')).show();
    }

    const addUserForm = document.getElementById('studentForm');
    const addUserValidationAlert = document.getElementById('addUserValidationAlert');
    

    // Delete user function
    function deleteStudent(userId) {
        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            window.location.href = `<?= base_url('students/delete/') ?>${userId}`;
        }
    }
    <?php endif; ?>

    // Manage enrollment function
    function manageEnrollment(studentId, studentName) {
        if (!studentName) {
            studentName = 'Student';
        }

        document.getElementById('enrollment_studentID').value = studentId;
        document.getElementById('enrollmentModalTitle').textContent = `Manage Enrollment - ${studentName}`;

        fetch(`<?= base_url('students/getEnrollmentData/') ?>${studentId}`)
            .then(response => response.json())
            .then(data => {
                console.log('Enrollment data response:', data);
                if (data.error) {
                    alert('Error: ' + data.error);
                    return;
                }

                const availableContainer = document.getElementById('availableCourses');
                const enrolledContainer = document.getElementById('currentEnrollments');
                const studentIdInt = parseInt(studentId, 10);

                if (isAdmin) {
                    let availableHTML = '';

                    if (Array.isArray(data.availableCourses) && data.availableCourses.length) {
                        data.availableCourses.forEach(course => {
                            const courseId = parseInt(course.courseID, 10);
                            availableHTML += `
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <strong>${escapeHtml(course.courseTitle || 'Untitled Course')}</strong>
                                        <small class="text-muted d-block">${escapeHtml(course.courseCode || '')}</small>
                                        <small class="text-muted d-block">${escapeHtml(course.schoolYear || '')}${course.semesterName ? ' • ' + escapeHtml(course.semesterName) : ''}</small>
                                        <small class="text-muted d-block">${escapeHtml(course.timeSlot || 'No schedule')}</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary" onclick="enrollStudent(${studentIdInt}, ${Number.isNaN(courseId) ? 'null' : courseId})">
                                        Enroll
                                    </button>
                                </div>
                            `;
                        });
                    } else {
                        availableHTML = '<p class="text-muted">No available courses</p>';
                    }

                    availableContainer.innerHTML = availableHTML;
                } else if (isTeacher) {
                    let availableHTML = '';

                    if (Array.isArray(data.availableCourses) && data.availableCourses.length) {
                        data.availableCourses.forEach(course => {
                            const courseId = parseInt(course.courseID, 10);
                            const defaultStatusIdRaw = course.defaultStatusID !== undefined ? parseInt(course.defaultStatusID, 10) : NaN;
                            const defaultStatusName = escapeHtml(course.defaultStatusName || 'Enrolled');
                            const actionLabel = escapeHtml(course.defaultActionLabel || 'Enroll');

                            const actionControl = Number.isNaN(courseId) || Number.isNaN(defaultStatusIdRaw)
                                ? '<span class="badge bg-light text-dark">No enrollment record</span>'
                                : `<button class="btn btn-sm btn-outline-primary" onclick="createPendingEnrollment(${studentIdInt}, ${courseId}, ${defaultStatusIdRaw}, this)">
                                        ${actionLabel}
                                   </button>`;

                            availableHTML += `
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <strong>${escapeHtml(course.courseTitle || 'Untitled Course')}</strong>
                                        <small class="text-muted d-block">${escapeHtml(course.courseCode || '')}</small>
                                        <small class="text-muted d-block">${escapeHtml(course.schoolYear || '')}${course.semesterName ? ' • ' + escapeHtml(course.semesterName) : ''}</small>
                                        <small class="text-muted d-block">${escapeHtml(course.timeSlot || 'No schedule')}</small>
                                        <small class="text-muted d-block">No enrollment record</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        ${actionControl}
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        availableHTML = '<p class="text-muted">No available courses</p>';
                    }

                    availableContainer.innerHTML = availableHTML;
                } else {
                    availableContainer.innerHTML = '<p class="text-muted mb-0">No available courses.</p>';
                }

                let enrolledHTML = '';

                const enrollments = Array.isArray(data.enrolledCourses) ? data.enrolledCourses : [];
                const statuses = Array.isArray(data.statuses) ? data.statuses : [];

                if (enrollments.length) {
                    enrollments.forEach(enrollment => {
                        const teacherOwnerId = enrollment.teacherID !== undefined && enrollment.teacherID !== null
                            ? parseInt(enrollment.teacherID, 10)
                            : NaN;
                        const managedByCurrentTeacher = isAdmin || (isTeacher && teacherOwnerId === currentTeacherId);
                        const hasEnrollmentRecord = Boolean(enrollment.enrollmentID);
                        const normalizedStatusId = enrollment.enrollmentStatus !== undefined ? parseInt(enrollment.enrollmentStatus, 10) : NaN;
                        const isPendingStatus = !Number.isNaN(normalizedStatusId) && normalizedStatusId === STATUS_PENDING;
                        const isVirtualPending = enrollment.isVirtualPending === true || enrollment.isVirtualPending === 1 || enrollment.isVirtualPending === '1';
                        const isPending = isPendingStatus || isVirtualPending;
                        const courseId = enrollment.course_id !== undefined ? parseInt(enrollment.course_id, 10) : NaN;
                        const statusOptionsHtml = statuses.map(status => `
                            <option value="${status.statusID}" ${String(status.statusID) === String(enrollment.enrollmentStatus) ? 'selected' : ''}>
                                ${escapeHtml(status.statusName)}
                            </option>
                        `).join('');

                        const statusLabel = escapeHtml(enrollment.statusName || (isPending ? 'Pending' : 'N/A'));
                        const currentStatusValue = enrollment.enrollmentStatus !== undefined && enrollment.enrollmentStatus !== null
                            ? String(enrollment.enrollmentStatus)
                            : '';

                        let statusControl = `<span class="badge bg-light text-dark">${statusLabel}</span>`;
                        let teacherActions = '';

                        if (managedByCurrentTeacher) {
                            if (isTeacher && isPending && Number.isInteger(courseId) && Number.isInteger(studentIdInt)) {
                                statusControl = `<span class="badge bg-warning text-dark">${statusLabel}</span>`;
                                teacherActions = `
                                    <button class="btn btn-sm btn-success" onclick="acceptEnrollment(${studentIdInt}, ${courseId}, this)">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="declineEnrollment(${studentIdInt}, ${courseId}, this)">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                `;
                            } else if (hasEnrollmentRecord && statusOptionsHtml) {
                                statusControl = `
                                    <select class="form-select form-select-sm w-auto" onchange="updateEnrollmentStatus(${enrollment.enrollmentID}, this.value)">
                                        ${statusOptionsHtml}
                                    </select>
                                `;
                            }
                        } else if (isTeacher) {
                            if (isPending || !hasEnrollmentRecord) {
                                statusControl = `<span class="badge bg-warning text-dark">${statusLabel}</span>`;
                            } else {
                                statusControl = '<span class="badge bg-secondary">Managed by another teacher</span>';
                            }
                        }

                        const unenrollButton = (isAdmin && hasEnrollmentRecord)
                            ? `<button class="btn btn-sm btn-outline-danger ms-2" onclick="unenrollStudent(${enrollment.enrollmentID})">Unenroll</button>`
                            : '';

                        const courseTitle = escapeHtml(enrollment.courseTitle || 'Course');
                        const courseCodeLine = enrollment.courseCode
                            ? `<small class="text-muted d-block">${escapeHtml(enrollment.courseCode)}</small>`
                            : '';
                        const scheduleLine = enrollment.schoolYear
                            ? `<small class="text-muted d-block">${escapeHtml(enrollment.schoolYear)}${enrollment.semesterName ? ' • ' + escapeHtml(enrollment.semesterName) : ''}</small>`
                            : '';
                        const timeLine = enrollment.timeSlot
                            ? `<small class="text-muted d-block">${escapeHtml(enrollment.timeSlot)}</small>`
                            : '';
                        const enrollmentDetail = hasEnrollmentRecord && enrollment.enrollmentDate
                            ? `Enrolled: ${escapeHtml(enrollment.enrollmentDate)}`
                            : 'No enrollment record';

                        enrolledHTML += `
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <strong>${courseTitle}</strong>
                                    ${courseCodeLine}
                                    ${scheduleLine}
                                    ${timeLine}
                                    <small class="text-muted d-block">${enrollmentDetail}</small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    ${statusControl}
                                    ${teacherActions}
                                    ${unenrollButton}
                                </div>
                            </div>
                        `;
                    });
                }

                if (!enrollments.length && !enrolledHTML) {
                    enrolledHTML = '<p class="text-muted">No current enrollments</p>';
                }

                enrolledContainer.innerHTML = enrolledHTML;
            })
            .catch(() => {
                alert('Failed to load enrollment data.');
            });

        new bootstrap.Modal(document.getElementById('enrollmentModal')).show();
    }

    // Enroll student function for admin
    function enrollStudent(studentId, courseId) {
        if (!isAdmin) {
            return;
        }

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
        })
        .catch(() => alert('Failed to enroll student.'));
    }

    // Update enrollment status admin and teacher can use
    function updateEnrollmentStatus(enrollmentId, statusId) {
        fetch('<?= base_url('students/updateEnrollmentStatus') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: new URLSearchParams({
                enrollmentId: enrollmentId,
                statusId: statusId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(() => alert('Failed to update enrollment status.'));
    }

    function createPendingEnrollment(studentId, courseId, statusId, controlEl) {
        if (!isTeacher) {
            return;
        }

        const control = controlEl || null;
        const isSelectControl = control && control.tagName === 'SELECT';
        const previousValue = isSelectControl ? (control.dataset.currentValue || '') : '';

        if (!statusId) {
            if (isSelectControl) {
                control.value = previousValue;
            }
            alert('Select a valid status.');
            return;
        }

        if (isSelectControl && String(statusId) === previousValue) {
            control.value = previousValue;
            return;
        }

        if (control) {
            control.disabled = true;
        }

        fetch('<?= base_url('students/createTeacherEnrollment') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: new URLSearchParams({
                studentId: studentId,
                courseId: courseId,
                statusId: statusId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unable to create enrollment.'));
                if (isSelectControl) {
                    control.value = previousValue;
                }
            }
        })
        .catch(() => {
            alert('Failed to update enrollment status.');
            if (isSelectControl) {
                control.value = previousValue;
            }
        })
        .finally(() => {
            if (control) {
                control.disabled = false;
                if (isSelectControl) {
                    control.dataset.currentValue = control.value;
                }
            }
        });
    }

    function acceptEnrollment(studentId, courseId, button) {
        updatePendingEnrollment(studentId, courseId, STATUS_ENROLLED, button);
    }

    function declineEnrollment(studentId, courseId, button) {
        if (!confirm('Decline this enrollment request?')) {
            return;
        }
        updatePendingEnrollment(studentId, courseId, STATUS_PENDING, button);
    }

    function updatePendingEnrollment(studentId, courseId, statusId, button) {
        if (!isTeacher) {
            return;
        }

        const spinner = document.createElement('span');
        spinner.className = 'spinner-border spinner-border-sm';
        spinner.setAttribute('role', 'status');
        spinner.setAttribute('aria-hidden', 'true');

        const originalContent = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '';
        button.appendChild(spinner);

        const bodyParams = new URLSearchParams({
            studentId: String(studentId),
            courseId: String(courseId),
            statusId: String(statusId),
        });

        fetch('<?= base_url('students/createTeacherEnrollment') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: bodyParams
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unable to update enrollment.'));
            }
        })
        .catch(() => {
            alert('Failed to update enrollment.');
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalContent;
        });
    }

    // Unenroll student function for admin
    function unenrollStudent(enrollmentId) {
        if (!isAdmin) {
            return;
        }

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
            })
            .catch(() => alert('Failed to unenroll student.'));
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