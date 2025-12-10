<title><?= $course['courseCode']?> | <?= esc($course['courseTitle'] ?? 'Course Details') ?></title>

<div class="container-fluid bg-light py-4 px-4 px-md-5 min-vh-100">
	<div class="d-flex flex-column flex-md-row align-items-md-center gap-3 mb-4">
		<a href="<?= base_url('/course/' . $course['courseID']) ?>" class="btn btn-outline-secondary btn-sm shadow-sm">
			Back to Course
		</a>
        <br>
		<div>
			<p class="text-uppercase text-muted small mb-1">Assignments • <?= esc($course['courseCode'] ?? 'Course') ?></p>
			<h2 class="h4 fw-bold mb-1"><?= esc($course['courseTitle'] ?? 'Course Details') ?></h2>
			<div class="text-muted small">
				<?= esc($course['semesterName'] ?? 'Semester N/A'); ?>
				<span class="mx-1">•</span>
				<?= esc($course['schoolYear'] ?? 'SY N/A'); ?>
			</div>
		</div>
	</div>

	<?php if (session()->getFlashdata('message')): ?>
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<?= esc(session()->getFlashdata('message')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<?php if (session()->getFlashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<?= esc(session()->getFlashdata('error')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<?php if (session()->getFlashdata('errors')): ?>
		<div class="alert alert-danger">
			<ul class="mb-0 small">
				<?php foreach (session()->getFlashdata('errors') as $err): ?>
					<li><?= esc($err) ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<div class="row g-4">
		<div class="col-lg-8">
			<?php if (!empty($assignments)): ?>
				<?php foreach ($assignments as $assignment): ?>
					<?php
						$assignmentId = (int) $assignment['AssignmentID'];
						$publishDateRaw = !empty($assignment['publishDate']) ? $assignment['publishDate'] : null;
						$dueDateRaw = !empty($assignment['dueDate']) ? $assignment['dueDate'] : null;
						$publishDate = $publishDateRaw ? date('M d, Y g:i A', strtotime($publishDateRaw)) : 'Not published yet';
						$dueDate = $dueDateRaw ? date('M d, Y g:i A', strtotime($dueDateRaw)) : 'No due date';
						$isOverdue = $dueDateRaw && strtotime($dueDateRaw) < time();
						$isClosed = !empty($assignment['isClosed']);
						$publishInputValue = $publishDateRaw ? date('Y-m-d\TH:i', strtotime($publishDateRaw)) : '';
						$dueInputValue = $dueDateRaw ? date('Y-m-d\TH:i', strtotime($dueDateRaw)) : '';
						$allowedAttempts = !empty($assignment['allowedAttempts']) ? (int) $assignment['allowedAttempts'] : null;
						$studentAttempts = $studentSubmissions[$assignmentId] ?? [];
						$studentAttemptsCount = count($studentAttempts);
						$latestSubmission = $studentAttemptsCount > 0 ? $studentAttempts[0] : null;
						$submissionCount = $submissionCounts[$assignmentId] ?? 0;
					?>
					<div class="card border-0 shadow-sm mb-3">
						<div class="card-body">
							<div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-2">
								<div>
									<h5 class="mb-1 text-primary">
										<?= esc($assignment['title'] ?? $assignment['materialName'] ?? 'Assignment #' . $assignmentId) ?>
									</h5>
									<p class="text-muted small mb-0">Published: <?= esc($publishDate) ?></p>
								</div>
								<div class="text-md-end">
									<span class="badge <?= $isOverdue ? 'bg-danger' : 'bg-secondary' ?>">
										<?= $isOverdue ? 'Past Due' : 'Due' ?>: <?= esc($dueDate) ?>
									</span>
									<?php if (!empty($assignment['autoClose'])): ?>
										<span class="badge bg-info text-dark ms-2">Auto-close</span>
									<?php endif; ?>
									<?php if ($isClosed): ?>
										<span class="badge bg-dark ms-2">Closed</span>
									<?php endif; ?>
								</div>
							</div>


							<?php if (!empty($assignment['Instructions'])): ?>
								<div class="bg-light border rounded-3 p-3 mb-3">
									<p class="text-uppercase text-muted small mb-1">Instructions</p>
									<div class="text-dark small"><?= nl2br(esc($assignment['Instructions'])) ?></div>
								</div>
							<?php endif; ?>

							<div class="row text-muted small g-2">
								<div class="col-md-4">
									<span class="fw-semibold">Attempts:</span>
									<?= $allowedAttempts ? $allowedAttempts : 'Unlimited'; ?>
								</div>
								<?php if ($role === 'student'): ?>
									<div class="col-md-4">
										<span class="fw-semibold">Used:</span> <?= $studentAttemptsCount; ?>
									</div>
									<div class="col-md-4">
										<span class="fw-semibold">Last submission:</span>
										<?= $latestSubmission ? esc(date('M d, Y g:i A', strtotime($latestSubmission['submissionDate']))) : 'None'; ?>
									</div>
								<?php else: ?>
									<div class="col-md-4">
										<span class="fw-semibold">Submissions:</span> <?= $submissionCount; ?>
									</div>
									<div class="col-md-4">
										<span class="fw-semibold">Download:</span>
										<?php if (!empty($assignment['materialIdRef'])): ?>
											<a href="<?= base_url('materials/download/' . $assignment['materialIdRef']) ?>" class="link-primary">File</a>
										<?php else: ?>
											<span class="text-muted">N/A</span>
										<?php endif; ?>
									</div>

									<?php if (!empty($assignment['autoClose']) && !empty($assignment['dueDate'])): ?>
										<p class="text-muted small mt-2 mb-0">Submissions will automatically close when the due date passes.</p>
									<?php endif; ?>
								<?php endif; ?>
							</div>

							<div class="d-flex flex-wrap gap-2 mt-3">
								<?php if (!empty($assignment['materialIdRef'])): ?>
									<a href="<?= base_url('materials/download/' . $assignment['materialIdRef']) ?>" class="btn btn-outline-primary btn-sm">
										Download Assignment
									</a>
								<?php endif; ?>

								<?php if ($role === 'student'): ?>
									<?php if ($isClosed): ?>
										<span class="badge bg-dark align-self-center">Submissions closed</span>
									<?php elseif( !$allowedAttempts || $allowedAttempts && $studentAttemptsCount < $allowedAttempts): ?>
										<button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse"
											data-bs-target="#submit-assignment-<?= $assignmentId ?>" aria-expanded="false">
											Submit / Resubmit
										</button>
										<?php elseif ($allowedAttempts && $studentAttemptsCount >= $allowedAttempts): ?>
											<button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse"
											data-bs-target="#submit-assignment-<?= $assignmentId ?>" aria-expanded="false" disabled>
											Submit / Resubmit
										</button>
                                        <?php endif; ?>
										<?php if ($isOverdue): ?>
											<span class="badge bg-warning text-dark align-self-center">Late submissions</span>
                                    <?php endif; ?>
										<a href="<?= base_url('/course/' . $course['courseID'] . '/assignments/' . $assignmentId . '/submissions') ?>"
											class="btn btn-outline-secondary btn-sm">
											View Submission Details
										</a>
								<?php endif; ?>
								<?php if (in_array($role, ['admin', 'teacher'], true)): ?>
									<a href="<?= base_url('/course/' . $course['courseID'] . '/assignments/' . $assignmentId . '/submissions') ?>"
										class="btn btn-light btn-sm">
										View Submission Details
									</a>
									<form action="<?= base_url('/course/' . $course['courseID'] . '/assignments/' . $assignmentId . '/status') ?>"
										method="post" class="d-inline ms-2">
										<?= csrf_field() ?>
										<input type="hidden" name="status" value="<?= $isClosed ? 'open' : 'closed' ?>">
										<button type="submit" class="btn btn-sm <?= $isClosed ? 'btn-outline-success' : 'btn-outline-danger' ?>">
											<?= $isClosed ? 'Reopen Submissions' : 'Close Submissions' ?>
										</button>
									</form>
								<?php endif; ?>
							</div>

							<?php if ($role === 'student'): ?>
								<?php if ($isClosed): ?>
									<div class="alert alert-dark mt-3 mb-0">Submissions are closed for this assignment.</div>
								<?php else: ?>
									<div class="collapse mt-3" id="submit-assignment-<?= $assignmentId ?>">
										<?php if (!$allowedAttempts || $studentAttemptsCount < $allowedAttempts): ?>
											<form action="<?= base_url('/course/' . $course['courseID'] . '/assignments/submit') ?>" method="post"
												enctype="multipart/form-data" class="border rounded-3 bg-light p-3">
												<?= csrf_field() ?>
												<input type="hidden" name="assignment_id" value="<?= $assignmentId ?>">
												<div class="mb-3">
													<label class="form-label">Upload submission</label>
													<input type="file" name="submission_file" class="form-control" required>
												</div>
												<div class="d-flex justify-content-between align-items-center">
													<span class="small text-muted">Accepted formats: pdf, doc, docx, ppt, pptx, txt, zip, rar</span>
													<button type="submit" class="btn btn-primary btn-sm">Submit</button>
												</div>
											</form>
										<?php else: ?>
											<div class="alert alert-warning mb-0">
												You have reached the allowed number of attempts for this assignment.
											</div>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							<?php else: ?>
								<button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse"
									data-bs-target="#edit-assignment-<?= $assignmentId ?>" aria-expanded="false">
									Edit Details
								</button>
							<?php endif; ?>

							<?php if (in_array($role, ['admin', 'teacher'], true)): ?>
								<div class="collapse mt-3" id="edit-assignment-<?= $assignmentId ?>">
									<form action="<?= base_url('/course/' . $course['courseID'] . '/assignments/' . $assignmentId . '/update') ?>"
										method="post" enctype="multipart/form-data"
										class="border rounded-3 bg-white p-3">
										<?= csrf_field() ?>
										<div class="mb-3">
											<label class="form-label">Replace Assignment File <span class="text-muted small">(optional)</span></label>
											<input type="file" name="assignment_file" class="form-control">
										</div>
										<div class="mb-3">
											<label class="form-label">Title</label>
											<input type="text" name="title" class="form-control" value="<?= esc($assignment['title'] ?? '') ?>" required>
										</div>
										<div class="mb-3">
											<label class="form-label">Instructions</label>
											<textarea name="instructions" class="form-control" rows="4" placeholder="Provide or update instructions"><?= esc($assignment['Instructions'] ?? '') ?></textarea>
										</div>
										<div class="row g-2">
											<div class="col-md-6">
												<label class="form-label">Allowed Attempts</label>
												<input type="number" name="allowedAttempts" class="form-control" min="1" max="9"
													value="<?= esc($allowedAttempts ?? '') ?>" placeholder="Unlimited">
											</div>
											<div class="col-md-6">
												<label class="form-label">Due Date</label>
												<input type="datetime-local" name="dueDate" class="form-control"
													value="<?= esc($dueInputValue) ?>">
												<small class="text-muted">Leave as-is or clear to remove the due date.</small>
											</div>
										</div>
										<div class="form-check form-switch mt-3">
											<input class="form-check-input" type="checkbox" role="switch" name="autoClose"
												id="autoCloseEdit-<?= $assignmentId ?>" value="1" <?= !empty($assignment['autoClose']) ? 'checked' : '' ?>>
											<label class="form-check-label" for="autoCloseEdit-<?= $assignmentId ?>">Automatically close submissions at the due date</label>
										</div>
										<small class="text-muted">Requires a due date to enable auto-close.</small>
										<p class="text-muted small mb-0">Publish date remains fixed to the original upload time.</p>
										<div class="d-flex justify-content-end gap-2 mt-3">
											<button type="button" class="btn btn-light btn-sm" data-bs-toggle="collapse"
												data-bs-target="#edit-assignment-<?= $assignmentId ?>">Cancel</button>
											<button type="submit" class="btn btn-success btn-sm">Save Changes</button>
										</div>
									</form>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<div class="card border-0 shadow-sm">
					<div class="card-body text-center py-5">
						<h5 class="fw-semibold mb-2">No assignments yet</h5>
						<p class="text-muted mb-0">Once assignments are published for this course, they'll appear here.</p>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<div class="col-lg-4">
			<div class="card border-0 shadow-sm mb-4">
				<div class="card-body">
					<h6 class="fw-semibold mb-3">Course</h6>
					<ul class="list-unstyled small text-muted mb-0">
						<li class="mb-2"><span class="fw-semibold text-dark">Code:</span> <?= esc($course['courseCode'] ?? 'N/A') ?></li>
						<li class="mb-2"><span class="fw-semibold text-dark">Teacher:</span> <?= esc($teacher['name'] ?? 'TBA') ?></li>
						<li class="mb-2"><span class="fw-semibold text-dark">School Year:</span> <?= esc($course['schoolYear'] ?? 'N/A') ?></li>
						<li><span class="fw-semibold text-dark">Semester:</span> <?= esc($course['semesterName'] ?? 'N/A') ?></li>
					</ul>
				</div>
			</div>

			<?php if (in_array($role, ['admin', 'teacher'], true)): ?>
				<div class="card border-0 shadow-sm">
					<div class="card-body">
						<h6 class="fw-semibold mb-3">Create Assignment</h6>
						<form action="<?= base_url('/course/' . $course['courseID'] . '/assignments') ?>" method="post" enctype="multipart/form-data">
							<?= csrf_field() ?>
							<div class="mb-3">
								<label class="form-label">Assignment File</label>
								<input type="file" name="assignment_file" class="form-control" required>
							</div>
							<div class="mb-3">
								<label class="form-label">Title</label>
								<input type="text" name="title" class="form-control" value="<?= esc(old('title')) ?>" required>
								<small class="text-muted">Students will see this as the assignment name.</small>
							</div>
							<div class="mb-3">
								<label class="form-label">Instructions <span class="text-muted small">(optional)</span></label>
								<textarea name="instructions" class="form-control" rows="4" placeholder="Describe the task, rubric, or other notes"></textarea>
							</div>
							<div class="mb-3">
								<label class="form-label">Allowed Attempts <span class="text-muted small">(optional)</span></label>
								<input type="number" name="allowedAttempts" class="form-control" min="1" max="9" placeholder="Unlimited">
							</div>
							<div class="row g-2">
								<div class="col-12">
									<label class="form-label">Due Date <span class="text-muted small">(optional)</span></label>
									<input type="datetime-local" name="dueDate" class="form-control"
										value="<?= esc(old('dueDate')) ?>">
									<small class="text-muted d-block">Publish date is set automatically to the current time.</small>
								</div>
							</div>
							<div class="form-check form-switch mt-3">
								<input class="form-check-input" type="checkbox" role="switch" name="autoClose" id="autoCloseCreate" value="1"
									<?= old('autoClose') ? 'checked' : '' ?>>
								<label class="form-check-label" for="autoCloseCreate">Automatically close submissions at the due date</label>
							</div>
							<small class="text-muted">Requires a due date.</small>
							<div class="d-grid mt-3">
								<button type="submit" class="btn btn-success">Upload Assignment</button>
							</div>
						</form>
					</div>
				</div>
			<?php else: ?>
				<div class="card border-0 shadow-sm">
					<div class="card-body">
						<h6 class="fw-semibold mb-3">Reminder!</h6>
						<p class="small text-muted mb-0">Taking a few extra moments to review your work can help prevent mistakes and avoid the need for resubmission.</p>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
