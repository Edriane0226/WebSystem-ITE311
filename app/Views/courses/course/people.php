<title><?= $course['courseCode']?> | People</title>

<?php
	$currentUserId = (int) (session()->get('userID') ?? 0);
	$isTeacher = $role === 'teacher';
	$isStudent = $role === 'student';
	$enrolledStatus = \App\Models\EnrollmentModel::STATUS_ENROLLED;

	$visibleStudents = array_values(array_filter($students ?? [], static function ($student) use ($enrolledStatus) {
		return (int) ($student['enrollmentStatus'] ?? 0) === $enrolledStatus;
	}));

	if ($isStudent) {
		$visibleStudents = array_values(array_filter($visibleStudents, static function ($student) use ($currentUserId) {
			return (int) ($student['userID'] ?? 0) !== $currentUserId;
		}));
	}

	$sectionTitle = $isTeacher ? 'Enrolled Students' : 'Classmates';
?>

<div class="container-fluid bg-light py-4 px-4 px-md-5 min-vh-100">
	<div class="d-flex flex-column flex-md-row align-items-md-center gap-3 mb-4">
		<a href="<?= base_url('/course/' . $course['courseID']) ?>" class="btn btn-outline-secondary btn-sm shadow-sm">
			Back to Course
		</a>
		<div>
			<p class="text-uppercase text-muted small mb-1">People • <?= esc($course['courseCode'] ?? 'Course') ?></p>
			<h2 class="h4 fw-bold mb-1"><?= esc($course['courseTitle'] ?? 'Course Details') ?></h2>
			<div class="text-muted small">
				<?= esc($course['semesterName'] ?? 'Semester N/A'); ?>
				<span class="mx-1">•</span>
				<?= esc($course['schoolYear'] ?? 'SY N/A'); ?>
			</div>
		</div>
	</div>

	<?php if ($teacher): ?>
		<div class="card border-0 shadow-sm mb-4">
			<div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
				<div>
					<p class="text-uppercase text-muted small mb-1">Teacher</p>
					<h5 class="fw-semibold mb-0"><?= esc($teacher['name']) ?></h5>
					<a href="mailto:<?= esc($teacher['email']) ?>" class="text-muted small"><?= esc($teacher['email']) ?></a>
				</div>
				<?php if ($isStudent): ?>
					<span class="badge bg-secondary align-self-start align-self-md-center">Course Instructor</span>
				<?php endif; ?>
			</div>
		</div>
	<?php else: ?>
		<div class="alert alert-warning border-0 shadow-sm mb-4">
			The teacher for this course has not been assigned yet.
		</div>
	<?php endif; ?>

	<div class="card border-0 shadow-sm">
		<div class="card-body">
			<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
				<div>
					<h5 class="fw-semibold mb-1"><?= esc($sectionTitle) ?></h5>
					<p class="text-muted small mb-0">
						<?php if ($isTeacher): ?>
							These students are currently enrolled in this course.
						<?php else: ?>
							These are the classmates enrolled alongside you.
						<?php endif; ?>
					</p>
				</div>
				<span class="badge bg-primary"><?= esc(count($visibleStudents)) ?> total</span>
			</div>

			<?php if (!empty($visibleStudents)): ?>
				<div class="table-responsive">
					<table class="table table-hover align-middle mb-0">
						<thead class="table-light">
							<tr>
								<th scope="col">Student</th>
								<th scope="col" class="text-muted">Email</th>
								<?php if ($isTeacher): ?>
									<th scope="col" class="text-muted">Status</th>
								<?php endif; ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($visibleStudents as $student): ?>
								<?php
									$statusName = $student['statusName'] ?? 'Enrolled';
									$statusClasses = [
										'Enrolled' => 'bg-success',
										'Pending' => 'bg-warning text-dark',
										'Completed' => 'bg-primary',
										'Dropped' => 'bg-secondary'
									];
									$statusClass = $statusClasses[$statusName] ?? 'bg-secondary';
								?>
								<tr>
									<td class="fw-semibold"><?= esc($student['name'] ?? 'Student') ?></td>
									<td>
										<?php if (!empty($student['email'])): ?>
											<a href="mailto:<?= esc($student['email']) ?>" class="text-decoration-none"><?= esc($student['email']) ?></a>
										<?php else: ?>
											<span class="text-muted">No email on record</span>
										<?php endif; ?>
									</td>
									<?php if ($isTeacher): ?>
										<td><span class="badge <?= esc($statusClass) ?>"><?= esc($statusName) ?></span></td>
									<?php endif; ?>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php else: ?>
				<div class="text-center py-5">
					<?php if ($isTeacher): ?>
						<h6 class="fw-semibold mb-2">No students enrolled yet</h6>
						<p class="text-muted small mb-0">Once students are enrolled, they will appear in this list.</p>
					<?php else: ?>
						<h6 class="fw-semibold mb-2">No classmates to display</h6>
						<p class="text-muted small mb-0">You are currently the only enrolled student in this course.</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
