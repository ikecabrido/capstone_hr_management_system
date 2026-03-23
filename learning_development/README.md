# Learning & Development Module Database Schema

This directory contains the complete database schema for the Learning & Development (LD) module of the HR Management System.

## Files

- **`hr_management.sql`** - Original schema dump with sample data (new ld_ prefixed format)
- **`old_hr_management.sql`** - Legacy schema dump with sample data (old non-prefixed format)
- **`setup_ld_schema.sql`** - Clean setup script for creating tables (IF NOT EXISTS)
- **`migrate_old_data.sql`** - Migration script to transfer data from old to new schema
- **`extract_missing_data.sql`** - Script to identify missing data between schemas
- **`migrations/rename_ld_tables.sql`** - Legacy migration script for table renaming

## Database Tables

### Core Tables

| Table | Purpose | Key Fields |
|-------|---------|------------|
| `ld_training_programs` | Training program definitions | title, trainer, start_date, end_date, max_participants |
| `ld_courses` | Individual courses within programs | title, instructor, duration, content_type, training_program_id |
| `ld_enrollments` | Employee course enrollments | employee_id, course_id, status, progress_percentage |
| `ld_certification` | Employee certifications | employee_id, course_id, certification_name, issued_date, expiry_date |

### Supporting Tables

| Table | Purpose | Key Fields |
|-------|---------|------------|
| `ld_elearning_modules` | Online learning content | title, content_url, course_id, module_order, duration_minutes |
| `ld_virtual_sessions` | Virtual training sessions | course_id, session_url, scheduled_at, duration_minutes, status |
| `ld_archive` | Archived records | archive_type, original_id, title, archived_by, archive_data |
| `skill_gap_analyses` | Employee skill assessments | employee_id, required_skill, current_level, required_level, recommendations |

## Setup Instructions

### Option 1: Clean Setup (Recommended for new installations)
```bash
mysql -h localhost -u root hr_management < setup_ld_schema.sql
```

### Option 2: Full Import (with sample data)
```bash
mysql -h localhost -u root hr_management < hr_management.sql
```

### Option 3: Manual Migration (if tables exist without ld_ prefix)
```bash
mysql -h localhost -u root hr_management < migrations/rename_ld_tables.sql
```
## Migration from Old to New Schema

If you have data in the old format (non-prefixed tables), use these steps:

### Step 1: Extract Missing Data
```bash
mysql -u root hr_management < extract_missing_data.sql
```
This will show you exactly what data is missing from the new schema.

### Step 2: Migrate Data
```bash
mysql -u root hr_management < migrate_old_data.sql
```
This safely migrates all missing data from old tables to new ld_ prefixed tables.

### Step 3: Verify Migration
```bash
mysql -u root hr_management < extract_missing_data.sql
```
Run again to confirm no data is missing.
## Table Relationships

```
users (id)
├── ld_training_programs (created_by_user_id)
├── ld_courses (created_by_user_id)
├── ld_certification (employee_id, issued_by_user_id)
├── ld_enrollments (employee_user_id)
├── ld_elearning_modules (created_by_user_id)
├── ld_virtual_sessions (created_by_user_id)
├── ld_archive (archived_by, restored_by, original_created_by)
└── skill_gap_analyses (employee_id, created_by)

ld_training_programs (ld_training_programs_id)
└── ld_courses (ld_training_programs_id)

ld_courses (ld_courses_id)
├── ld_enrollments (ld_courses_id)
├── ld_certification (ld_courses_id)
├── ld_elearning_modules (ld_courses_id)
└── ld_virtual_sessions (ld_courses_id)
```

## Key Features

- **Archive System**: Complete audit trail with JSON data storage
- **Flexible Content Types**: Support for in-person, online, and hybrid courses
- **Progress Tracking**: Enrollment status and completion tracking
- **Certification Management**: Expiry dates and status tracking
- **Skill Gap Analysis**: Employee development planning
- **Virtual Sessions**: Online training session management

## Sample Data Included

The `hr_management.sql` file includes sample data with:
- 11 users (including learning role user)
- 2 training programs
- 4 courses (including 1 additional course not in old schema)
- 2 enrollments
- 4 certifications
- 3 archived records

## Key Differences: Old vs New Schema

### Table Name Changes
- `certifications` → `ld_certification`
- `courses` → `ld_courses`
- `enrollments` → `ld_enrollments`
- `training_programs` → `ld_training_programs`
- `elearning_modules` → `ld_elearning_modules`
- `virtual_sessions` → `ld_virtual_sessions`

### Column Name Changes
- `issued_by` → `issued_by_user_id`
- `course_id` → `ld_courses_id`
- `training_program_id` → `ld_training_programs_id`
- `employee_id` → `employee_user_id` (in enrollments)
- `created_by` → `created_by_user_id`

### Additional Data in New Schema
- Course ID 4: "G" course with online content type
- Updated archive records with proper JSON formatting

## Usage in PHP Models

All tables are designed to work with the existing PHP model classes in the `models/` directory:

- `TrainingProgram.php` → `ld_training_programs`
- `Course.php` → `ld_courses`
- `Enrollment.php` → `ld_enrollments`
- `Certification.php` → `ld_certification`
- `Archive.php` → `ld_archive`
- `ELearning.php` → `ld_elearning_modules`
- `SkillGapAnalysis.php` → `skill_gap_analyses`

## Notes

- All foreign keys reference the `users` table for user relationships
- Archive table uses JSON for flexible data storage
- Tables use `ld_` prefix for namespace isolation
- Auto-increment IDs use descriptive names (e.g., `ld_courses_id`)
- Timestamps include both created_at and updated_at where appropriate