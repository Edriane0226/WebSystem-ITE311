<body class="d-flex">
    <div class="container p-3">

        <div class="row mb-4">
            <div class="col-md-6">
                <form id="searchForm" class="d-flex">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control"
                               placeholder="Search courses..." name="search_term">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row" id="coursesContainer">
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="col-md-4 mb-4 course-card">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?= esc($course['courseTitle']); ?></h5>
                                <p class="card-text">
                                    <?= esc($course['courseDescription'] ?? 'No description available.'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-muted text-center">No courses found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

<script>
$(document).ready(function () {

    // Client-side filtering
    $('#searchInput').on('keyup', function () {
        var value = $(this).val().toLowerCase();
        $('#coursesContainer .course-card').filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Server-side search
    $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        var searchTerm = $('#searchInput').val().toLowerCase();

        $.get('<?= base_url("/course/search") ?>', { search_term: searchTerm }, function (data) {
            $('#coursesContainer').html(""); 

            if (data.courses && data.courses.length > 0) {
                $.each(data.courses, function (index, course) {
                    var courseHtml = `
                        <div class="col-md-4 mb-4 course-card">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title">${course.courseTitle}</h5>
                                    <p class="card-text">${course.courseDescription || 'No description available.'}</p>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <a href="<?= base_url('/courses/' . $course['courseID']) ?>" class="btn btn-primary w-100">View Details</a>
                                </div>
                            </div>
                        </div>`;
                    $('#coursesContainer').append(courseHtml);
                });
            } else {
                $('#coursesContainer').html('<div class="col-12"><div class="alert alert-info">No courses found matching your search.</div></div>');
            }
        });
    });

});
</script>
