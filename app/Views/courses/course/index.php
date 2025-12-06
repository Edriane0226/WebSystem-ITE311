<?php
$courseId = (int) ($course['courseID'] ?? 0);
$assignmentUrl = base_url('course/' . $courseId . '/assignments');
$modulesUrl = base_url('course/' . $courseId . '/modules');
$peopleUrl = base_url('course/' . $courseId . '/peoples');

$statusLabel = $course['statusName'] ?? 'Status unavailable';
$statusClass = 'bg-secondary';
$statusLower = strtolower($statusLabel);

if (strpos($statusLower, 'active') !== false) {
    $statusClass = 'bg-success';
} elseif (strpos($statusLower, 'pending') !== false) {
    $statusClass = 'bg-warning text-dark';
} elseif (strpos($statusLower, 'inactive') !== false || strpos($statusLower, 'closed') !== false) {
    $statusClass = 'bg-danger';
}

$formatDate = static function ($value) {
    if (empty($value)) {
        return 'Not set';
    }

    $timestamp = strtotime((string) $value);
    if ($timestamp === false) {
        return (string) $value;
    }

    return date('M j, Y', $timestamp);
};

$startDateLabel = $formatDate($course['startDate'] ?? null);
$endDateLabel = $formatDate($course['endDate'] ?? null);
$timeSlotLabel = !empty($course['timeSlot']) ? $course['timeSlot'] : 'To be announced';
$teacherName = $course['teacherName'] ?? 'No teacher assigned';
$schoolYear = $course['schoolYear'] ?? 'Not available';
$semesterName = $course['semesterName'] ?? null;
?>

<div class="container-fluid p-4">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2 class="h3 fw-semibold mb-2">
                        <i class="bi bi-journal-bookmark me-2 text-primary"></i><?= esc($course['courseTitle'] ?? 'Course Overview') ?>
                    </h2>
                    <p class="text-muted mb-3 mb-lg-4">
                        <?= esc($course['courseDescription'] ?? 'This course has no description yet.') ?>
                    </p>
                    <div class="d-flex flex-wrap gap-3 text-muted small">
                        <span class="d-inline-flex align-items-center">
                            <i class="bi bi-hash me-2"></i>
                            <?= esc($course['courseCode'] ?? 'No code') ?>
                        </span>
                        <span class="d-inline-flex align-items-center">
                            <i class="bi bi-person-badge me-2"></i>
                            <?= esc($teacherName) ?>
                        </span>
                        <span class="d-inline-flex align-items-center">
                            <i class="bi bi-calendar-event me-2"></i>
                            <?= esc($startDateLabel) ?> – <?= esc($endDateLabel) ?>
                        </span>
                        <span class="d-inline-flex align-items-center">
                            <i class="bi bi-clock-history me-2"></i>
                            <?= esc($timeSlotLabel) ?>
                        </span>
                        <span class="d-inline-flex align-items-center">
                            <i class="bi bi-mortarboard me-2"></i>
                            <?= esc($schoolYear) ?><?= $semesterName ? ' · ' . esc($semesterName) : '' ?>
                        </span>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <span class="badge <?= esc($statusClass) ?> px-3 py-2 text-uppercase fw-semibold">
                        <?= esc($statusLabel) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <a href="<?= esc($assignmentUrl, 'url') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="bi bi-clipboard-check text-primary fs-3"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">Assignments</h5>
                        <p class="text-muted small mb-3">Track and submit your course tasks on time.</p>
                        <div class="bg-light border rounded-pill px-3 py-2 small text-muted">
                            Base URL: <code><?= esc($assignmentUrl) ?></code>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= esc($modulesUrl, 'url') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="bi bi-collection-play text-success fs-3"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">Modules</h5>
                        <p class="text-muted small mb-3">Access learning materials and weekly lessons.</p>
                        <div class="bg-light border rounded-pill px-3 py-2 small text-muted">
                            Base URL: <code><?= esc($modulesUrl) ?></code>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= esc($peopleUrl, 'url') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="bi bi-people text-info fs-3"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">Peoples</h5>
                        <p class="text-muted small mb-3">Connect with classmates and instructors in this course.</p>
                        <div class="bg-light border rounded-pill px-3 py-2 small text-muted">
                            Base URL: <code><?= esc($peopleUrl) ?></code>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

</div>
