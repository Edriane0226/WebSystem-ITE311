
    <div class="container-fluid">
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

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h3 mb-0"><i class="fas fa-book-open me-2"></i>Course Management</h2>
                   
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
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

        <!-- Course Table -->
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
                                    <td><?= esc($course['teacherName'] ?? 'Not Assigned') ?>
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
    </div>

    <!-- Course Modal -->
    

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#coursesTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });

        // Reset form
        function resetForm() {
            document.getElementById('courseForm').reset();
            document.getElementById('courseID').value = '';
            document.getElementById('modalTitle').textContent = 'Add New Course';
            document.getElementById('submitBtnText').textContent = 'Save Course';
        }
    </script>