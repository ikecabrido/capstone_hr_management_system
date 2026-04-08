# HR Management System - OOP Architecture

## Overview

This is a complete Object-Oriented refactor of the HR Management & Learning Development System. The architecture follows SOLID principles and is organized into clear layers for better maintainability, testability, and scalability.

## Architecture Layers

### 1. Database Layer (`src/Database/`)
- **Database.php** - Singleton PDO connection manager
  - Handles database connections
  - Provides helper methods for executing queries
  - Supports transactions

### 2. Model Layer (`src/Models/`)
Entity models representing database tables:
- **BaseModel.php** - Abstract base for all models with common methods
- **User.php** - User entity
- **TrainingProgram.php** - Training program entity
- **TrainingEnrollment.php** - Training enrollment entity
- **CareerPath.php** - Career path entity
- **IndividualDevelopmentPlan.php** - IDP entity
- **PerformanceReview.php** - Performance review entity
- **LeadershipProgram.php** - Leadership program entity

**Features:**
- Properties with getter/setter methods
- Type safety
- Conversion to/from arrays
- Fluent interface for chaining

### 3. Repository Layer (`src/Repositories/`)
Data Access Objects for CRUD operations:
- **BaseRepository.php** - Base CRUD operations for all repositories
- **UserRepository.php** - User data access
- **TrainingProgramRepository.php** - Training program data access
- **TrainingEnrollmentRepository.php** - Enrollment data access
- **CareerPathRepository.php** - Career path data access
- **IndividualDevelopmentPlanRepository.php** - IDP data access
- **PerformanceReviewRepository.php** - Performance review data access
- **LeadershipProgramRepository.php** - Leadership program data access

**Features:**
- Standard CRUD methods (create, read, update, delete)
- Custom finder methods
- Search functionality
- Statistical queries
- Query builders

### 4. Service Layer (`src/Services/`)
Business logic and operations:
- **UserService.php** - User management, authentication
- **TrainingService.php** - Training program management
- **CareerDevelopmentService.php** - Career path & IDP management
- **PerformanceService.php** - Performance reviews
- **LeadershipService.php** - Leadership program management

**Features:**
- Complex business operations
- Validation logic
- Data transformation
- Cross-entity operations
- Transaction handling

### 5. Utility Layer (`src/Utils/`)
Helper functions and utilities:
- **AuthManager.php** - Session and authentication management
- **Validator.php** - Data validation
- **ResponseHandler.php** - Standardized API responses
- **Helper.php** - Common utility functions

## Usage Examples

### Basic Setup

```php
<?php
require_once 'autoload.php';

use HRManagement\Database\Database;
use HRManagement\Services\TrainingService;
use HRManagement\Utils\AuthManager;

// Start session and authenticate
AuthManager::startSession();

// Get database instance
$db = Database::getInstance();

// Use services
$trainingService = new TrainingService();
```

### Creating a Training Program

```php
use HRManagement\Services\TrainingService;
use HRManagement\Utils\AuthManager;

$trainingService = new TrainingService();
$userId = AuthManager::getCurrentUserId();

$programData = [
    'name' => 'Advanced PHP Development',
    'description' => 'Master PHP 8 features',
    'category' => 'Technical',
    'type' => 'Online Course',
    'duration' => 40,
    'status' => 'active'
];

$programId = $trainingService->createProgram($programData, $userId);
echo "Program created with ID: $programId";
```

### Enrolling a User in Training

```php
$trainingService = new TrainingService();

try {
    $enrollmentId = $trainingService->enrollUser(
        userId: 5,
        programId: 1,
        startDate: '2026-03-20'
    );
    echo "Enrollment created: $enrollmentId";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Searching Programs

```php
$trainingService = new TrainingService();

$results = $trainingService->searchPrograms('PHP', limit: 20);

foreach ($results as $program) {
    echo $program->getName() . "\n";
}
```

### Creating a Performance Review

```php
use HRManagement\Services\PerformanceService;

$performanceService = new PerformanceService();
$userId = AuthManager::getCurrentUserId();

$reviewData = [
    'employee_id' => 5,
    'review_period_start' => '2026-01-01',
    'review_period_end' => '2026-12-31',
    'rating' => 4.5,
    'comments' => 'Great performance this year',
    'status' => 'draft'
];

$reviewId = $performanceService->createReview($reviewData, $userId);
```

### Career Development Plans

```php
use HRManagement\Services\CareerDevelopmentService;

$careerService = new CareerDevelopmentService();

// Create career path
$pathData = [
    'name' => 'Senior Developer',
    'target_position' => 'Technical Lead',
    'duration_months' => 24,
    'skills_required' => ['PHP', 'MySQL', 'Leadership']
];

$pathId = $careerService->createCareerPath($pathData, $userId);

// Create IDP for user
$idpData = [
    'career_path_id' => $pathId,
    'start_date' => '2026-03-01',
    'end_date' => '2028-03-01',
    'objectives' => 'Develop leadership skills',
    'milestones' => [
        ['title' => 'Complete leadership training', 'dueDate' => '2026-09-01'],
        ['title' => 'Lead a project', 'dueDate' => '2027-03-01']
    ]
];

$idpId = $careerService->createIDP($userId, $userId, $idpData);
```

### User Authentication

```php
use HRManagement\Services\UserService;
use HRManagement\Utils\AuthManager;

$userService = new UserService();

// Authenticate
$user = $userService->authenticate('username', 'password');

if ($user) {
    AuthManager::login($user->getId(), $user->toArray());
    echo "Login successful";
} else {
    echo "Invalid credentials";
}
```

### Validation

```php
use HRManagement\Utils\Validator;

$validator = new Validator();

$data = [
    'username' => 'john_doe',
    'email' => 'john@example.com',
    'password' => 'SecurePass123',
    'password_confirm' => 'SecurePass123',
    'full_name' => 'John Doe'
];

if ($validator->validateRegistration($data)) {
    echo "Validation passed";
} else {
    foreach ($validator->getErrors() as $field => $error) {
        echo "$field: $error\n";
    }
}
```

### API Response Handling

```php
use HRManagement\Utils\ResponseHandler;

// Success response
ResponseHandler::success($program->toArray(), 'Program created successfully')->send();

// Error response
ResponseHandler::error('Program not found', 404)->send();

// Validation error
ResponseHandler::validationError([
    'name' => 'Program name is required',
    'category' => 'Category is required'
])->send();

// Unauthorized
ResponseHandler::unauthorized('You must be logged in')->send();
```

## Directory Structure

```
src/
├── Database/
│   └── Database.php
├── Models/
│   ├── BaseModel.php
│   ├── User.php
│   ├── TrainingProgram.php
│   ├── TrainingEnrollment.php
│   ├── CareerPath.php
│   ├── IndividualDevelopmentPlan.php
│   ├── PerformanceReview.php
│   └── LeadershipProgram.php
├── Repositories/
│   ├── BaseRepository.php
│   ├── UserRepository.php
│   ├── TrainingProgramRepository.php
│   ├── TrainingEnrollmentRepository.php
│   ├── CareerPathRepository.php
│   ├── IndividualDevelopmentPlanRepository.php
│   ├── PerformanceReviewRepository.php
│   └── LeadershipProgramRepository.php
├── Services/
│   ├── UserService.php
│   ├── TrainingService.php
│   ├── CareerDevelopmentService.php
│   ├── PerformanceService.php
│   └── LeadershipService.php
└── Utils/
    ├── AuthManager.php
    ├── Validator.php
    ├── ResponseHandler.php
    └── Helper.php

autoload.php  - PSR-4 Autoloader
```

## SOLID Principles Applied

### Single Responsibility
- Each class has one reason to change
- Services handle business logic
- Repositories handle data access
- Models represent data structures

### Open/Closed
- BaseModel and BaseRepository provide extensibility without modification
- Easy to add new entities and features

### Liskov Substitution
- All repositories extend BaseRepository
- All models extend BaseModel
- Consistent interface across implementations

### Interface Segregation
- Services expose only necessary methods
- Repositories provide focused data access

### Dependency Inversion
- Services depend on repositories (abstractions)
- Loose coupling between layers

## Migration from Old Code

To migrate existing procedural code to this OOP structure:

1. **Update database connection:**
   ```php
   // Old
   $pdo = new PDO(...);
   
   // New
   use HRManagement\Database\Database;
   $db = Database::getInstance();
   ```

2. **Replace direct queries with repositories:**
   ```php
   // Old
   $stmt = $pdo->prepare('SELECT * FROM training_programs WHERE id = ?');
   $stmt->execute([$id]);
   $program = $stmt->fetch(PDO::FETCH_ASSOC);
   
   // New
   use HRManagement\Repositories\TrainingProgramRepository;
   $repo = new TrainingProgramRepository();
   $program = $repo->findById($id);
   ```

3. **Use services for business logic:**
   ```php
   // Old
   $stmt = $pdo->prepare('INSERT INTO training_enrollments...');
   
   // New
   use HRManagement\Services\TrainingService;
   $service = new TrainingService();
   $enrollmentId = $service->enrollUser($userId, $programId);
   ```

4. **Use AuthManager for session handling:**
   ```php
   // Old
   $_SESSION['user_id'] = $userId;
   
   // New
   use HRManagement\Utils\AuthManager;
   AuthManager::login($userId, $userData);
   ```

## Best Practices

1. **Always use type hints** in your code
2. **Use fluent interfaces** for method chaining
3. **Validate data** before passing to services
4. **Handle exceptions** appropriately
5. **Use repositories** instead of direct DB access
6. **Keep services focused** on business logic
7. **Use the helper functions** for common tasks

## Future Enhancements

- Add caching layer (Redis/Memcached)
- Implement logging
- Add event system
- Create API endpoints
- Add unit tests
- Implement dependency injection container
- Add query builder for complex queries
- Create migration system
