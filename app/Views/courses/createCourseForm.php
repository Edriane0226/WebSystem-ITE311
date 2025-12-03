<div class="container-fluid p-4">
    <div class="row">
        <div class="col-md-8">
            <h3>Create New Course</h3>
            
            <div class="card mt-3">
                <div class="card-body">
                    <form action="<?= base_url('courses/save') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Course Code</label>
                            <input type="text" name="courseCode" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Course Title</label>
                            <input type="text" name="courseTitle" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="courseDescription" class="form-control" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">School Year</label>
                            <input type="text" name="schoolYear" class="form-control" placeholder="2024-2025" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Teacher</label>
                            <select name="teacherID" class="form-select">
                                <option value="">Select Teacher</option>
                                <?php if (isset($teachers)): ?>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= $teacher['userID'] ?>">
                                            <?= $teacher['firstName'] . ' ' . $teacher['lastName'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Create Course</button>
                        <a href="<?= base_url('courses/manage') ?>" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>