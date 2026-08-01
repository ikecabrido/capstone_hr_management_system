# Incident Reporting and Disciplinary Action Management Module

## Overview

This module provides a complete incident reporting and disciplinary action management system for the HR Legal & Compliance system. It follows a modular MVC-inspired OOP structure and integrates seamlessly with the existing system layout.

## Features

### Incident Reporting Module
- Create incident reports with detailed information
- View all incidents in a filterable table
- View single incident details via AJAX modal
- Update incident status through workflow
- Filter incidents by status, severity, type, date, and search
- Multiple file uploads for evidence
- Status history tracking
- Comments system

### Disciplinary Action Module
- Create disciplinary actions linked to incidents
- View all disciplinary actions per incident
- Update action status
- Approval workflow
- Implementation tracking
- Employee disciplinary history

## File Structure

```
legal_compliance/
├── models/
│   ├── Incident.php              # Incident model
│   ├── Employee.php              # Employee model
│   ├── DisciplinaryAction.php    # Disciplinary action model
│   └── FileUpload.php            # File upload handler model
├── controllers/
│   ├── IncidentController.php    # Incident controller
│   ├── DisciplinaryController.php # Disciplinary action controller
│   └── AjaxController.php        # AJAX request handler
├── includes/
│   ├── incident_modals.php       # Incident modals
│   ├── disciplinary_modals.php   # Disciplinary action modals
│   └── upload_handler.php        # File upload handler
├── js/
│   └── incident_reporting.js     # JavaScript for AJAX and UI
├── css/
│   └── incident_reporting.css    # Custom styles
├── uploads/
│   └── incident_evidence/        # Uploaded evidence files
├── incident_reporting.php        # Main page
└── README_INCIDENT_REPORTING.md  # This file
```

## Database Tables

The module uses the following database tables:

- `incidents` - Main incident records
- `incident_people_involved` - People involved in incidents
- `incident_evidence` - Evidence files attached to incidents
- `disciplinary_actions` - Disciplinary actions linked to incidents
- `incident_status_history` - Audit trail for status changes
- `incident_comments` - Comments on incidents
- `incident_types` - Lookup table for incident types

## Installation

1. Ensure the database tables are created using the SQL file:
   ```
   ../legal-and-compliance/sql/incident_reporting.sql
   ```

2. The module will automatically integrate with the existing system layout (header, sidebar, footer).

3. Access the module via the sidebar menu or navigate to:
   ```
   legal_compliance/incident_reporting.php
   ```

## Usage

### Reporting an Incident

1. Click "Report New Incident" button
2. Fill in the incident details:
   - Title and description
   - Incident type and category
   - Severity level
   - Date, time, and location
   - Respondent (person being reported)
   - Evidence files (optional)
3. Click "Submit Incident"

### Viewing Incidents

1. The main page displays all incidents in a table
2. Use filters to narrow down results:
   - Status
   - Severity
   - Incident type
   - Date range
   - Search text
3. Click the eye icon to view incident details

### Updating Incident Status

1. Open an incident detail modal
2. Click "Update Status"
3. Select the new status
4. Add notes (optional)
5. Click "Update Status"

### Creating Disciplinary Action

1. Open an incident detail modal
2. Click "Add Disciplinary Action"
3. Fill in the disciplinary action details:
   - Employee
   - Action type (verbal warning, written warning, suspension, termination)
   - Reason and violation description
   - Start date and end date (for suspension)
4. Click "Create Disciplinary Action"

### Managing Disciplinary Actions

1. Click "View Disciplinary Actions" button
2. View all disciplinary actions in a table
3. Click the eye icon to view details
4. Approve or mark as implemented

## Status Flow

### Incident Status Flow
```
submitted → under_review → investigation → escalated → resolved → closed
```

### Disciplinary Action Status Flow
```
pending → issued → appealed → upheld → dismissed
```

## Security

- All database queries use prepared statements
- Input validation on all forms
- File upload validation (type and size)
- Session-based authentication
- Role-based access control

## AJAX Endpoints

The module uses the following AJAX endpoints via `AjaxController.php`:

### Incident Actions
- `get_incident` - Get incident by ID
- `get_incidents` - Get all incidents with filters
- `create_incident` - Create new incident
- `update_incident` - Update incident
- `update_incident_status` - Update incident status
- `delete_incident` - Delete incident
- `get_incident_statistics` - Get incident statistics
- `get_incident_types` - Get incident types
- `add_incident_comment` - Add comment to incident

### Disciplinary Actions
- `get_disciplinary_action` - Get disciplinary action by ID
- `get_disciplinary_actions` - Get all disciplinary actions
- `get_disciplinary_by_incident` - Get disciplinary actions by incident
- `create_disciplinary` - Create disciplinary action
- `update_disciplinary` - Update disciplinary action
- `update_disciplinary_status` - Update disciplinary action status
- `delete_disciplinary` - Delete disciplinary action
- `approve_disciplinary` - Approve disciplinary action
- `mark_disciplinary_implemented` - Mark as implemented
- `get_disciplinary_statistics` - Get disciplinary statistics
- `get_action_types` - Get action types
- `get_action_statuses` - Get action statuses

### Employee Actions
- `search_employees` - Search employees
- `get_employee` - Get employee by ID
- `get_employees` - Get all employees
- `get_hr_employees` - Get HR employees
- `get_departments` - Get departments
- `get_employee_disciplinary_history` - Get employee disciplinary history

## File Upload

The module supports multiple file uploads for incident evidence:

- **Allowed types**: Images (JPEG, PNG, GIF, WebP), PDF, Word, Excel, Text, CSV, Video, Audio
- **Max file size**: 10MB per file
- **Storage**: `uploads/incident_evidence/`

## Customization

### Adding New Incident Types

1. Insert into `incident_types` table:
   ```sql
   INSERT INTO incident_types (type_name, category, severity_default, is_active)
   VALUES ('New Type', 'Category', 'medium', 1);
   ```

### Modifying Status Flow

1. Update the `status` ENUM in `incidents` table
2. Update the status options in `incident_modals.php`
3. Update the status badges in `incident_reporting.js`

### Adding New Disciplinary Action Types

1. Update the `action_type` ENUM in `disciplinary_actions` table
2. Update the action type options in `disciplinary_modals.php`
3. Update the action type mapping in `incident_reporting.js`

## Troubleshooting

### Files not uploading
- Check that `uploads/incident_evidence/` directory exists and is writable
- Verify PHP upload settings in `php.ini`:
  - `upload_max_filesize`
  - `post_max_size`
  - `max_execution_time`

### AJAX requests failing
- Check browser console for errors
- Verify database connection is working
- Check PHP error logs

### Modals not displaying
- Ensure all required CSS and JS files are loaded
- Check for JavaScript errors in console
- Verify jQuery and Bootstrap are loaded

## Support

For issues or questions, please contact the development team.

## License

This module is part of the HR Management System and is proprietary software.
