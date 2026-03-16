# OOP Architecture - Quick Reference Guide

## Quick Start

### 1. Import Autoloader
```php
require_once 'autoload.php';
```

### 2. Use Services (Business Logic)
```php
use HRManagement\Services\TrainingService;
use HRManagement\Services\UserService;
use HRManagement\Services\CareerDevelopmentService;
use HRManagement\Services\PerformanceService;
use HRManagement\Services\LeadershipService;
```

### 3. Use Utilities
```php
use HRManagement\Utils\AuthManager;
use HRManagement\Utils\Validator;
use HRManagement\Utils\ResponseHandler;
use HRManagement\Utils\Helper;
```

## Common Tasks

### User Management

#### Register User
```php
$userService = new UserService();
$userId = $userService->register([
    'username' => 'john_doe',
    'email' => 'john@example.com',
    'password' => 'SecurePass123',
    'full_name' => 'John Doe',
    'role' => 'employee',
    'department' => 'Engineering'
]);
```

#### Authenticate User
```php
$user = $userService->authenticate('john_doe', 'SecurePass123');
if ($user) {
    AuthManager::login($user->getId(), $user->toArray());
}
```

#### Change Password
```php
$userService->changePassword($userId, 'NewPassword123');
```

#### Get User Details
```php
$userDetails = $userService->getUserWithDetails($userId);
```

#### Search Users
```php
$results = $userService->searchUsers('john', 20);
```

### Training Program Management

#### Create Program
```php
$trainingService = new TrainingService();
$programId = $trainingService->createProgram([
    'name' => 'PHP Advanced Course',
    'description' => 'Learn advanced PHP concepts',
    'category' => 'Technical',
    'type' => 'Online',
    'duration' => 40,
    'status' => 'active'
], $userId);
```

#### Update Program
```php
$trainingService->updateProgram($programId, [
    'name' => 'Updated Name',
    'status' => 'inactive'
]);
```

#### Enroll User
```php
$enrollmentId = $trainingService->enrollUser($userId, $programId, '2026-03-20');
```

#### Update Enrollment Progress
```php
$trainingService->updateEnrollmentProgress(
    enrollmentId: $enrollmentId,
    percentage: 75,
    status: 'in_progress',
    score: 4.5
);
```

#### Unenroll User
```php
$trainingService->unenrollUser($userId, $programId);
```

#### Get Program Details
```php
$details = $trainingService->getProgramDetails($programId);
// Returns: id, name, description, enrollment stats, completion rate
```

#### Get User's Programs
```php
$programs = $trainingService->getUserEnrollments($userId);
```

#### Search Programs
```php
$results = $trainingService->searchPrograms('PHP', 20);
```

#### Delete Program
```php
$trainingService->deleteProgram($programId);
```

### Career Development

#### Create Career Path
```php
$careerService = new CareerDevelopmentService();
$pathId = $careerService->createCareerPath([
    'name' => 'Senior Developer',
    'description' => 'Path to technical leadership',
    'target_position' => 'Tech Lead',
    'duration_months' => 24,
    'skills_required' => ['PHP', 'Leadership', 'Mentoring']
], $userId);
```

#### Create Individual Development Plan
```php
$idpId = $careerService->createIDP($employeeId, $createdBy, [
    'career_path_id' => $pathId,
    'start_date' => '2026-03-01',
    'end_date' => '2028-03-01',
    'objectives' => 'Develop as a technical leader',
    'milestones' => [
        ['title' => 'Complete training', 'dueDate' => '2026-09-01'],
        ['title' => 'Lead project', 'dueDate' => '2027-03-01']
    ]
]);
```

#### Update IDP
```php
$careerService->updateIDP($idpId, [
    'objectives' => 'Updated objectives',
    'status' => 'active'
]);
```

#### Complete IDP
```php
$careerService->completeIDP($idpId);
```

#### Get User's Current Plan
```php
$currentPlan = $careerService->getUserCurrentPlan($userId);
```

#### Get Available Career Paths
```php
$paths = $careerService->getAvailableCareerPaths(20);
```

#### Search Paths
```php
$results = $careerService->searchCareerPaths('Developer', 20);
```

### Performance Management

#### Create Review
```php
$performanceService = new PerformanceService();
$reviewId = $performanceService->createReview([
    'employee_id' => 5,
    'review_period_start' => '2026-01-01',
    'review_period_end' => '2026-12-31',
    'rating' => 4.5,
    'comments' => 'Great performance',
    'status' => 'draft'
], $reviewerId);
```

#### Submit Review
```php
$performanceService->submitReview($reviewId, [
    'rating' => 4.5,
    'comments' => 'Excellent work this period'
]);
```

#### Complete Review
```php
$performanceService->completeReview($reviewId);
```

#### Get Review Details
```php
$details = $performanceService->getReviewDetails($reviewId);
```

#### Get Employee Reviews
```php
$reviews = $performanceService->getEmployeeReviews($employeeId);
```

#### Get Pending Reviews
```php
$pending = $performanceService->getPendingReviews($reviewerId);
```

#### Get Performance Stats
```php
$stats = $performanceService->getEmployeePerformanceStats($employeeId);
// Returns: total_reviews, completed_reviews, pending_reviews, average_rating
```

### Leadership Programs

#### Create Program
```php
$leadershipService = new LeadershipService();
$programId = $leadershipService->createProgram([
    'name' => 'Middle Management Development',
    'level' => 'Middle',
    'focus_area' => 'Team Leadership',
    'duration_weeks' => 12,
    'target_audience' => 'Team Leads'
], $userId);
```

#### Enroll User
```php
$enrollmentId = $leadershipService->enrollUser($userId, $programId, '2026-03-20');
```

#### Complete Enrollment
```php
$leadershipService->completeEnrollment($enrollmentId, 'Great progress!');
```

#### Get User's Enrolled Programs
```php
$programs = $leadershipService->getUserEnrollments($userId);
```

#### Get Active Programs
```php
$programs = $leadershipService->getActivePrograms(20);
```

## Authentication

### Check Login Status
```php
if (AuthManager::isLoggedIn()) {
    echo "User is logged in";
}
```

### Get Current User
```php
$userId = AuthManager::getCurrentUserId();
$role = AuthManager::getCurrentRole();
```

### Check Permissions
```php
if (AuthManager::isAdmin()) { }
if (AuthManager::isLearningAdmin()) { }
if (AuthManager::hasRole('manager')) { }
if (AuthManager::hasAnyRole(['admin', 'manager', 'learning'])) { }
```

### Login/Logout
```php
AuthManager::login($userId, $userData);
AuthManager::logout();
```

## Validation

### Validate Registration Data
```php
$validator = new Validator();
if ($validator->validateRegistration($data)) {
    // All good
} else {
    $errors = $validator->getErrors();
}
```

### Validate Training Program
```php
if (!$validator->validateTrainingProgram($data)) {
    $errors = $validator->getErrors();
}
```

### Validate Performance Review
```php
if (!$validator->validatePerformanceReview($data)) {
    $errors = $validator->getErrors();
}
```

### Static Validation Methods
```php
Validator::isValidEmail($email);
Validator::isValidPassword($password);
Validator::isValidUsername($username);
Validator::isValidDate($date);
Validator::isValidInteger($value);
Validator::isValidFloat($value);
Validator::isValidUrl($url);
```

## API Responses

### Success Response
```php
ResponseHandler::success($data, 'Success message')->send();
```

### Created Response
```php
ResponseHandler::created(['id' => 1], 'Resource created')->send();
```

### Error Response
```php
ResponseHandler::error('Error message', 400)->send();
```

### Validation Error
```php
ResponseHandler::validationError([
    'field1' => 'Error message',
    'field2' => 'Error message'
])->send();
```

### Not Found
```php
ResponseHandler::notFound('Resource not found')->send();
```

### Unauthorized
```php
ResponseHandler::unauthorized('Login required')->send();
```

### Forbidden
```php
ResponseHandler::forbidden('Permission denied')->send();
```

## Helper Functions

### String Manipulation
```php
Helper::sanitize($input);
Helper::sanitizeArray($array);
Helper::truncate('Long text...', 100);
Helper::toSlug('My Text Here'); // my-text-here
```

### Date/Time
```php
Helper::formatDate('2026-03-20');
Helper::formatDateTime('2026-03-20 14:30:00');
Helper::timeAgo('2026-03-15 10:00:00'); // "5 days ago"
```

### Utilities
```php
Helper::randomString(32);
Helper::isEmpty($value);
Helper::coalesce($val1, $val2, $val3);
Helper::average([1, 2, 3, 4, 5]);
Helper::groupBy($array, 'category');
```

## Error Handling

### Try-Catch Pattern
```php
try {
    $result = $service->doSomething();
} catch (Exception $e) {
    ResponseHandler::error($e->getMessage())->send();
    // or: error_log($e->getMessage());
}
```

### Validation Then Create
```php
$validator = new Validator();
if (!$validator->validateTrainingProgram($data)) {
    ResponseHandler::validationError($validator->getErrors())->send();
}
try {
    $id = $service->createProgram($data, $userId);
    ResponseHandler::created(['id' => $id])->send();
} catch (Exception $e) {
    ResponseHandler::error($e->getMessage())->send();
}
```

## Database Directly

### When you need raw queries (not recommended, use services instead)
```php
use HRManagement\Database\Database;

$db = Database::getInstance();

// Fetch one
$result = $db->fetchOne('SELECT * FROM users WHERE id = ?', [$id]);

// Fetch all
$results = $db->fetchAll('SELECT * FROM users WHERE role = ?', ['manager']);

// Execute (for INSERT, UPDATE, DELETE)
$db->execute('UPDATE users SET status = ? WHERE id = ?', ['active', $id]);

// Transactions
$db->beginTransaction();
try {
    // Do operations
    $db->commit();
} catch (Exception $e) {
    $db->rollback();
}
```

## Best Practices Checklist

- ✅ Always use services instead of direct DB access
- ✅ Always validate input before creating/updating
- ✅ Always check authentication before sensitive operations
- ✅ Always use appropriate ResponseHandler calls
- ✅ Always catch exceptions
- ✅ Always sanitize user input
- ✅ Never expose passwords in responses
- ✅ Always use prepared statements
- ✅ Test validation before implementation
- ✅ Keep business logic in services, not in modules
