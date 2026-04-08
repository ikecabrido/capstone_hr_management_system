-- Migration Guide: From Procedural to OOP

## Step-by-Step Migration Plan

### Phase 1: Setup OOP Infrastructure (COMPLETED)
- [x] Create database abstraction layer
- [x] Create model classes
- [x] Create repository classes
- [x] Create service classes
- [x] Create utility classes
- [x] Create autoloader

### Phase 2: Create API Endpoints (TO DO)
1. Create `/api/` directory
2. Create API endpoints that use services:
   - `/api/training.php` - Training program endpoints
   - `/api/career.php` - Career path endpoints
   - `/api/performance.php` - Performance review endpoints
   - `/api/user.php` - User management endpoints
   - `/api/leadership.php` - Leadership program endpoints

Example structure:
```php
<?php
require_once __DIR__ . '/../autoload.php';

use HRManagement\Services\TrainingService;
use HRManagement\Utils\AuthManager;
use HRManagement\Utils\ResponseHandler;

AuthManager::startSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ResponseHandler::error('Method not allowed', 405)->send();
}

$service = new TrainingService();
$action = $_POST['action'] ?? '';

match ($action) {
    'create' => handleCreate($service),
    'update' => handleUpdate($service),
    'delete' => handleDelete($service),
    default => ResponseHandler::error('Invalid action')->send(),
};
```

### Phase 3: Refactor Views (TO DO)
1. Update `learning_development.php` to use services
2. Update module files to fetch data through services
3. Remove direct database queries from views
4. Use helper functions for formatting

### Phase 4: Update Modules (TO DO)
Refactor existing modules:
1. `modules/training.php` → Use TrainingService
2. `modules/career.php` → Use CareerDevelopmentService
3. `modules/performance.php` → Use PerformanceService
4. `modules/leadership.php` → Use LeadershipService
5. `modules/analytics.php` → Use repository queries

### Phase 5: Testing & Deployment (TO DO)
1. Write unit tests for services
2. Test all CRUD operations
3. Test authorization checks
4. Performance testing
5. Deploy to production

## Code Migration Examples

### Example 1: Migrating Training Create

#### BEFORE (Procedural):
```php
<?php
require_once __DIR__ . '/config.php';

if ($_POST['action'] === 'create') {
    $name = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? 'General');
    $duration = intval($_POST['duration'] ?? 0);
    
    $stmt = $pdo->prepare('
        INSERT INTO training_programs (name, description, category, duration, created_by, status)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([$name, $description, $category, $duration, $userId, 'active']);
    $programId = $pdo->lastInsertId();
}
?>
```

#### AFTER (OOP):
```php
<?php
require_once __DIR__ . '/../autoload.php';

use HRManagement\Services\TrainingService;
use HRManagement\Utils\AuthManager;
use HRManagement\Utils\ResponseHandler;

AuthManager::startSession();

if ($_POST['action'] === 'create') {
    try {
        $service = new TrainingService();
        $programId = $service->createProgram([
            'name' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'category' => $_POST['category'] ?? 'General',
            'duration' => $_POST['duration'] ?? 0,
        ], AuthManager::getCurrentUserId());
        
        ResponseHandler::created(['id' => $programId])->send();
    } catch (Exception $e) {
        ResponseHandler::error($e->getMessage())->send();
    }
}
?>
```

### Example 2: Migrating User Search

#### BEFORE:
```php
<?php
$searchTerm = $_GET['q'] ?? '';
$stmt = $pdo->prepare('SELECT * FROM users WHERE full_name LIKE ? OR username LIKE ? LIMIT 20');
$stmt->execute(["%$searchTerm%", "%$searchTerm%"]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

#### AFTER:
```php
<?php
use HRManagement\Services\UserService;

$service = new UserService();
$users = $service->searchUsers($_GET['q'] ?? '');

// Convert to array if needed for views
$usersArray = array_map(fn($u) => $u->toArray(), $users);
?>
```

### Example 3: Migrating Permission Check

#### BEFORE:
```php
<?php
$isAuthorized = in_array($_SESSION['role'], ['admin', 'manager', 'learning']);

if (!$isAuthorized) {
    echo "Access denied";
}
?>
```

#### AFTER:
```php
<?php
use HRManagement\Utils\AuthManager;

if (!AuthManager::isLearningAdmin()) {
    ResponseHandler::forbidden()->send();
}
?>
```

## Backward Compatibility Notes

1. **Database Changes**: The OOP layer wraps existing tables, no schema changes needed
2. **Session Structure**: AuthManager supports both old and new session formats
3. **Existing Code**: Can coexist with OOP code during migration
4. **Database Config**: Update db credentials in Database.php

## Testing Checklist

- [ ] Create user and verify authentication
- [ ] Create training program
- [ ] Enroll/unenroll user in training
- [ ] Update enrollment progress
- [ ] Create career path and IDP
- [ ] Create performance review
- [ ] Create leadership program
- [ ] Test search functionality
- [ ] Test permission restrictions
- [ ] Test form validation
- [ ] Test error handling

## Performance Optimization Tips

1. Use pagination for large datasets
2. Cache frequently accessed data
3. Optimize database queries with proper indexes
4. Use eager loading for related data
5. Consider lazy loading for heavy relations

## Additional Resources

- See `OOP_ARCHITECTURE.md` for detailed architecture guide
- See `QUICK_REFERENCE.md` for code examples
- Check `refactored_modules/` for example implementations
