<head>
    <title><?= esc($course['courseTitle'] ?? 'Course Modules') ?></title>
</head>
<body class="bg-light">

<div class="container p-3">
    <h3 class="p-3">Course Modules</h3>
    <a href="<?= base_url('/course/' . $course['courseID']) ?>" class="btn btn-outline-secondary btn-sm shadow-sm">
			Back to Course
		</a>
    <div class="row mt-4 g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div class="mb-3">
                        <h4 class="card-title mb-1">Modules</h4>
                        <?php if (!empty($course['courseTitle'])): ?>
                            <p class="text-muted mb-0"><?= esc($course['courseTitle']); ?><?= !empty($course['courseCode']) ? ' - ' . esc($course['courseCode']) : '' ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if(session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success'); ?></div>
                    <?php elseif(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error'); ?></div>
                    <?php endif; ?>

                    <?php if($canUpload): ?>
                        <form action="<?= base_url('materials/upload/' . $course_id) ?>" method="post" enctype="multipart/form-data" class="mb-3">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label">Choose File</label>
                                <input type="file" name="material_file" class="form-control" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success rounded-pill">Upload Module</button>
                            </div>
                        </form>
                        <hr>
                    <?php endif; ?>

                    <?php if(!empty($modules)): ?>
                        <div class="list-group list-group-flush flex-grow-1">
                            <?php foreach($modules as $module): ?>
                                <div class="list-group-item d-flex align-items-center gap-3">
                                    <div class="flex-grow-1">
                                        <a href="<?= base_url('materials/download/' . $module['id']) ?>" class="text-decoration-none fw-semibold">
                                            <?= esc($module['display_name'] ?? $module['file_name']) ?>
                                        </a>
                                        <?php if (!empty($module['uploaded_at'])): ?>
                                            <?php $uploadedAt = strtotime($module['uploaded_at']); ?>
                                            <?php if ($uploadedAt !== false): ?>
                                                <div><small class="text-muted">Uploaded: <?= esc(date('M d, Y g:i A', $uploadedAt)) ?></small></div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php if($canUpload): ?>
                                        <div class="ms-auto">
                                            <a href="<?= base_url('materials/delete/' . $module['id']) ?>" class="btn btn-danger btn-sm rounded-pill">Delete</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No modules uploaded yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div class="mb-3">
                        <h4 class="card-title mb-1">Assignments</h4>
                        <p class="text-muted mb-0">Course activities and assessments</p>
                    </div>

                    <?php if(!empty($assignments)): ?>
                        <div class="list-group list-group-flush flex-grow-1">
                            <?php foreach($assignments as $assignment): ?>
                                <div class="list-group-item d-flex align-items-center gap-3">
                                    <div class="flex-grow-1">
                                        <a href="<?= base_url('materials/download/' . $assignment['id']) ?>" class="text-decoration-none fw-semibold">
                                            <?= esc($assignment['display_name'] ?? $assignment['file_name']) ?>
                                        </a>
                                        <?php if (!empty($assignment['uploaded_at'])): ?>
                                            <?php $uploadedAt = strtotime($assignment['uploaded_at']); ?>
                                            <?php if ($uploadedAt !== false): ?>
                                                <div><small class="text-muted">Uploaded: <?= esc(date('M d, Y g:i A', $uploadedAt)) ?></small></div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php if($canUpload): ?>
                                        <div class="ms-auto">
                                            <a href="<?= base_url('materials/delete/' . $assignment['id']) ?>" class="btn btn-danger btn-sm rounded-pill">Delete</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No assignments uploaded yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if(empty($modules) && empty($assignments) && empty($otherMaterials)): ?>
        <div class="alert alert-info mt-4">No materials are available for this course yet.</div>
    <?php endif; ?>
</div>

</body>
</html>