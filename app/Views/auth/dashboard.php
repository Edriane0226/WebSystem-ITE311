<div class="container p-3">
        <h3>Dashboard</h3>
        <h4 class="mt-3 text-muted">Hello, <?= $name ?>!</h4>
    
        <div class="row">
            <?php if( $role == 'student' ): ?>
                <div class="col-md-10">
                    <div class="card mt-5">
                        <div class="card-body">
                            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">
                                <h4 class="card-title mb-0">Available Courses</h4>
                                <form id="studentSearchForm" class="ms-md-auto w-100">
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="studentSearchInput" class="form-control" placeholder="Search available courses...">
                                        <button class="btn btn-outline-primary" type="submit">Search</button>
                                    </div>
                                </form>
                            </div>
                            <div class="row mt-3" id="studentCoursesContainer">
                                <?php
                                $enrollmentByCourse = [];
                                foreach ($enrollments as $enroll) {
                                    $enrollmentByCourse[$enroll['course_id']] = $enroll['statusName'];
                                }
                                ?>
                                
                                <?php foreach ($courses as $course):?>
                                    <?php
                                        $statusValue = strtolower(trim($course['statusName'] ?? ''));
                                        
                                        if ($statusValue === 'inactive') {
                                            continue; 
                                        }
                                    ?>
                                    <div class="col-md-4 course-card-wrapper">
                                        <!-- remove lng ang base64_encode ug decode sa controller kung i try ang CSRF Token -->
                                        <div class="card mt-5 courseCard" data-course_id="<?= base64_encode($course['courseID']) ?>">
                                            <div class="card-body">
                                                <h4 class="card-title"><?= $course['courseTitle'] ?></h4>
                                                <h6 class="card-subtitle mb-2 text-muted"><?= $course['courseCode'] ?></h6>
                                                <p><?= $course['courseDescription'] ?></p>
                                                <p class="text-muted small">School Year: <?= $course['schoolYear'] ?></p>
                                                <p class="text-muted small">Semester: <?= $course['semesterName'] ?? $course['Semester'] ?></p>
                                                <p class="text-muted small">Schedule: <?= $course['timeSlot'] ?? 'Not Set' ?></p>
                                                   
                                                    <?php $status = $enrollmentByCourse[$course['courseID']] ?? null;?>
                                                    <?php if ($status == 'Enrolled'): ?>
                                                    <button class="btn btn-primary" disabled>Enrolled</button>
                                                    <?php elseif ($status == 'Completed'): ?>
                                                        <button class="btn btn-secondary" disabled>Completed</button>
                                                    <?php elseif ($status == 'Dropped'): ?>
                                                        <button class="btn btn-danger" disabled>Dropped</button>
                                                    <?php else: ?>
                                                        <button class="btn btn-primary enroll">Enroll</button>
                                                    <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                


                <div class="col-md-8">
                    <div class="card mt-5">
                        <div class="card-body">
                            <h4 class="card-title">Enrolled Courses</h4>
                            <div class="row" id="enrolledCourses">
                                <?php foreach ($enrollments as $enrolled):?>
                                <?php if ($enrolled['enrollmentStatus'] == 2 || $enrolled['enrollmentStatus'] == 3) continue;?>
                                <div class="col-md-4">
                                    <div class="card mt-5 position-relative">
                                        <div class="card-body">
                                            <h4 class="card-title"><?= $enrolled['courseTitle']?></h4>
                                            <h6 class="card-subtitle mb-2 text-muted"><?= $enrolled['courseCode']?></h6>
                                            <p><?= $enrolled['courseDescription'] ?></p>
                                            <?php if (!empty($enrolled['startDate']) || !empty($enrolled['endDate'])): ?>
                                                <p class="text-muted small mb-0">
                                                    <?php if (!empty($enrolled['startDate'])): ?>
                                                        Starts: <?= date('M j, Y', strtotime($enrolled['startDate'])) ?>
                                                    <?php endif; ?>
                                                    <?php if (!empty($enrolled['endDate'])): ?>
                                                        <br>Ends: <?= date('M j, Y', strtotime($enrolled['endDate'])) ?>
                                                    <?php endif; ?>
                                                </p>
                                            <?php endif; ?>
                                            <a href="<?= base_url('course/' . $enrolled['course_id']) ?>" class="stretched-link" aria-label="Open <?= esc($enrolled['courseTitle']) ?> course page"></a>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

           <?php elseif ($role == 'teacher'): ?>
            <div class="col-md-7">
                <div class="card mt-5 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2 mb-3">
                            <h4 class="card-title mb-0">My Courses</h4>
                            <form id="teacherSearchForm" class="ms-md-auto w-100">
                                <div class="input-group input-group-sm">
                                    <input type="text" id="teacherSearchInput" class="form-control" placeholder="Search my courses...">
                                    <button class="btn btn-outline-primary" type="submit">Search</button>
                                </div>
                            </form>
                        </div>

                        <?php if (!empty($courses)): ?>
                            <div class="list-group" id="teacherCoursesContainer">
                                <?php foreach ($courses as $course): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center position-relative teacher-course-item">
                                        <div>
                                            <a href="<?= base_url('course/' . $course['courseID']); ?>" class="stretched-link text-decoration-none">
                                                <h5 class="mb-1 p-2 text-primary"><?= esc($course['courseTitle']); ?></h5>
                                            </a>
                                            <?php if (!empty($course['courseCode'])): ?>
                                                <p class="mb-0 px-2 text-muted small"><?= esc($course['courseCode']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center mt-3">No courses assigned yet.</p>
                        <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>


                <div class="col-md-8">
                    <div class="card mt-5">
                        <div class="card-body">
                            <h4 class="card-title">Enrolled Students</h4>
                        </div>
                    </div>
                </div>

            <?php elseif( $role == 'admin' ): ?>
                
                <h2 class="mt-3">Statistics</h2>
                <div class="col-md-6">
                    <div class="card mt-2">
                        <div class="card-body">
                            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2 mb-3">
                                <h4 class="card-title mb-0">Total Users</h4>
                                <div class="input-group input-group-sm ms-md-auto w-100">
                                    <input type="text" id="adminUserSearchInput" class="form-control" placeholder="Search users...">
                                </div>
                            </div>
                            <table class="table" id="adminUsersTable">
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Name</th>
                                        <th>Role</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- retrieve all users -->
                                    <?php foreach ($allUsers as $user): ?>
                                    <tr>
                                        <td><?= esc($user['userID']); ?></td>
                                        <td><?= esc($user['name']); ?></td>
                                        <td><?= esc($user['role']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table> 
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                <div class="card mt-5 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2 mb-3">
                            <h4 class="card-title mb-0">Courses</h4>
                            <form id="adminCourseSearchForm" class="ms-md-auto w-100">
                                <div class="input-group input-group-sm">
                                    <input type="text" id="adminCourseSearchInput" class="form-control" placeholder="Search courses...">
                                    <button class="btn btn-outline-primary" type="submit">Search</button>
                                </div>
                            </form>
                        </div>

                        <?php if (!empty($courses)): ?>
                            <div class="list-group" id="adminCoursesContainer">
                                <?php foreach ($courses as $course): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center admin-course-item">
                                        <div>
                                            <h5 class="mb-1 p-2"><?= esc($course['courseTitle']); ?></h5>
                                        </div>
                                        <div>
                                            <a href="<?= base_url('admin/course/' . $course['courseID'] . '/upload'); ?>" 
                                            class="btn btn-primary btn-sm rounded-pill">
                                                Add Material
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center mt-3">No courses assigned yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

                
            <?php endif; ?>
        </div>
        

    </div>
</html>
<script>
(function ($) {
    'use strict';

    if (typeof $ === 'undefined') {
        return;
    }

    const courseSearchUrl = "<?= base_url('/course/search'); ?>";
    const enrollmentStatusMap = (function () {
        const raw = <?= json_encode(isset($enrollmentByCourse) ? $enrollmentByCourse : []); ?>;
        if (Array.isArray(raw)) {
            const converted = {};
            raw.forEach(function (value, index) {
                if (value !== null && value !== undefined) {
                    converted[String(index)] = value;
                }
            });
            return converted;
        }
        if (raw && typeof raw === 'object') {
            return raw;
        }
        return {};
    })();
    const currentRole = "<?= esc($role); ?>";
    const currentUserId = <?= (int) session()->get('userID'); ?>;
    const coursePageBaseUrl = "<?= base_url('course/'); ?>";
    const adminMaterialUrlBase = "<?= base_url('admin/course/'); ?>";
    const csrfNameMeta = $('meta[name="csrf-token-name"]');
    const csrfValueMeta = $('meta[name="csrf-token-value"]');

    const escapeMap = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        '\'': '&#39;'
    };

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return escapeMap[char] || char;
        });
    }

    function updateCsrf(hash) {
        if (hash && csrfValueMeta.length) {
            csrfValueMeta.attr('content', hash);
        }
    }

    function renderStudentCourses(courses) {
        const $container = $('#studentCoursesContainer');
        if (!$container.length) {
            return;
        }

        $container.empty();

        let activeCount = 0;

        (courses || []).forEach(function (course) {
            const statusName = String(course.statusName || '').toLowerCase().trim();
            if (statusName === 'inactive') {
                return;
            }

            activeCount += 1;

            const encodedCourseId = window.btoa(String(course.courseID));
            const enrollmentStatus = enrollmentStatusMap[String(course.courseID)] || null;
            const courseTitle = escapeHtml(course.courseTitle || '');
            const courseCode = escapeHtml(course.courseCode || '');
            const courseDescription = escapeHtml(course.courseDescription || '');
            const schoolYear = escapeHtml(course.schoolYear || 'Not Set');
            const semester = escapeHtml(course.semesterName || course.Semester || 'Not Set');
            const schedule = escapeHtml(course.timeSlot || 'Not Set');

            let buttonHtml = '<button class="btn btn-primary enroll">Enroll</button>';
            if (enrollmentStatus === 'Enrolled') {
                buttonHtml = '<button class="btn btn-primary" disabled>Enrolled</button>';
            } else if (enrollmentStatus === 'Completed') {
                buttonHtml = '<button class="btn btn-secondary" disabled>Completed</button>';
            } else if (enrollmentStatus === 'Dropped') {
                buttonHtml = '<button class="btn btn-danger" disabled>Dropped</button>';
            }

            const cardHtml = `
                <div class="col-md-4 course-card-wrapper">
                    <div class="card mt-5 courseCard" data-course_id="${encodedCourseId}">
                        <div class="card-body">
                            <h4 class="card-title">${courseTitle}</h4>
                            <h6 class="card-subtitle mb-2 text-muted">${courseCode}</h6>
                            <p>${courseDescription}</p>
                            <p class="text-muted small">School Year: ${schoolYear}</p>
                            <p class="text-muted small">Semester: ${semester}</p>
                            <p class="text-muted small">Schedule: ${schedule}</p>
                            ${buttonHtml}
                        </div>
                    </div>
                </div>
            `;

            $container.append(cardHtml);
        });

        if (activeCount === 0) {
            $container.html('<div class="col-12"><div class="alert alert-info">No active courses found.</div></div>');
        }
    }

    function renderTeacherCourses(courses) {
        const $container = $('#teacherCoursesContainer');
        if (!$container.length) {
            return;
        }

        $container.empty();

        const list = Array.isArray(courses) ? courses : [];

        if (list.length === 0) {
            $container.append('<div class="list-group-item text-muted text-center teacher-course-item">No courses assigned yet.</div>');
            return;
        }

        let visibleCount = 0;

        list.forEach(function (course) {
            if (currentRole === 'teacher' && course.teacherID && parseInt(course.teacherID, 10) !== currentUserId) {
                return;
            }

            visibleCount += 1;

            const title = escapeHtml(course.courseTitle || '');
            const courseCode = escapeHtml(course.courseCode || '');
            const courseUrl = `${coursePageBaseUrl}${course.courseID}`;

            const itemHtml = `
                <div class="list-group-item d-flex justify-content-between align-items-center position-relative teacher-course-item">
                    <div>
                        <a href="${courseUrl}" class="stretched-link text-decoration-none">
                            <h5 class="mb-1 p-2 text-primary">${title}</h5>
                        </a>
                        ${courseCode ? `<p class="mb-0 px-2 text-muted small">${courseCode}</p>` : ''}
                    </div>
                </div>
            `;

            $container.append(itemHtml);
        });

        if (visibleCount === 0) {
            $container.append('<div class="list-group-item text-muted text-center teacher-course-item">No courses assigned yet.</div>');
        }
    }

    function renderAdminCourses(courses) {
        const $container = $('#adminCoursesContainer');
        if (!$container.length) {
            return;
        }

        $container.empty();

        const list = Array.isArray(courses) ? courses : [];

        if (list.length === 0) {
            $container.append('<div class="list-group-item text-muted text-center admin-course-item">No courses found.</div>');
            return;
        }

        list.forEach(function (course) {
            const title = escapeHtml(course.courseTitle || '');
            const uploadUrl = `${adminMaterialUrlBase}${course.courseID}/upload`;

            const itemHtml = `
                <div class="list-group-item d-flex justify-content-between align-items-center admin-course-item">
                    <div>
                        <h5 class="mb-1 p-2">${title}</h5>
                    </div>
                    <div>
                        <a href="${uploadUrl}" class="btn btn-primary btn-sm rounded-pill">Add Material</a>
                    </div>
                </div>
            `;

            $container.append(itemHtml);
        });
    }

    function handleCourseSearch($form, inputSelector, context, renderCallback) {
        if (!$form.length || typeof renderCallback !== 'function') {
            return;
        }

        const $input = $form.find(inputSelector);

        $form.on('submit', function (event) {
            event.preventDefault();

            const searchTerm = $input.val().trim();

            $.get(courseSearchUrl, { search_term: searchTerm, context: context })
                .done(function (response) {
                    if (response && Array.isArray(response.courses)) {
                        renderCallback(response.courses);
                    } else {
                        renderCallback([]);
                    }

                    updateCsrf(response ? response.csrfHash : null);
                })
                .fail(function () {
                    if (context === 'student-dashboard') {
                        $('#studentCoursesContainer').html('<div class="col-12"><div class="alert alert-danger">Unable to search courses right now.</div></div>');
                    } else if (context === 'teacher-dashboard') {
                        $('#teacherCoursesContainer').html('<div class="list-group-item text-muted text-center">Unable to search courses right now.</div>');
                    } else if (context === 'admin-dashboard') {
                        $('#adminCoursesContainer').html('<div class="list-group-item text-muted text-center">Unable to search courses right now.</div>');
                    }
                });
        });
    }

    $(document).on('click', '.enroll', function (event) {
        event.preventDefault();

        const $button = $(this);
        const $courseCard = $button.closest('.courseCard');
        const encodedCourseId = $courseCard.data('course_id');

        if (!encodedCourseId) {
            return;
        }

        const csrfName = csrfNameMeta.attr('content');
        const csrfHash = csrfValueMeta.attr('content');

        const payload = {
            course_id: encodedCourseId
        };

        if (csrfName && csrfHash) {
            payload[csrfName] = csrfHash;
        }

        $.ajax({
            url: "<?= base_url('/course/enroll'); ?>",
            type: 'POST',
            data: payload,
            dataType: 'json'
        }).done(function (data) {
            if (data && data.success) {
                $button.prop('disabled', true).text('Enrolled');
                $courseCard.append(`<div class="alert alert-success mt-3">${escapeHtml(data.message || 'Enrollment successful.')}</div>`);

                const courseTitle = $courseCard.find('.card-title').first().text();
                const courseDescription = $courseCard.find('p').first().text();

                $('#enrolledCourses').append(`
                    <div class="col-md-4">
                        <div class="card mt-3">
                            <div class="card-body">
                                <h4 class="card-title">${escapeHtml(courseTitle)}</h4>
                                <h6 class="card-subtitle mb-2 text-muted">Term 1</h6>
                                <p>${escapeHtml(courseDescription)}</p>
                            </div>
                        </div>
                    </div>
                `);

                const decodedCourseId = window.atob(String(encodedCourseId));
                enrollmentStatusMap[String(decodedCourseId)] = 'Enrolled';
            } else {
                $courseCard.append(`<div class="alert alert-danger mt-3">${escapeHtml((data && data.message) || 'Unable to enroll.')}</div>`);
                $button.prop('disabled', true).text('Enrolled');
            }

            updateCsrf(data ? data.csrfHash : null);
        }).fail(function (xhr) {
            if (xhr && xhr.status === 403) {
                alert('Session expired or invalid CSRF. Please refresh the page.');
            }
        });
    });

    const $studentSearchForm = $('#studentSearchForm');
    if ($studentSearchForm.length) {
        const $studentContainer = $('#studentCoursesContainer');
        $('#studentSearchInput').on('keyup', function () {
            const value = $(this).val().toLowerCase();
            $studentContainer.find('.course-card-wrapper').each(function () {
                const $wrapper = $(this);
                $wrapper.toggle($wrapper.text().toLowerCase().indexOf(value) > -1);
            });
        });

        handleCourseSearch($studentSearchForm, '#studentSearchInput', 'student-dashboard', renderStudentCourses);
    }

    const $teacherSearchForm = $('#teacherSearchForm');
    if ($teacherSearchForm.length) {
        const $teacherContainer = $('#teacherCoursesContainer');
        $('#teacherSearchInput').on('keyup', function () {
            const value = $(this).val().toLowerCase();
            $teacherContainer.find('.teacher-course-item').each(function () {
                const $item = $(this);
                $item.toggle($item.text().toLowerCase().indexOf(value) > -1);
            });
        });

        handleCourseSearch($teacherSearchForm, '#teacherSearchInput', 'teacher-dashboard', renderTeacherCourses);
    }

    const $adminCourseSearchForm = $('#adminCourseSearchForm');
    if ($adminCourseSearchForm.length) {
        const $adminCoursesContainer = $('#adminCoursesContainer');
        $('#adminCourseSearchInput').on('keyup', function () {
            const value = $(this).val().toLowerCase();
            $adminCoursesContainer.find('.admin-course-item').each(function () {
                const $item = $(this);
                $item.toggle($item.text().toLowerCase().indexOf(value) > -1);
            });
        });

        handleCourseSearch($adminCourseSearchForm, '#adminCourseSearchInput', 'admin-dashboard', renderAdminCourses);
    }

    const $adminUserSearchInput = $('#adminUserSearchInput');
    if ($adminUserSearchInput.length) {
        $adminUserSearchInput.on('keyup', function () {
            const value = $(this).val().toLowerCase();
            $('#adminUsersTable tbody tr').each(function () {
                const $row = $(this);
                $row.toggle($row.text().toLowerCase().indexOf(value) > -1);
            });
        });
    }
})(window.jQuery);
</script>
