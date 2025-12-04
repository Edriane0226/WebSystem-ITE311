<header>
    <title>Course Management</title>
</header>
<?php $today = date('Y-m-d'); ?>
<div class="container-fluid">
        <!-- Flash Messages -->
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

        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h3 mb-0"><i class="fas fa-book-open me-2"></i>Course Management</h2>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                    <i class="fas fa-book text-primary fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Courses</h6>
                                <h4 class="mb-0"><?= isset($course) ? count($course) : 0 ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                    <i class="fas fa-check-circle text-success fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Active Courses</h6>
                                <h4 class="mb-0"><?= isset($activeCourses) ? $activeCourses : 0 ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">All Courses</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" placeholder="Search courses..." id="searchInput">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="coursesTable">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 ps-4">Course Code</th>
                                <th class="border-0">Title</th>
                                <th class="border-0">Description</th>
                                <th class="border-0">School Year</th>
                                <th class="border-0">Teacher</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Edit Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($course) && !empty($course)): ?>
                                <?php foreach ($course as $course): ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-primary"><?= esc($course['courseCode']) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-medium"><?= esc($course['courseTitle']) ?></div>
                                    </td>
                                    <td>
                                        <div class="text-muted" style="max-width: 200px;">
                                            <?= strlen($course['courseDescription']) > 50 ? substr(esc($course['courseDescription']), 0, 50) . '...' : esc($course['courseDescription']) ?>
                                        </div>
                                    </td>
                                    <td><?= esc($course['schoolYear']) ?></td>
                                    <td><?= esc($course['teacherName'] ?? 'Not Assigned') ?></td>
                                    <td>
                                        <form action="<?= base_url('course/setStatus/' . $course['courseID']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <select name="statusID" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <?php if (isset($courseStatuses)): ?>
                                                    <?php foreach ($courseStatuses as $status): ?>
                                                        <option value="<?= $status['statusID'] ?>" <?= $course['statusID'] == $status['statusID'] ? 'selected' : '' ?>>
                                                            <?= $status['statusName'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-secondary mt-2"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editCourseModal"
                                            onclick="loadCourseData(<?= htmlspecialchars(json_encode($course)) ?>)">
                                             Edit Details
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-book fa-3x mb-3"></i>
                                            <h5>No courses found</h5>
                                            <p>Start by adding your first course.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                                
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="p-3">
            <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                        <i class="fas fa-plus me-1"></i>Add New Course
            </button>
        </div>
    </div>

    <div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCourseModalLabel">Create New Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('courses/manage') ?>" method="post" id="courseForm">
                    <div class="modal-body">
                        <?= csrf_field() ?>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Course Code</label>
                                <input type="text" name="courseCode" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">School Year</label>
                                <select name="schoolYear" class="form-select" required>
                                    <option value="">Select School Year</option>
                                    <?php if (isset($schoolYears)): ?>
                                        <?php foreach ($schoolYears as $sy): ?>
                                            <option value="<?= $sy['schoolYearID'] ?>">
                                                <?= $sy['schoolYear'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Course Title</label>
                                <input type="text" name="courseTitle" class="form-control" required>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="courseDescription" class="form-control" rows="3" required></textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Teacher</label>
                                <select name="teacherID" class="form-select">
                                    <option value="">Select Teacher</option>
                                    <?php if (isset($teachers)): ?>
                                        <?php foreach ($teachers as $teacher): ?>
                                            <option value="<?= $teacher['userID'] ?>">
                                                <?= $teacher['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="statusID" class="form-select" required>
                                    <option value="">Select Status</option>
                                    <?php if (isset($courseStatuses)): ?>
                                        <?php foreach ($courseStatuses as $status): ?>
                                            <option value="<?= $status['statusID'] ?>">
                                                <?= $status['statusName'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="startDate" class="form-control" required min="<?= esc($today) ?>" value="<?= esc(old('startDate')) ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" name="endDate" class="form-control" required min="<?= esc($today) ?>" value="<?= esc(old('endDate')) ?>">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Create Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
                                
    <div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCourseModalLabel">Edit Course Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('/course/update/') ?>" method="post" id="editCourseForm">
                    <div class="modal-body">
                        <?= csrf_field() ?>
                        <input type="hidden" name="courseID" id="edit_courseID">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Course Code</label>
                                <input type="text" name="courseCode" id="edit_courseCode" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">School Year</label>
                                <select name="schoolYearID" id="edit_schoolYear" class="form-select" required>
                                    <option value="">Select School Year</option>
                                    <?php if (isset($schoolYears)): ?>
                                        <?php foreach ($schoolYears as $sy): ?>
                                            <option value="<?= $sy['schoolYearID'] ?>"><?= $sy['schoolYear'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="startDate" id="edit_startDate" class="form-control" required min="<?= esc($today) ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" name="endDate" id="edit_endDate" class="form-control" required min="<?= esc($today) ?>">
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Course Title</label>
                                <input type="text" name="courseTitle" id="edit_courseTitle" class="form-control" required>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="courseDescription" id="edit_courseDescription" class="form-control" rows="3" required></textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Teacher</label>
                                <select name="teacherID" id="edit_teacherID" class="form-select">
                                    <option value="">Select Teacher</option>
                                    <?php if (isset($teachers)): ?>
                                        <?php foreach ($teachers as $teacher): ?>
                                            <option value="<?= $teacher['userID'] ?>">
                                                <?= esc($teacher['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const today = '<?= esc($today) ?>';

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#coursesTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });

        // Keep end date synced with start date in create form
        const createStart = document.querySelector('#courseForm input[name="startDate"]');
        const createEnd = document.querySelector('#courseForm input[name="endDate"]');
        if (createStart && createEnd) {
            createEnd.min = createStart.value || today;
            createStart.addEventListener('change', function () {
                createEnd.min = this.value || today;
                if (createEnd.value && createEnd.value < createEnd.min) {
                    createEnd.value = this.value;
                }
            });
        }

        // Reset form when modal is closed
        document.getElementById('createCourseModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('courseForm').reset();
            if (createEnd) {
                createEnd.min = createStart ? createStart.min : today;
            }
        });

        // Load course data into edit modal
        function loadCourseData(course) {
            document.getElementById('edit_courseID').value = course.courseID;
            document.getElementById('edit_courseCode').value = course.courseCode;
            document.getElementById('edit_courseTitle').value = course.courseTitle;
            document.getElementById('edit_courseDescription').value = course.courseDescription;
            document.getElementById('edit_schoolYear').value = course.schoolYearID;
            document.getElementById('edit_teacherID').value = course.teacherID || '';
            document.getElementById('edit_startDate').value = course.startDate ? course.startDate.substring(0, 10) : '';
            document.getElementById('edit_endDate').value = course.endDate ? course.endDate.substring(0, 10) : '';
            document.getElementById('edit_startDate').min = today;
            document.getElementById('edit_endDate').min = document.getElementById('edit_startDate').value || today;
            document.getElementById('editCourseForm').action = `<?= base_url('/course/update/') ?>${course.courseID}`;
        }

        const editStart = document.getElementById('edit_startDate');
        const editEnd = document.getElementById('edit_endDate');
        if (editStart && editEnd) {
            editStart.addEventListener('change', function () {
                editEnd.min = this.value || today;
                if (editEnd.value && editEnd.value < editEnd.min) {
                    editEnd.value = this.value;
                }
            });
        }

        // Reset edit form when modal is closed
        document.getElementById('editCourseModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('editCourseForm').reset();
            if (editEnd) {
                editEnd.min = today;
            }
        });
    </script>