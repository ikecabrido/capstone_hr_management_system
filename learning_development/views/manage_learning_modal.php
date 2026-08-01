<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../controllers/TrainingProgramController.php";
require_once "../controllers/CourseController.php";

// Only learning or admin users
if (!in_array($_SESSION['user']['role'], ['learning', 'admin'])) {
    http_response_code(403);
    echo '<div class="alert alert-danger">Access denied.</div>';
    exit;
}

$type = $_GET['type'] ?? 'program';
$view_id = $_GET['view_id'] ?? null;
$edit_id = $_GET['edit_id'] ?? null;

$programController = new TrainingProgramController();
$courseController = new CourseController();

if ($type === 'program') {
    if ($view_id) {
        // View mode: read-only display
        $program = $programController->show($view_id);
        if (!$program) {
            echo '<div class="alert alert-danger">Program not found.</div>';
            exit;
        }
        ?>
        <div class="program-details">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="mb-3"><i class="fas fa-graduation-cap text-primary mr-2"></i><?= htmlspecialchars($program['title']) ?></h4>
                    <p class="text-muted mb-4"><?= nl2br(htmlspecialchars($program['description'])) ?></p>
                </div>
                <div class="col-md-4">
                    <!-- Progress Card -->
                    <div class="card border-info mb-3">
                        <div class="card-body text-center">
                            <h6 class="card-title mb-3"><i class="fas fa-tasks text-info mr-1"></i>Progress</h6>
                            <div style="position: relative; width: 120px; height: 120px; margin: 0 auto; background: conic-gradient(#28a745 0deg 180deg, #e9ecef 180deg 360deg); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <div style="width: 105px; height: 105px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <span style="font-size: 32px; font-weight: bold; color: #28a745;">50%</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="mb-2">
                                    <i class="fas fa-check-circle text-success mr-2"></i><small>Test: <strong>Answered</strong></small>
                                </div>
                                <div>
                                    <i class="fas fa-times-circle text-secondary mr-2"></i><small>Quiz: <strong>Not Answered</strong></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-primary">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-info-circle text-primary mr-1"></i>Program Info</h6>
                            <div class="mb-2"><strong>Trainer:</strong> <?= htmlspecialchars($program['trainer']) ?></div>
                            <div class="mb-2"><strong>Start Date:</strong> <?= date('M d, Y', strtotime($program['start_date'])) ?></div>
                            <div class="mb-2"><strong>End Date:</strong> <?= date('M d, Y', strtotime($program['end_date'])) ?></div>
                            <div class="mb-2"><strong>Max Participants:</strong> <?= htmlspecialchars($program['max_participants']) ?></div>
                            <div class="mb-2"><strong>Test/Quiz:</strong> 1 Test</div>
                            <div class="mb-2"><strong>Status:</strong>
                                <span class="badge badge-<?= $program['status'] === 'active' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($program['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courses in this program -->
            <?php
            $programCourses = array_filter($allCourses ?? [], function($course) use ($view_id) {
                return $course['ld_training_programs_id'] == $view_id;
            });
            if (!empty($programCourses)):
            ?>
            <div class="mt-4">
                <h5><i class="fas fa-list text-info mr-2"></i>Courses in this Program</h5>
                <div class="row">
                    <?php foreach ($programCourses as $course): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title"><?= htmlspecialchars($course['title']) ?></h6>
                                <p class="card-text small text-muted"><?= htmlspecialchars(substr($course['description'], 0, 100)) ?>...</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-user mr-1"></i><?= htmlspecialchars($course['instructor']) ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-clock mr-1"></i><?= $course['duration_hours'] ?>h
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Test Section (Answered) -->
            <div class="mt-4">
                <h5 class="mb-3"><i class="fas fa-file-alt text-primary mr-2"></i>Test</h5>
                <div class="card border-primary">
                    <div class="card-header bg-light" style="border-bottom: 1px solid #dee2e6;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Program Assessment Test</h6>
                            <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Answered</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p><strong>Q1:</strong> What is the primary goal of strategic planning?</p>
                            <p class="ml-3 text-success"><i class="fas fa-check mr-2"></i><strong>Your Answer:</strong> To align organizational resources with long-term objectives</p>
                        </div>
                        <div class="mb-3">
                            <p><strong>Q2:</strong> Name two key components of effective team management.</p>
                            <p class="ml-3 text-success"><i class="fas fa-check mr-2"></i><strong>Your Answer:</strong> Communication and accountability</p>
                        </div>
                        <div>
                            <p><strong>Q3:</strong> How would you handle a conflict between team members?</p>
                            <p class="ml-3 text-success"><i class="fas fa-check mr-2"></i><strong>Your Answer:</strong> By facilitating open dialogue and finding common ground</p>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Test completed on May 05, 2026 at 2:30 PM</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Section (Interactive) -->
            <div class="mt-4">
                <h5 class="mb-3"><i class="fas fa-question-circle text-warning mr-2"></i>Quiz</h5>
                <form id="programQuizForm" class="card border-warning">
                    <div class="card-header bg-light" style="border-bottom: 1px solid #dee2e6;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Program Knowledge Quiz</h6>
                            <span class="badge badge-warning"><i class="fas fa-hourglass-start mr-1"></i>Pending</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <p><strong>Q1:</strong> What are the benefits of continuous learning?</p>
                            <div class="ml-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q1" id="q1_a" value="a">
                                    <label class="form-check-label" for="q1_a">Improved skills and career growth</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q1" id="q1_b" value="b">
                                    <label class="form-check-label" for="q1_b">Better problem-solving abilities</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q1" id="q1_c" value="c">
                                    <label class="form-check-label" for="q1_c">All of the above</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <p><strong>Q2:</strong> Which of the following is not a leadership skill?</p>
                            <div class="ml-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q2" id="q2_a" value="a">
                                    <label class="form-check-label" for="q2_a">Time management</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q2" id="q2_b" value="b">
                                    <label class="form-check-label" for="q2_b">Emotional intelligence</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q2" id="q2_c" value="c">
                                    <label class="form-check-label" for="q2_c">Social media marketing</label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-warning"><i class="fas fa-paper-plane mr-2"></i>Submit Quiz</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
        exit;
    } elseif ($edit_id) {
        // Edit mode: editable form
        $program = $programController->show($edit_id);
        if (!$program) {
            echo '<div class="alert alert-danger">Program not found.</div>';
            exit;
        }
        ?>
        <h4>Edit Training Program</h4>
        <form id="manageProgramForm" method="post" action="process_edit_program.php">
          <input type="hidden" name="id" value="<?= htmlspecialchars($edit_id) ?>">
          <div class="form-group"><label>Title</label><input name="title" class="form-control" value="<?= htmlspecialchars($program['title']) ?>"></div>
          <div class="form-group"><label>Description</label><textarea name="description" class="form-control"><?= htmlspecialchars($program['description']) ?></textarea></div>
          <div class="form-group"><label>Trainer</label><input name="trainer" class="form-control" value="<?= htmlspecialchars($program['trainer']) ?>"></div>
          <div class="form-group"><label>Start Date</label><input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($program['start_date']) ?>"></div>
          <div class="form-group"><label>End Date</label><input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($program['end_date']) ?>"></div>
          <div class="form-group"><label>Max Participants</label><input type="number" name="max_participants" class="form-control" value="<?= htmlspecialchars($program['max_participants']) ?>"></div>
          <button type="submit" class="btn btn-primary">Update</button>
        </form>
        <?php
        exit;
    } else {
        // Create mode
        ?>
        <h4>Create Training Program</h4>
        <form id="manageProgramForm" method="post" action="process_create_program.php">
          <div class="form-group"><label>Title</label><input name="title" class="form-control"></div>
          <div class="form-group"><label>Description</label><textarea name="description" class="form-control"></textarea></div>
          <div class="form-group"><label>Trainer</label><input name="trainer" class="form-control"></div>
          <div class="form-group"><label>Start Date</label><input type="date" name="start_date" class="form-control"></div>
          <div class="form-group"><label>End Date</label><input type="date" name="end_date" class="form-control"></div>
          <div class="form-group"><label>Max Participants</label><input type="number" name="max_participants" class="form-control"></div>
          <input type="hidden" name="created_by" value="<?= htmlspecialchars($_SESSION['user']['id']) ?>">
          <button type="submit" class="btn btn-primary">Create</button>
        </form>
        <?php
        exit;
    }
}

if ($type === 'course') {
    if ($view_id) {
        // View mode: read-only display
        $course = $courseController->show($view_id);
        if (!$course) {
            echo '<div class="alert alert-danger">Course not found.</div>';
            exit;
        }
        ?>
        <div class="course-details">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="mb-3"><i class="fas fa-book text-success mr-2"></i><?= htmlspecialchars($course['title']) ?></h4>
                    <p class="text-muted mb-4"><?= nl2br(htmlspecialchars($course['description'])) ?></p>
                </div>
                <div class="col-md-4">
                    <!-- Progress Card -->
                    <div class="card border-info mb-3">
                        <div class="card-body text-center">
                            <h6 class="card-title mb-3"><i class="fas fa-tasks text-info mr-1"></i>Progress</h6>
                            <div style="position: relative; width: 120px; height: 120px; margin: 0 auto; background: conic-gradient(#28a745 0deg 180deg, #e9ecef 180deg 360deg); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <div style="width: 105px; height: 105px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <span style="font-size: 32px; font-weight: bold; color: #28a745;">50%</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="mb-2">
                                    <i class="fas fa-check-circle text-success mr-2"></i><small>Test: <strong>Answered</strong></small>
                                </div>
                                <div>
                                    <i class="fas fa-times-circle text-secondary mr-2"></i><small>Quiz: <strong>Not Answered</strong></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-info-circle text-success mr-1"></i>Course Info</h6>
                            <div class="mb-2"><strong>Instructor:</strong> <?= htmlspecialchars($course['instructor']) ?></div>
                            <div class="mb-2"><strong>Duration:</strong> <?= htmlspecialchars($course['duration_hours']) ?> hours</div>
                            <div class="mb-2"><strong>Content Type:</strong> <?= htmlspecialchars(ucfirst($course['content_type'] ?? 'N/A')) ?></div>
                            <div class="mb-2"><strong>Test/Quiz:</strong> 1 Quiz</div>
                            <div class="mb-2"><strong>Status:</strong>
                                <span class="badge badge-<?= $course['status'] === 'active' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($course['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Program association -->
            <?php if (!empty($course['ld_training_programs_id'])): ?>
            <div class="mt-4">
                <h5><i class="fas fa-graduation-cap text-primary mr-2"></i>Part of Training Program</h5>
                <?php
                $programController = new TrainingProgramController();
                $associatedProgram = $programController->show($course['ld_training_programs_id']);
                if ($associatedProgram):
                ?>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title"><?= htmlspecialchars($associatedProgram['title']) ?></h6>
                        <p class="card-text small text-muted"><?= htmlspecialchars(substr($associatedProgram['description'], 0, 150)) ?>...</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-user mr-1"></i><?= htmlspecialchars($associatedProgram['trainer']) ?>
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-calendar mr-1"></i><?= date('M d, Y', strtotime($associatedProgram['start_date'])) ?> - <?= date('M d, Y', strtotime($associatedProgram['end_date'])) ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Test Section (Answered) -->
            <div class="mt-4">
                <h5 class="mb-3"><i class="fas fa-file-alt text-primary mr-2"></i>Test</h5>
                <div class="card border-primary">
                    <div class="card-header bg-light" style="border-bottom: 1px solid #dee2e6;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Course Assessment Test</h6>
                            <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Answered</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p><strong>Q1:</strong> What is the main topic of this course?</p>
                            <p class="ml-3 text-success"><i class="fas fa-check mr-2"></i><strong>Your Answer:</strong> Professional development and skill enhancement</p>
                        </div>
                        <div class="mb-3">
                            <p><strong>Q2:</strong> List three key concepts covered in this course.</p>
                            <p class="ml-3 text-success"><i class="fas fa-check mr-2"></i><strong>Your Answer:</strong> Critical thinking, problem-solving, and collaboration</p>
                        </div>
                        <div>
                            <p><strong>Q3:</strong> How will you apply what you've learned?</p>
                            <p class="ml-3 text-success"><i class="fas fa-check mr-2"></i><strong>Your Answer:</strong> By implementing these techniques in daily work tasks</p>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Test completed on May 04, 2026 at 3:45 PM</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Section (Interactive) -->
            <div class="mt-4">
                <h5 class="mb-3"><i class="fas fa-question-circle text-warning mr-2"></i>Quiz</h5>
                <form id="courseQuizForm" class="card border-warning">
                    <div class="card-header bg-light" style="border-bottom: 1px solid #dee2e6;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Course Knowledge Quiz</h6>
                            <span class="badge badge-warning"><i class="fas fa-hourglass-start mr-1"></i>Pending</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <p><strong>Q1:</strong> What is the main objective of this course?</p>
                            <div class="ml-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q1" id="cq1_a" value="a">
                                    <label class="form-check-label" for="cq1_a">To provide theoretical knowledge</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q1" id="cq1_b" value="b">
                                    <label class="form-check-label" for="cq1_b">To develop practical skills</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q1" id="cq1_c" value="c">
                                    <label class="form-check-label" for="cq1_c">Both theoretical and practical knowledge</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <p><strong>Q2:</strong> Which concept was most important in this course?</p>
                            <div class="ml-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q2" id="cq2_a" value="a">
                                    <label class="form-check-label" for="cq2_a">Understanding fundamentals</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q2" id="cq2_b" value="b">
                                    <label class="form-check-label" for="cq2_b">Applying techniques</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q2" id="cq2_c" value="c">
                                    <label class="form-check-label" for="cq2_c">All are equally important</label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-warning"><i class="fas fa-paper-plane mr-2"></i>Submit Quiz</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
        exit;
    } elseif ($edit_id) {
        // Edit mode: editable form
        $course = $courseController->show($edit_id);
        if (!$course) {
            echo '<div class="alert alert-danger">Course not found.</div>';
            exit;
        }
        ?>
        <h4>Edit Course</h4>
        <form id="manageCourseForm" method="post" action="process_edit_course.php">
          <input type="hidden" name="id" value="<?= htmlspecialchars($edit_id) ?>">
          <div class="form-group"><label>Title</label><input name="title" class="form-control" value="<?= htmlspecialchars($course['title']) ?>"></div>
          <div class="form-group"><label>Description</label><textarea name="description" class="form-control"><?= htmlspecialchars($course['description']) ?></textarea></div>
          <div class="form-group"><label>Instructor</label><input name="instructor" class="form-control" value="<?= htmlspecialchars($course['instructor']) ?>"></div>
          <div class="form-group"><label>Duration Hours</label><input type="number" name="duration_hours" class="form-control" value="<?= htmlspecialchars($course['duration_hours']) ?>"></div>
          <div class="form-group"><label>Program</label><input type="text" name="training_program_id" class="form-control" value="<?= htmlspecialchars($course['training_program_id'] ?? '') ?>"></div>
          <div class="form-group"><label>Content Type</label><input name="content_type" class="form-control" value="<?= htmlspecialchars($course['content_type'] ?? '') ?>"></div>
          <button type="submit" class="btn btn-primary">Update</button>
        </form>
        <?php
        exit;
    } else {
        // Create mode
        ?>
        <h4>Create Course</h4>
        <form id="manageCourseForm" method="post" action="process_create_course.php">
          <div class="form-group"><label>Title</label><input name="title" class="form-control"></div>
          <div class="form-group"><label>Description</label><textarea name="description" class="form-control"></textarea></div>
          <div class="form-group"><label>Instructor</label><input name="instructor" class="form-control"></div>
          <div class="form-group"><label>Duration Hours</label><input type="number" name="duration_hours" class="form-control"></div>
          <div class="form-group"><label>Program</label><input type="text" name="training_program_id" class="form-control"></div>
          <div class="form-group"><label>Content Type</label><input name="content_type" class="form-control"></div>
          <input type="hidden" name="created_by" value="<?= htmlspecialchars($_SESSION['user']['id']) ?>">
          <button type="submit" class="btn btn-primary">Create</button>
        </form>
        <?php
        exit;
    }
}

// fallback
echo '<div class="alert alert-danger">Invalid modal action.</div>';
