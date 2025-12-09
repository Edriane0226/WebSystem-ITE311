<head>
    <title>Learning Materials</title>
</head>
<body class="bg-light">

<div class="container p-3">
    <h3>Learning Materials</h3>
    <h5 class="mt-2 text-muted">Welcome, <?= session()->get('name') ?>!</h5>

    <div class="row mt-4">
        <!-- Courses Card -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-3">Select Course</h4>
                    <form method="get">
                        <?= csrf_field() ?>
                        <select name="course_id" class="form-select" required onchange="this.form.submit()">
                            <option value="">-- Choose a Course --</option>
                            <?php foreach($courses as $c): ?>
                                <option value="<?= $c['courseID']; ?>" <?= ($course_id == $c['courseID']) ? 'selected' : '' ?>>
                                    <?= esc($c['courseTitle']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- Materials Card -->
        <?php if(!empty($course_id)): ?>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-3">Course Materials</h4>

                    <?php $role = session()->get('role'); ?>

                    <!-- Flash Messages galing sa controller -->
                    <?php if(session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success'); ?></div>
                    <?php elseif(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error'); ?></div>
                    <?php endif; ?>

                    <?php if($role == 'admin' || $role == 'teacher'): ?>
                        <form action="<?= base_url('admin' . '/course/' . $course_id . '/upload') ?>" 
                        method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label">Choose File</label>
                                <input type="file" name="material_file" class="form-control" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success rounded-pill">Upload Material</button>
                            </div>
                        </form>
                        <hr>
                    <?php endif; ?>

                    <!-- Uploaded Lists -->
                    <?php if(!empty($materials)): ?>
                        <div class="list-group">
                            <?php foreach($materials as $m): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <?= esc($m['file_name']) ?>
                                    </div>
                                    <div>
                                        <a href="<?= base_url('materials/download/' . $m['id']) ?>" class="btn btn-primary btn-sm rounded-pill">Download</a>
                                        <?php if($role == 'admin' || $role == 'teacher'): ?>
                                            <a href="<?= base_url('materials/delete/' . $m['id']) ?>" class="btn btn-danger btn-sm rounded-pill">Delete</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mt-2">No materials uploaded yet.</p>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>