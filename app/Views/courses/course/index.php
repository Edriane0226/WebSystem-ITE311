
<title><?= esc($course['courseTitle'] ?? 'Course Details') ?></title>

<div class="container-fluid py-4 px-4 px-md-5">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('/dashboard') ?>" class="btn btn-outline-secondary btn-sm shadow-sm">
            Back
        </a>
    </div>

        <div class="row g-4" id="coursesContainer">
            <div class="col-12 col-lg-7 col-xl-6">
                <div class="p-4">
                    <h1 class="h4 fw-bold mb-1">
                        <?= esc($course['courseTitle']); ?>
                    </h1>
                    <div class="text-muted small mb-3">
                        <?= esc($course['courseCode']); ?>
                    </div>

                    <div class="d-flex align-items-center gap-2 text-muted small mb-4">
                        <span><?= esc($course['semesterName'] ?? 'N/A'); ?></span>
                        <span class="text-secondary">&bull;</span>
                        <span><?= esc($course['schoolYear'] ?? 'N/A'); ?></span>
                    </div>

                    <?php if ($role == 'student'): ?>
                        <div class="d-flex flex-column flex-sm-row flex-wrap gap-2">
                            <a href="<?= base_url("/course/{$course['courseID']}/assignments") ?>"
                                class="btn btn-primary flex-grow-1">
                                View Assignments
                            </a>
                            <a href="<?= base_url("/course/{$course['courseID']}/modules") ?>"
                                class="btn btn-outline-secondary flex-grow-1">
                                View Modules
                            </a>
                            <a href="<?= base_url("/course/{$course['courseID']}/peoples") ?>"
                                class="btn btn-outline-secondary flex-grow-1">
                                View People
                            </a>
                        </div>
                    <?php elseif ($role == 'teacher'): ?>
                        <div class="d-flex flex-column flex-sm-row flex-wrap gap-2">
                            <a href="<?= base_url("/course/{$course['courseID']}/assignments") ?>"
                                class="btn btn-primary flex-grow-1">
                                Manage Assignments
                            </a>
                            <a href="<?= base_url("/course/{$course['courseID']}/modules") ?>"
                                class="btn btn-outline-secondary flex-grow-1">
                                Manage Modules
                            </a>
                            <a href="<?= base_url("/course/{$course['courseID']}/peoples") ?>"
                                class="btn btn-outline-secondary flex-grow-1">
                                View People
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
</div>