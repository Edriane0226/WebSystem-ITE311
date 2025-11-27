<body class="d-flex">
    <div class="container p-3">

        <div>
            <a href="<?= base_url('/course/search') ?>" class="btn btn-secondary mb-3">
                Back
            </a>
        </div>

        <div class="row" id="coursesContainer">
            <?php if (!empty($course)): ?>
                <div class="col-md-6 mb-4 course-card">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= esc($course['courseTitle']); ?></h5>
                            <p class="card-text text-muted small">
                                Course Code: <strong><?= esc($course['courseCode'] ?? 'N/A'); ?></strong>
                            </p>

                            <p class="card-text">
                                <?= esc($course['courseDescription'] ?? 'No description available.'); ?>
                            </p>
                            <p class="card-text text-muted small">
                                Teacher: <strong><?= esc($teacher['name'] ?? 'N/A'); ?></strong>
                            </p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-muted text-center">Course not found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body> 