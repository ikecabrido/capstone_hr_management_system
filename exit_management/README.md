# Exit Management System

A comprehensive OOP PHP-based system for managing employee exit processes in an HR management system.

## Features

### 1. Resignation/Termination Management
- Submit resignation requests (voluntary/involuntary)
- Track notice periods and last working dates
- Approve/reject resignations
- Maintain resignation history

### 2. Exit Interview Management
- Schedule exit interviews
- Collect structured feedback
- Rate various aspects (work environment, management, compensation, etc.)
- Generate insights from feedback

### 3. Knowledge Transfer Process
- Create knowledge transfer plans
- Assign successors for departing employees
- Track transfer items and progress
- Ensure smooth handover of responsibilities

### 4. Final Settlement & Dues Calculation
- Calculate gratuity, provident fund, notice pay
- Handle outstanding loans and deductions
- Generate final settlement letters
- Track payment status

### 5. Exit Documentation Management
- Upload and manage required documents
- Clearance checklist for different departments
- Document verification workflow
- Secure document storage

### 6. Post-Exit Surveys
- Create customizable surveys
- Collect feedback from ex-employees
- Generate reports and analytics
- Improve retention strategies

## Architecture

### OOP Structure
- **Models**: Handle database operations and business logic
- **Controllers**: Manage HTTP requests and responses
- **Views**: Present data to users (integrated with AdminLTE)

### Key Classes

#### Models
- `ExitManagementModel` - Base model with common functionality
- `ResignationModel` - Handles resignation processes
- `ExitInterviewModel` - Manages exit interviews and feedback
- `KnowledgeTransferModel` - Handles knowledge transfer plans
- `SettlementModel` - Calculates and manages settlements
- `DocumentationModel` - Manages documents and clearance
- `SurveyModel` - Handles post-exit surveys

#### Controllers
- `ExitManagementController` - Base controller
- `ResignationController` - Resignation management
- `ExitInterviewController` - Interview scheduling and feedback
- `KnowledgeTransferController` - Transfer plan management
- `SettlementController` - Settlement calculations
- `DocumentationController` - Document management
- `SurveyController` - Survey management

## Database Schema

The system requires the following tables:
- `resignations`
- `exit_interviews`
- `exit_interview_feedback`
- `knowledge_transfer_plans`
- `knowledge_transfer_items`
- `employee_settlements`
- `exit_documents`
- `clearance_checklist`
- `exit_surveys`
- `survey_questions`
- `survey_responses`
- `survey_answers`

Run `exit_management_schema.sql` to create all required tables.

## Installation

1. Ensure you have the base HR management system set up
2. Run the SQL schema file to create required tables
3. The system will automatically integrate with existing employee data
4. Access via `exit_management.php`

## Usage

### For HR Administrators
1. **Dashboard**: View pending tasks and statistics
2. **Resignations**: Review and process resignation requests
3. **Interviews**: Schedule and manage exit interviews
4. **Transfers**: Create and monitor knowledge transfer plans
5. **Settlements**: Calculate and approve final settlements
6. **Documents**: Manage exit documentation and clearance
7. **Surveys**: Create and analyze post-exit surveys

### For Employees
- Submit resignation requests
- Participate in exit interviews
- Complete knowledge transfer tasks
- Access settlement information
- Respond to post-exit surveys

## Security Features

- Session-based authentication
- Role-based access control
- Input validation and sanitization
- SQL injection prevention via PDO
- File upload security for documents

## Dependencies

- PHP 7.4+
- MySQL/MariaDB
- AdminLTE 3.x
- jQuery
- Bootstrap 4

## Future Enhancements

- Email notifications for pending tasks
- Automated reminder systems
- Advanced reporting and analytics
- Integration with payroll systems
- Mobile-responsive interface improvements
- API endpoints for third-party integrations