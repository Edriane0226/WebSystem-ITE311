<?php
$courseId = (int) ($course['courseID'] ?? 0);
$roleName = strtolower((string) ($role ?? session()->get('role') ?? ''));
$isStudent = $roleName === 'student';
$teacherName = $teacher['name'] ?? 'No assigned instructor';

$formatDate = static function ($value, string $fallback = 'Not set'): string {
    if (empty($value)) {
        return $fallback;
    }
    $timestamp = strtotime((string) $value);
    if ($timestamp === false) {
        return (string) $value;
    }
    return date('M j, Y', $timestamp);
};

$message = session()->getFlashdata('message');
$error = session()->getFlashdata('error');
$errors = session()->getFlashdata('errors');
$hasAssignments = !empty($assignments);
?>

<div class="container-fluid p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-4 gap-3">
        <div>
            <h2 class="h3 fw-semibold mb-1">
                <i class="bi bi-clipboard-data me-2 text-primary"></i>Assignments
            </h2>
            <p class="text-muted mb-0">Instructor: <?= esc($teacherName) ?></p>
            <p class="text-muted mb-0">Course: <?= esc($course['courseTitle'] ?? 'Course') ?></p>
        </div>
        <div>
            <a href="<?= esc(base_url('course/' . $courseId), 'url') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Course Page
            </a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors) && is_array($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach ($errors as $field => $fieldError): ?>
                    <li><?= esc($fieldError) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold mb-0">Assignment List</h5>
                        <span class="badge bg-primary-subtle text-primary px-3 py-2">
                            <?= esc(count($assignments)) ?> total
                        </span>
                    </div>

                    <?php if (!$hasAssignments): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inboxes fs-1 mb-3"></i>
                            <p class="mb-0">No assignments have been posted for this course yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($assignments as $assignment): ?>
                                <?php
                                    $assignmentId = (int) $assignment['AssignmentID'];
                                    $allowedAttempts = $assignment['allowedAttempts'] ?? null;
                                    $attemptLimitText = $allowedAttempts ? $allowedAttempts . ' attempt' . ((int) $allowedAttempts > 1 ? 's' : '') : 'Unlimited attempts';
                                    $assignmentSubmissions = $isStudent ? ($studentSubmissions[$assignmentId] ?? []) : [];
                                    $attemptCount = $isStudent ? count($assignmentSubmissions) : 0;
                                    $latestSubmission = $attemptCount > 0 ? $assignmentSubmissions[0] : null;
                                    $submissionCount = !$isStudent ? ($submissionCounts[$assignmentId] ?? 0) : $attemptCount;
                                    $materialId = (int) ($assignment['materialIdRef'] ?? 0);
                                    $materialDownloadUrl = $materialId > 0 ? base_url('materials/download/' . $materialId) : null;
                                ?>
                                <div class="list-group-item px-0 py-4 border-0 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                                        <div>
                                            <h6 class="fw-semibold mb-1">
                                                <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                                                <?= esc($assignment['materialName'] ?? 'Assignment #' . $assignmentId) ?>
                                            </h6>
                                            <div class="text-muted small d-flex flex-wrap gap-3">
                                                <span><i class="bi bi-calendar-plus me-1"></i>Publish: <?= esc($formatDate($assignment['publishDate'] ?? null)) ?></span>
                                                <span><i class="bi bi-calendar-event me-1"></i>Due: <?= esc($formatDate($assignment['dueDate'] ?? null, 'No due date')) ?></span>
                                                <span><i class="bi bi-arrow-repeat me-1"></i><?= esc($attemptLimitText) ?></span>
                                            </div>
                                            <?php if ($materialDownloadUrl): ?>
                                                <div class="mt-2">
                                                    <a href="<?= esc($materialDownloadUrl, 'url') ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-download me-1"></i>Download Assignment
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-info-subtle text-info px-3 py-2 d-inline-flex align-items-center">
                                                <i class="bi bi-people me-1"></i>
                                                <?= esc($submissionCount) ?> submissions
                                            </span>
                                        </div>
                                    </div>

                                    <?php if ($isStudent): ?>
                                        <div class="mt-3 small">
                                            <?php if ($attemptCount === 0): ?>
                                                <span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>No submissions yet.</span>
                                            <?php else: ?>
                                                <div class="p-3 bg-light rounded border">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <strong>Latest submission:</strong>
                                                            <span class="ms-2">Attempt <?= esc($attemptCount) ?> • <?= esc($formatDate($latestSubmission['submissionDate'] ?? null, 'Unknown date')) ?></span>
                                                        </div>
                                                        <?php if (!empty($latestSubmission['submissionMaterialId'])): ?>
                                                            <a href="<?= esc(base_url('materials/download/' . $latestSubmission['submissionMaterialId']), 'url') ?>" class="btn btn-sm btn-outline-secondary">
                                                                <i class="bi bi-box-arrow-down me-1"></i>Download copy
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <?php if (!$isStudent): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold mb-3">Upload New Assignment</h5>
                        <p class="text-muted small">Upload an assignment brief and optionally configure attempts and important dates.</p>
                        <form action="<?= esc(base_url('course/' . $courseId . '/assignments'), 'url') ?>" method="post" enctype="multipart/form-data" class="mt-3">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="assignment_file" class="form-label">Assignment file</label>
                                <input type="file" name="assignment_file" id="assignment_file" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip,.rar" required>
                            </div>
                            <div class="mb-3">
                                <label for="allowedAttempts" class="form-label">Allowed attempts</label>
                                <input type="number" min="1" max="9" name="allowedAttempts" id="allowedAttempts" class="form-control" placeholder="Leave blank for unlimited">
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col">
                                    <label for="publishDate" class="form-label">Publish date</label>
                                    <input type="date" name="publishDate" id="publishDate" class="form-control">
                                </div>
                                <div class="col">
                                    <label for="dueDate" class="form-label">Due date</label>
                                    <input type="date" name="dueDate" id="dueDate" class="form-control">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-cloud-upload me-2"></i>Upload Assignment
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($isStudent): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold mb-3">Submit Assignment</h5>
                        <p class="text-muted small">Select the assignment you are uploading for and attach your work.</p>
                        <form action="<?= esc(base_url('course/' . $courseId . '/assignments/submit'), 'url') ?>" method="post" enctype="multipart/form-data" class="mt-3">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="assignment_id" class="form-label">Assignment</label>
                                <select name="assignment_id" id="assignment_id" class="form-select" <?= $hasAssignments ? '' : 'disabled' ?> required>
                                    <option value="" disabled selected>Select assignment</option>
                                    <?php foreach ($assignments as $assignment): ?>
                                        <?php
                                            $assignmentId = (int) $assignment['AssignmentID'];
                                            $attemptLimit = $assignment['allowedAttempts'] ?? null;
                                            $attemptsMade = count($studentSubmissions[$assignmentId] ?? []);
                                            $isLocked = !empty($attemptLimit) && $attemptsMade >= (int) $attemptLimit;
                                        ?>
                                        <option value="<?= esc($assignmentId) ?>" <?= $isLocked ? 'disabled' : '' ?>>
                                            <?= esc($assignment['materialName'] ?? 'Assignment #' . $assignmentId) ?>
                                            <?= $isLocked ? ' (maximum attempts reached)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="submission_file" class="form-label">Upload file</label>
                                <input type="file" name="submission_file" id="submission_file" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip,.rar" <?= $hasAssignments ? '' : 'disabled' ?> required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" <?= $hasAssignments ? '' : 'disabled' ?>>
                                <i class="bi bi-upload me-2"></i>Submit
                            </button>
                            <?php if (!$hasAssignments): ?>
                                <p class="text-muted small mt-2 mb-0">An assignment must be posted before you can upload.</p>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
