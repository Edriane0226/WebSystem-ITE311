<title><?= esc($course['courseTitle'] ?? 'Course') ?> • Submission Details</title>

<div class="container-fluid bg-light py-4 px-4 px-md-5 min-vh-100">
    <div class="d-flex flex-column flex-md-row align-items-md-center gap-3 mb-4">
        <a href="<?= base_url('/course/' . $course['courseID'] . '/assignments') ?>" class="btn btn-outline-secondary btn-sm shadow-sm">
            ← Back to Assignments
        </a>
        <div>
            <p class="text-uppercase text-muted small mb-1">Submission Details • <?= esc($course['courseCode'] ?? 'Course') ?></p>
            <h2 class="h4 fw-bold mb-1"><?= esc($assignment['title'] ?? $assignment['materialName'] ?? 'Assignment #' . $assignment['AssignmentID']) ?></h2>
            <div class="text-muted small">
                <?= esc($course['semesterName'] ?? 'Semester N/A'); ?>
                <span class="mx-1">•</span>
                <?= esc($course['schoolYear'] ?? 'SY N/A'); ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">Publish Date</p>
                            <p class="fw-semibold mb-0">
                                <?= !empty($assignment['publishDate']) ? esc(date('M d, Y g:i A', strtotime($assignment['publishDate']))) : 'N/A' ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">Due Date</p>
                            <p class="fw-semibold mb-0">
                                <?= !empty($assignment['dueDate']) ? esc(date('M d, Y g:i A', strtotime($assignment['dueDate']))) : 'No due date' ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">Allowed Attempts</p>
                            <p class="fw-semibold mb-0">
                                <?= !empty($assignment['allowedAttempts']) ? esc($assignment['allowedAttempts']) : 'Unlimited' ?>
                            </p>
                        </div>
                    </div>
                    <?php if (!empty($assignment['Instructions'])): ?>
                        <div class="bg-light border rounded-3 p-3 mt-3">
                            <p class="text-uppercase text-muted small mb-1">Instructions</p>
                            <div class="text-dark small"><?= nl2br(esc($assignment['Instructions'])) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($submissions)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-semibold mb-0">Submissions</h5>
                            <span class="badge bg-primary"><?= esc($stats['totalSubmissions']) ?> total</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Submitted At</th>
                                        <th>Attempt #</th>
                                        <th>Status</th>
                                        <th>File</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($submissions as $submission): ?>
                                        <?php
                                            $isLate = !empty($assignment['dueDate'])
                                                && strtotime($submission['submissionDate']) > strtotime($assignment['dueDate']);
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">
                                                    <?= esc($submission['studentName'] ?? 'Unknown Student') ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <?= esc($submission['studentEmail'] ?? 'N/A') ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?= esc(date('M d, Y g:i A', strtotime($submission['submissionDate']))) ?>
                                            </td>
                                            <td><?= esc($submission['attemptNumber'] ?? '1') ?></td>
                                            <td>
                                                <span class="badge <?= $isLate ? 'bg-warning text-dark' : 'bg-success' ?>">
                                                    <?= $isLate ? 'Late' : 'On Time' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($submission['submissionMaterialId'])): ?>
                                                    <a href="<?= base_url('materials/download/' . $submission['submissionMaterialId']) ?>" class="btn btn-sm btn-outline-primary">
                                                        Download
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Unavailable</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <h5 class="fw-semibold mb-2">No submissions yet</h5>
                        <p class="text-muted mb-0">Once students submit their work, details will appear here.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Submission Overview</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2"><span class="fw-semibold text-dark">Total Files:</span> <?= esc($stats['totalSubmissions']) ?></li>
                        <li class="mb-2"><span class="fw-semibold text-dark">Unique Students:</span> <?= esc($stats['uniqueStudents']) ?></li>
                        <li class="mb-2"><span class="fw-semibold text-dark">Submission Window:</span>
                            <span class="badge <?= !empty($assignment['isClosed']) ? 'bg-dark' : 'bg-success' ?>">
                                <?= !empty($assignment['isClosed']) ? 'Closed' : 'Open' ?>
                            </span>
                        </li>
                        <li><span class="fw-semibold text-dark">Latest Submission:</span>
                            <?= $stats['latestSubmission'] ? esc(date('M d, Y g:i A', strtotime($stats['latestSubmission']))) : 'N/A' ?>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Course Details</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2"><span class="fw-semibold text-dark">Course:</span> <?= esc($course['courseTitle'] ?? 'N/A') ?></li>
                        <li class="mb-2"><span class="fw-semibold text-dark">Instructor:</span> <?= esc($teacher['name'] ?? 'TBA') ?></li>
                        <li><span class="fw-semibold text-dark">School Year:</span> <?= esc($course['schoolYear'] ?? 'N/A') ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>