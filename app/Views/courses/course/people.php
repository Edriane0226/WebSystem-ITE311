<?php
$courseId = (int) ($course['courseID'] ?? 0);
$roleName = strtolower((string) ($role ?? session()->get('role') ?? ''));
$teacherName = $teacher['name'] ?? 'No assigned instructor';
$teacherEmail = $teacher['email'] ?? null;
$teacherId = $teacher['userID'] ?? null;
?>

<div class="container-fluid p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h2 class="h3 fw-semibold mb-1">
                <i class="bi bi-people-fill me-2 text-primary"></i>Course People
            </h2>
            <p class="text-muted mb-0">Course: <?= esc($course['courseTitle'] ?? 'Course') ?></p>
            <p class="text-muted mb-0">Section: <?= esc($course['courseCode'] ?? 'N/A') ?></p>
        </div>
        <div>
            <a href="<?= esc(base_url('course/' . $courseId), 'url') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Course Page
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <span class="badge bg-primary-subtle text-primary mb-3 px-3 py-2">Instructor</span>
                    <h5 class="fw-semibold mb-1">
                        <i class="bi bi-person-badge me-2"></i><?= esc($teacherName) ?>
                    </h5>
                    <p class="text-muted small mb-2">Teacher ID: <?= esc($teacherId ?? 'N/A') ?></p>
                    <?php if ($teacherEmail): ?>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-envelope me-2"></i><a class="text-decoration-none" href="mailto:<?= esc($teacherEmail) ?>"><?= esc($teacherEmail) ?></a>
                        </p>
                    <?php else: ?>
                        <p class="text-muted small mb-0">No contact email available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold mb-0">Enrolled Students</h5>
                        <span class="badge bg-info-subtle text-info px-3 py-2"><?= esc(count($students)) ?> total</span>
                    </div>

                    <?php if (empty($students)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-emoji-neutral fs-1 mb-3"></i>
                            <p class="mb-0">No students are currently enrolled in this course.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                        <?php
                                            $statusName = $student['statusName'] ?? 'Unknown';
                                            $statusClass = 'bg-secondary';
                                            $statusLower = strtolower($statusName);

                                            if (strpos($statusLower, 'enroll') !== false) {
                                                $statusClass = 'bg-success';
                                            } elseif (strpos($statusLower, 'pending') !== false) {
                                                $statusClass = 'bg-warning text-dark';
                                            } elseif (strpos($statusLower, 'drop') !== false) {
                                                $statusClass = 'bg-danger';
                                            }
                                        ?>
                                        <tr>
                                            <td><?= esc($student['name'] ?? 'Student') ?></td>
                                            <td>
                                                <?php if (!empty($student['email'])): ?>
                                                    <a href="mailto:<?= esc($student['email']) ?>" class="text-decoration-none">
                                                        <?= esc($student['email']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">No email</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge <?= esc($statusClass) ?> px-3 py-2">
                                                    <?= esc($statusName) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
