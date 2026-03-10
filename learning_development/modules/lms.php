<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/header.php';

$message = '';
$messageType = 'success';
$username = $_SESSION['username'] ?? null;
$userId = null;
$role = 'employee';

try {
    if ($username) {
        $stmt = $pdo->prepare('SELECT id, role FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $userId = $user['id'];
            $role = $user['role'];
        }
    }
} catch (Exception $e) {
    error_log('Error getting user info: ' . $e->getMessage());
}

// Check for success redirect
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'created') {
        $message = 'Course created successfully!';
    } elseif ($_GET['success'] === 'updated') {
        $message = 'Course updated successfully!';
    } elseif ($_GET['success'] === 'deleted') {
        $message = 'Course deleted successfully!';
    }
    $messageType = 'success';
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_course' && in_array($role, ['admin', 'trainer'])) {
        try {
            $stmt = $pdo->prepare('
                INSERT INTO lms_courses (title, description, category, instructor_id, course_content, duration_hours, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $_POST['title'],
                $_POST['description'] ?? '',
                $_POST['category'] ?? '',
                $userId,
                $_POST['course_content'] ?? '',
                $_POST['duration_hours'] ?? 0,
                'published'
            ]);
            // Redirect after successful creation to prevent form resubmission prompt
            header('Location: lms.php?success=created', true, 303);
            exit;
        } catch (Exception $e) {
            $message = 'Error creating course: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } elseif ($action === 'enroll_course' && $userId) {
        try {
            $stmt = $pdo->prepare('
                INSERT INTO lms_enrollments (user_id, course_id, status)
                VALUES (?, ?, ?)
            ');
            $stmt->execute([$userId, $_POST['course_id'], 'enrolled']);
            $message = 'Enrolled in course successfully!';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $message = 'You are already enrolled in this course.';
                $messageType = 'warning';
            } else {
                $message = 'Error enrolling: ' . $e->getMessage();
                $messageType = 'danger';
            }
        } catch (Exception $e) {
            $message = 'Error enrolling: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } elseif ($action === 'edit_course' && in_array($role, ['admin', 'trainer'])) {
        try {
            $stmt = $pdo->prepare('
                UPDATE lms_courses 
                SET title = ?, description = ?, category = ?, course_content = ?, duration_hours = ?
                WHERE id = ? AND instructor_id = ?
            ');
            $stmt->execute([
                $_POST['title'],
                $_POST['description'] ?? '',
                $_POST['category'] ?? '',
                $_POST['course_content'] ?? '',
                $_POST['duration_hours'] ?? 0,
                $_POST['course_id'],
                $userId
            ]);
            if ($stmt->rowCount() > 0) {
                // Redirect after successful update to prevent form resubmission prompt
                header('Location: lms.php?success=updated', true, 303);
                exit;
            } else {
                $message = 'Course not found or you do not have permission to edit it.';
                $messageType = 'warning';
            }
        } catch (Exception $e) {
            $message = 'Error updating course: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } elseif ($action === 'delete_course' && in_array($role, ['admin', 'trainer'])) {
        try {
            $stmt = $pdo->prepare('
                DELETE FROM lms_courses 
                WHERE id = ? AND instructor_id = ?
            ');
            $stmt->execute([$_POST['course_id'], $userId]);
            if ($stmt->rowCount() > 0) {
                // Redirect after successful deletion to prevent form resubmission prompt
                header('Location: lms.php?success=deleted', true, 303);
                exit;
            } else {
                $message = 'Course not found or you do not have permission to delete it.';
                $messageType = 'warning';
            }
        } catch (Exception $e) {
            $message = 'Error deleting course: ' . $e->getMessage();
            $messageType = 'danger';
        }
    }
}

// Fetch LMS courses
$courses = [];
try {
    $stmt = $pdo->prepare('
        SELECT lc.*, u.full_name as instructor_name,
               COUNT(le.id) as enrollment_count
        FROM lms_courses lc
        LEFT JOIN users u ON lc.instructor_id = u.id
        LEFT JOIN lms_enrollments le ON lc.id = le.course_id
        WHERE lc.status = "published"
        GROUP BY lc.id
        ORDER BY lc.created_at DESC
    ');
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching courses: ' . $e->getMessage());
}

// Fetch user enrollments
$userEnrollments = [];
if ($userId) {
    try {
        $stmt = $pdo->prepare('
            SELECT le.*, lc.title
            FROM lms_enrollments le
            LEFT JOIN lms_courses lc ON le.course_id = lc.id
            WHERE le.user_id = ?
            ORDER BY le.enrollment_date DESC
        ');
        $stmt->execute([$userId]);
        $userEnrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error fetching enrollments: ' . $e->getMessage());
    }
}

?>

<div class="container" style="margin-top:90px; margin-bottom: 40px;">
    <div class="lms-toolbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="m-0">Learning Management System</h2>
            <p class="text-muted mt-2 mb-0">Access online courses and learning materials</p>
        </div>
        <?php if (in_array($role, ['admin', 'trainer'])): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">Create Course</button>
        <?php endif; ?>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($courses): ?>
    <!-- Available Courses -->
    <div class="mb-5">
        <h3 class="mb-3">Available Courses</h3>
        <div class="row g-3">
            <?php $idx = 0; foreach ($courses as $course): $idx++; $delay = ($idx - 1) * 0.08; ?>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm pop-in course-card" style="animation-delay: <?php echo $delay; ?>s;"
                             data-id="<?php echo intval($course['id']); ?>"
                             data-title="<?php echo htmlspecialchars($course['title'], ENT_QUOTES); ?>"
                             data-description="<?php echo htmlspecialchars($course['description'], ENT_QUOTES); ?>"
                             data-category="<?php echo htmlspecialchars($course['category'], ENT_QUOTES); ?>"
                             data-duration="<?php echo intval($course['duration_hours']); ?>"
                             data-instructor="<?php echo htmlspecialchars($course['instructor_name'], ENT_QUOTES); ?>"
                             data-enrollment-count="<?php echo htmlspecialchars($course['enrollment_count'] ?? 0); ?>">
                        <img src="img/placeholder.gif" class="card-img-top" style="height:160px;object-fit:cover;" alt="">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                            <p class="card-text text-muted small mb-2"><?php echo htmlspecialchars(substr($course['description'], 0, 60) . '...'); ?></p>
                            <div class="mb-2">
                                <small class="text-secondary"><strong>Category:</strong> <?php echo htmlspecialchars($course['category'] ?? 'General'); ?></small>
                            </div>
                            <div class="mb-2">
                                <small class="text-secondary"><strong>Duration:</strong> <?php echo htmlspecialchars($course['duration_hours'] ?? 0); ?> hours</small>
                            </div>
                            <div class="mb-3">
                                <small class="text-secondary"><strong>Instructor:</strong> <?php echo htmlspecialchars($course['instructor_name'] ?? 'TBA'); ?></small>
                            </div>
                            <small class="text-muted mb-3"><?php echo htmlspecialchars($course['enrollment_count'] ?? 0); ?> enrolled</small>
                            <div class="mt-auto d-flex gap-2">
                                <?php if ($username): ?>
                                    <form method="POST" class="flex-grow-1">
                                        <input type="hidden" name="action" value="enroll_course">
                                        <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course['id']); ?>">
                                        <button type="submit" class="btn btn-sm btn-primary w-100">Enroll</button>
                                    </form>
                                <?php endif; ?>
                                <?php if (in_array($role, ['admin', 'trainer'])): ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-course-btn" 
                                        data-id="<?php echo intval($course['id']); ?>"
                                        data-title="<?php echo htmlspecialchars($course['title'], ENT_QUOTES); ?>"
                                        data-description="<?php echo htmlspecialchars($course['description'], ENT_QUOTES); ?>"
                                        data-category="<?php echo htmlspecialchars($course['category'], ENT_QUOTES); ?>"
                                        data-duration="<?php echo intval($course['duration_hours']); ?>"
                                        data-content="<?php echo htmlspecialchars($course['course_content'], ENT_QUOTES); ?>">Edit</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-course-btn" 
                                        data-id="<?php echo intval($course['id']); ?>"
                                        data-title="<?php echo htmlspecialchars($course['title'], ENT_QUOTES); ?>">Delete</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- My Enrollments -->
    <?php if ($username && $userEnrollments): ?>
        <div class="mb-5">
            <h3 class="mb-3">My Courses</h3>
            <div class="row g-3">
                <?php foreach ($userEnrollments as $enrollment): ?>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($enrollment['title']); ?></h5>
                                    <span class="badge bg-info"><?php echo ucfirst(htmlspecialchars($enrollment['status'])); ?></span>
                                </div>
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo htmlspecialchars($enrollment['progress_percentage'] ?? 0); ?>%"></div>
                                </div>
                                <p class="text-muted small mt-2 mb-0">Progress: <?php echo htmlspecialchars($enrollment['progress_percentage'] ?? 0); ?>%</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Create/Edit Course Modal -->
<div class="modal fade" id="createCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseModalTitle">Create Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" id="courseAction" value="create_course">
                <input type="hidden" name="course_id" id="courseId" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Course Title</label>
                        <input type="text" id="title" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" id="category" name="category" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="duration_hours" class="form-label">Duration (Hours)</label>
                            <input type="number" id="duration_hours" name="duration_hours" class="form-control" min="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="course_content" class="form-label">Course Content</label>
                        <textarea id="course_content" name="course_content" class="form-control" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="courseSubmitBtn">Create Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Course Modal -->
<div class="modal fade" id="deleteCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the course "<span id="courseToDeleteName"></span>"?</p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="delete_course">
                    <input type="hidden" name="course_id" id="courseIdToDelete" value="">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.pop-in {
    animation: popIn 0.5s ease-out forwards;
}

@keyframes popIn {
    0% {
        opacity: 0;
        transform: scale(0.85);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit course button handler
    document.querySelectorAll('.edit-course-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('courseModalTitle').textContent = 'Edit Course';
            document.getElementById('courseSubmitBtn').textContent = 'Update Course';
            document.getElementById('courseAction').value = 'edit_course';
            document.getElementById('courseId').value = this.dataset.id;
            document.getElementById('title').value = this.dataset.title;
            document.getElementById('description').value = this.dataset.description;
            document.getElementById('category').value = this.dataset.category;
            document.getElementById('duration_hours').value = this.dataset.duration;
            document.getElementById('course_content').value = this.dataset.content;
            
            const modal = new bootstrap.Modal(document.getElementById('createCourseModal'));
            modal.show();
        });
    });

    // Click on card shows details (ignore clicks on buttons/forms)
    document.querySelectorAll('.course-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.closest('button') || e.target.closest('form') || e.target.closest('a')) return;
            document.getElementById('viewCourseTitle').textContent = card.dataset.title || '';
            document.getElementById('viewCourseDescription').textContent = card.dataset.description || '';
            document.getElementById('viewCourseCategory').textContent = card.dataset.category || '';
            document.getElementById('viewCourseDuration').textContent = card.dataset.duration || '';
            document.getElementById('viewCourseInstructor').textContent = card.dataset.instructor || '';
            document.getElementById('viewCourseCount').textContent = card.dataset.enrollmentCount || '0';
            const vmodal = new bootstrap.Modal(document.getElementById('viewCourseModal'));
            vmodal.show();
        });
    });
    
    // Delete course button handler
    document.querySelectorAll('.delete-course-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('courseToDeleteName').textContent = this.dataset.title;
            document.getElementById('courseIdToDelete').value = this.dataset.id;
            const modal = new bootstrap.Modal(document.getElementById('deleteCourseModal'));
            modal.show();
        });
    });
    
    // Reset form when create modal is closed, to prepare for new create
    document.getElementById('createCourseModal').addEventListener('hide.bs.modal', function() {
        document.getElementById('courseModalTitle').textContent = 'Create Course';
        document.getElementById('courseSubmitBtn').textContent = 'Create Course';
        document.getElementById('courseAction').value = 'create_course';
        document.getElementById('courseId').value = '';
        document.getElementById('title').value = '';
        document.getElementById('description').value = '';
        document.getElementById('category').value = '';
        document.getElementById('duration_hours').value = '';
        document.getElementById('course_content').value = '';
    });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
