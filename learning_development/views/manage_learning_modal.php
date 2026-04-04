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
        <h4>Program Details</h4>
        <div class="row">
            <div class="col-md-6"><strong>Title:</strong> <?= htmlspecialchars($program['title']) ?></div>
            <div class="col-md-6"><strong>Trainer:</strong> <?= htmlspecialchars($program['trainer']) ?></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6"><strong>Start Date:</strong> <?= htmlspecialchars($program['start_date']) ?></div>
            <div class="col-md-6"><strong>End Date:</strong> <?= htmlspecialchars($program['end_date']) ?></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6"><strong>Max Participants:</strong> <?= htmlspecialchars($program['max_participants']) ?></div>
            <div class="col-md-6"><strong>Status:</strong> <?= htmlspecialchars(ucfirst($program['status'])) ?></div>
        </div>
        <div class="mt-3"><strong>Description:</strong><br><?= nl2br(htmlspecialchars($program['description'])) ?></div>
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
        <h4>Course Details</h4>
        <div class="row">
            <div class="col-md-6"><strong>Title:</strong> <?= htmlspecialchars($course['title']) ?></div>
            <div class="col-md-6"><strong>Instructor:</strong> <?= htmlspecialchars($course['instructor']) ?></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6"><strong>Duration:</strong> <?= htmlspecialchars($course['duration_hours']) ?> hours</div>
            <div class="col-md-6"><strong>Status:</strong> <?= htmlspecialchars(ucfirst($course['status'])) ?></div>
        </div>
        <div class="mt-3"><strong>Description:</strong><br><?= nl2br(htmlspecialchars($course['description'])) ?></div>
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
