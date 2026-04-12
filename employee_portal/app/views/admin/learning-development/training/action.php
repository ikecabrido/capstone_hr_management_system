<?php 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!is_logged_in()) {
    $message = 'You must be logged in to perform this action.';
    $messageType = 'danger';
  } elseif (in_array($_POST['action'] ?? '', ['create', 'edit', 'delete']) && !$isAuthorized) {
    $message = 'You do not have permission to manage programs. Only administrators and managers can create, edit, or delete programs.';
    $messageType = 'danger';
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  try {
    // Create training program
    if ($action === 'create' && in_array(current_role(), ['admin', 'manager', 'learning'])) {
      if (!$currentUserId) {
        // sessions or DB state are inconsistent - require a logged-in user
        $message = 'Cannot create program: you must be logged in as an admin, manager, or learning admin.';
        $messageType = 'danger';
      } else {
        $name = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $date = trim($_POST['date'] ?? '');
        $capacity = intval($_POST['capacity'] ?? 0);
        $location = trim($_POST['location'] ?? '');
        $trainer = trim($_POST['trainer'] ?? '');
        $status = trim($_POST['status'] ?? 'Active');
        $sessions = intval($_POST['sessions'] ?? 1);

        // Handle image upload
        $cover_photo = null;
        if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
          $uploadResult = uploadImage($_FILES['cover_photo'], 'training', 2 * 1024 * 1024);
          if ($uploadResult['success']) {
            $cover_photo = $uploadResult['path'];
          } else {
            throw new Exception('Image upload failed: ' . $uploadResult['error']);
          }
        }

        $creatorId = $currentUserId;

        $stmt = $pdo->prepare('
                INSERT INTO training_programs (name, description, category, type, duration, created_by, status, cover_photo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ');
        $stmt->execute([$name, $description, 'General', 'Workshop', $sessions, $creatorId, $status, $cover_photo]);
        $programId = $pdo->lastInsertId();

        // Store extra fields in a JSON meta field (we'll use description for now, but ideally add course_content)
        $message = 'Program created successfully.';
        $messageType = 'success';
      }
    }

    // Edit training program
    if ($action === 'edit' && in_array(current_role(), ['admin', 'manager', 'learning'])) {
      $id = intval($_POST['id'] ?? 0);
      $name = trim($_POST['title'] ?? '');
      $description = trim($_POST['description'] ?? '');
      $date = trim($_POST['date'] ?? '');
      $capacity = intval($_POST['capacity'] ?? 0);
      $location = trim($_POST['location'] ?? '');
      $trainer = trim($_POST['trainer'] ?? '');
      $status = trim($_POST['status'] ?? 'Active');
      $sessions = intval($_POST['sessions'] ?? 1);

      // Handle image upload
      $cover_photo = null;
      if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
        // Get existing image to delete old one
        $existing = $pdo->prepare('SELECT cover_photo FROM training_programs WHERE id = ?');
        $existing->execute([$id]);
        $old_image = $existing->fetch(PDO::FETCH_ASSOC)['cover_photo'] ?? null;

        $uploadResult = uploadImage($_FILES['cover_photo'], 'training', 2 * 1024 * 1024);
        if ($uploadResult['success']) {
          $cover_photo = $uploadResult['path'];
          // Delete old image if new one uploaded
          if ($old_image && file_exists($old_image)) {
            deleteImage($old_image);
          }
        } else {
          throw new Exception('Image upload failed: ' . $uploadResult['error']);
        }
      }

      if ($cover_photo) {
        $stmt = $pdo->prepare('
                UPDATE training_programs 
                SET name = ?, description = ?, category = ?, type = ?, duration = ?, status = ?, cover_photo = ?
                WHERE id = ?
            ');
        $stmt->execute([$name, $description, 'General', $trainer, $sessions, $status, $cover_photo, $id]);
      } else {
        $stmt = $pdo->prepare('
                UPDATE training_programs 
                SET name = ?, description = ?, category = ?, type = ?, duration = ?, status = ?
                WHERE id = ?
            ');
        $stmt->execute([$name, $description, 'General', $trainer, $sessions, $status, $id]);
      }
      $message = 'Program updated.';
      $messageType = 'success';
    }

    // Delete training program
    if ($action === 'delete' && in_array(current_role(), ['admin', 'manager', 'learning'])) {
      $id = intval($_POST['id'] ?? 0);

      // Delete enrollments first
      $stmt = $pdo->prepare('DELETE FROM training_enrollments WHERE program_id = ?');
      $stmt->execute([$id]);

      // Delete program
      $stmt = $pdo->prepare('DELETE FROM training_programs WHERE id = ?');
      $stmt->execute([$id]);
      $message = 'Program deleted.';
      $messageType = 'success';
    }

    // Enroll user
    if ($action === 'enroll' && $currentUserId) {
      $programId = intval($_POST['id'] ?? 0);

      // Check if already enrolled
      $stmt = $pdo->prepare('SELECT id FROM training_enrollments WHERE user_id = ? AND program_id = ?');
      $stmt->execute([$currentUserId, $programId]);
      $existing = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($existing) {
        $message = 'Already enrolled.';
        $messageType = 'warning';
      } else {
        $stmt = $pdo->prepare('
                INSERT INTO training_enrollments (user_id, program_id, status)
                VALUES (?, ?, ?)
            ');
        $stmt->execute([$currentUserId, $programId, 'pending']);
        $message = 'Enrolled successfully.';
        $messageType = 'success';
      }
    }

    // Unenroll user
    if ($action === 'unenroll' && $currentUserId) {
      $programId = intval($_POST['id'] ?? 0);

      $stmt = $pdo->prepare('DELETE FROM training_enrollments WHERE user_id = ? AND program_id = ?');
      $stmt->execute([$currentUserId, $programId]);
      $message = 'Unenrolled.';
      $messageType = 'success';
    }

    // Admin actions: ban/exempt/remove/unban
    if (in_array(current_role(), ['admin', 'manager', 'trainer'])) {
      if ($action === 'ban_user') {
        $programId = intval($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');

        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
          $stmt = $pdo->prepare('UPDATE training_enrollments SET status = ? WHERE user_id = ? AND program_id = ?');
          $stmt->execute(['banned', $user['id'], $programId]);
          $message = 'User banned.';
          $messageType = 'success';
        }
      }

      if ($action === 'unban_user') {
        $programId = intval($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');

        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
          $stmt = $pdo->prepare('UPDATE training_enrollments SET status = ? WHERE user_id = ? AND program_id = ?');
          $stmt->execute(['pending', $user['id'], $programId]);
          $message = 'User unbanned.';
          $messageType = 'success';
        }
      }

      if ($action === 'exempt_user') {
        $programId = intval($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');

        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
          $stmt = $pdo->prepare('UPDATE training_enrollments SET status = ? WHERE user_id = ? AND program_id = ?');
          $stmt->execute(['exempt', $user['id'], $programId]);
          $message = 'User exempted.';
          $messageType = 'success';
        }
      }

      if ($action === 'remove_user') {
        $programId = intval($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');

        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
          $stmt = $pdo->prepare('DELETE FROM training_enrollments WHERE user_id = ? AND program_id = ?');
          $stmt->execute([$user['id'], $programId]);
          $message = 'User removed from enrollment.';
          $messageType = 'success';
        }
      }
    }
  } catch (Exception $e) {
    // log full exception for server-side diagnosis
    error_log('Training module error: ' . $e->getMessage());
    // expose basic exception message to user when in development
    $debugMsg = !empty($e->getMessage()) ? ' (' . htmlspecialchars($e->getMessage()) . ')' : '';
    $message = 'An error occurred. Please try again.' . $debugMsg;
    $messageType = 'danger';
  }
}