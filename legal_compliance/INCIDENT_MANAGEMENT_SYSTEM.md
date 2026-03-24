# Incident Management System with Disciplinary Action Workflow
## Bestlink College of the Philippines - HR Legal and Compliance Module

---

## 1. SYSTEM OVERVIEW

This is a comprehensive Incident Management System with step-based disciplinary workflow and progressive discipline logic. The system is designed for real HR processes and includes:

- Incident Submission and Tracking
- HR Review Workflow with Approve/Reject
- Step-by-step Disciplinary Process
- Progressive Discipline Logic (automatic action suggestions based on offense count)
- Email Notifications
- Activity Logging and Audit Trail

---

## 2. WORKFLOW DIAGRAM

```
                    ┌─────────────┐
                    │  SUBMITTED  │
                    └──────┬──────┘
                           │
                           ▼
                ┌─────────────────────┐
                │   UNDER REVIEW     │
                │  (HR Validation)   │
                └──────┬──────────────┘
                       │
           ┌───────────┴───────────┐
           ▼                       ▼
    ┌─────────────┐        ┌─────────────┐
    │  VALIDATED  │        │  REJECTED   │
    └──────┬──────┘        └─────────────┘
           │
           ▼
    ┌─────────────────┐
    │  NTE ISSUED    │
    │ (Notice to     │
    │  Explain)      │
    └──────┬──────────┘
           │
           ▼
    ┌─────────────────────┐
    │ EXPLANATION RECEIVED│
    └──────┬──────────────┘
           │
           ▼
    ┌─────────────────┐
    │  HR EVALUATION │
    └──────┬──────────┘
           │
           ▼
    ┌───────────────┐
    │DECISION MADE  │
    └──────┬────────┘
           │
           ▼
    ┌─────────────────┐
    │  FINAL ACTION  │
    │ (Disciplinary  │
    │    Action)     │
    └──────┬──────────┘
           │
           ▼
    ┌─────────────┐
    │    CLOSED   │
    └─────────────┘
```

---

## 3. DATABASE DESIGN

### Main Tables

#### 3.1 Incidents Table (Enhanced)
Primary table storing all incident data with workflow and progressive discipline columns.

```sql
-- Key columns (additional to existing):
- respondent_id          INT(11)       -- Employee involved
- reporter_name         VARCHAR(100)  -- Person who reported
- current_workflow_step ENUM          -- Current step in workflow
- nte_deadline          DATE          -- NTE response deadline
- explanation_deadline   DATE          -- Employee explanation deadline
- final_decision        VARCHAR(100)  -- Chosen disciplinary action
- final_decision_date   DATE          -- When decision was made
- closed_at             TIMESTAMP     -- When case was closed
- closure_reason        TEXT          -- Reason for closure
- suggested_action      VARCHAR(50)   -- System suggested action
- offense_count         INT(11)       -- Number of previous offenses
- offense_period_months INT(11)        -- Period to track (default 12)
- is_override           TINYINT(1)    -- Did HR override suggestion?
- override_reason       TEXT          -- Reason for override
- created_by            INT(11)       -- Who created the incident
```

#### 3.2 Incident Workflow Table
Tracks step-by-step progression through the disciplinary process.

```sql
CREATE TABLE incident_workflow (
  id              INT(11) AUTO_INCREMENT PRIMARY KEY,
  incident_id     INT(11) NOT NULL,
  step            ENUM('submitted','under_review','validated','rejected',
                       'nte_issued','explanation_received','hr_evaluation',
                       'decision_made','final_action','closed'),
  step_status     ENUM('pending','in_progress','completed','skipped','rejected'),
  started_at      TIMESTAMP NULL,
  completed_at    TIMESTAMP NULL,
  deadline        TIMESTAMP NULL,
  notes           TEXT,
  performed_by    INT(11),
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 3.3 Disciplinary Actions Table
Stores final disciplinary actions taken against employees.

```sql
CREATE TABLE disciplinary_actions (
  id              INT(11) AUTO_INCREMENT PRIMARY KEY,
  incident_id     INT(11) NOT NULL,
  action_type     ENUM('verbal_warning','written_warning','final_written_warning',
                       'suspension','termination','case_dismissed','counseling','probation'),
  action_details  TEXT,
  action_date     DATE NOT NULL,
  effective_date  DATE,
  duration_days   INT(11),        -- For suspension
  issued_by       INT(11) NOT NULL,
  approved_by    INT(11),
  approved_at    TIMESTAMP,
  is_final        TINYINT(1) DEFAULT 0,
  document_path  VARCHAR(255)
);
```

#### 3.4 Explanations Table
Stores employee responses to incidents/NTE.

```sql
CREATE TABLE explanations (
  id              INT(11) AUTO_INCREMENT PRIMARY KEY,
  incident_id     INT(11) NOT NULL,
  employee_id     INT(11) NOT NULL,
  explanation_text TEXT NOT NULL,
  submission_method ENUM('online','written','verbal_recorded'),
  submitted_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  is_late         TINYINT(1) DEFAULT 0,
  late_reason     TEXT,
  attachments     VARCHAR(255),
  reviewed_by     INT(11),
  reviewed_at     TIMESTAMP,
  review_notes    TEXT
);
```

#### 3.5 Notice to Explain (NTE) Table
Stores NTE details issued to employees.

```sql
CREATE TABLE notice_to_explain (
  id              INT(11) AUTO_INCREMENT PRIMARY KEY,
  incident_id     INT(11) NOT NULL,
  nte_number      VARCHAR(50) UNIQUE NOT NULL,
  issued_to        INT(11) NOT NULL,
  issued_by       INT(11) NOT NULL,
  issue_date      DATE NOT NULL,
  deadline_date   DATE NOT NULL,
  nte_content     TEXT NOT NULL,
  delivery_method ENUM('email','physical','both'),
  delivered_at    TIMESTAMP,
  is_received     TINYINT(1) DEFAULT 0,
  reminder_sent   TINYINT(1) DEFAULT 0,
  reminder_sent_at TIMESTAMP
);
```

#### 3.6 Progressive Discipline Rules Table
Defines automatic action suggestions based on offense count.

```sql
CREATE TABLE progressive_discipline_rules (
  id              INT(11) AUTO_INCREMENT PRIMARY KEY,
  offense_count   INT(11) UNIQUE NOT NULL,
  action_type     ENUM('verbal_warning','written_warning','final_written_warning','suspension','termination'),
  action_label    VARCHAR(100) NOT NULL,
  description     TEXT,
  is_active       TINYINT(1) DEFAULT 1
);

-- Default Rules:
-- 1st offense  → Verbal Warning
-- 2nd offense  → Written Warning
-- 3rd offense  → Suspension
-- 4th+ offense → Termination
```

---

## 4. PROGRESSIVE DISCIPLINE LOGIC

### How It Works

1. **Employee Selection**: When HR selects an employee for an incident, the system automatically:
   - Counts previous offenses within the tracking period (default 12 months)
   - Can filter by same incident type or all incident types

2. **Action Suggestion**:
   ```
   Offense Count: 1  → Verbal Warning
   Offense Count: 2  → Written Warning
   Offense Count: 3  → Suspension
   Offense Count: 4+ → Termination
   ```

3. **HR Override**: HR can override the suggested action:
   - Must provide a reason for override
   - Override is logged in the system

4. **Backend Implementation** (`models/IncidentModel.php`):
```php
// Count offenses
public function countOffenses($employeeId, $incidentType = null, $months = 12)

// Get suggested action based on offense count
public function getSuggestedAction($offenseCount)

// Get employee offense history
public function getOffenseHistory($employeeId, $months = 12)

// Apply disciplinary action with override logic
public function applyDisciplinaryAction($incidentId, $actionType, $actionDetails, 
                                        $userId, $isOverride = false, $overrideReason = null)
```

---

## 5. EMAIL NOTIFICATION SYSTEM

### Trigger Points

1. **Incident Submitted** → Notify HR
2. **NTE Issued** → Notify Employee
3. **NTE Reminder** → Remind Employee (if no explanation received)
4. **Explanation Received** → Notify HR
5. **Decision Made** → Notify Employee
6. **Case Closed** → Notify all parties

### Implementation (`controllers/IncidentController.php`)

```php
private function sendEmailNotification($incidentId, $recipientId, $emailType, 
                                        $subject, $body)
{
    // Logs email to incident_email_log table
    // Placeholder for SMTP integration with PHPMailer
}
```

### Email Log Table

```sql
CREATE TABLE incident_email_log (
  id              INT(11) AUTO_INCREMENT PRIMARY KEY,
  incident_id     INT(11),
  recipient_id    INT(11) NOT NULL,
  recipient_email VARCHAR(255) NOT NULL,
  email_type      ENUM('incident_submitted','nte_issued','nte_reminder',
                       'explanation_received','decision_notice','case_closed','hr_review'),
  subject         VARCHAR(255) NOT NULL,
  body            TEXT NOT NULL,
  sent_by         INT(11) NOT NULL,
  sent_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status          ENUM('pending','sent','failed','bounced'),
  error_message   TEXT
);
```

---

## 6. FRONTEND UI LAYOUT

### 6.1 Incident Submission Form
- Employee Involved (dropdown from employees table)
- Incident Type (dropdown)
- Severity (dropdown)
- Incident Date
- Reporter Name
- Location
- Description
- Witnesses

### 6.2 HR Dashboard (Incident List)
Tabs:
- All Incidents
- Pending (Submitted)
- Under Review
- NTE Issued
- Decisions Made
- Closed

Table Columns:
- ID, Date, Employee, Type, Severity, Status, Current Step, Actions

### 6.3 Incident Details Page
- Incident Information Card
- Workflow Timeline
- Current Status with deadlines

### 6.4 Disciplinary Action Form (HR Evaluation Step)
```
┌─────────────────────────────────────────────────────┐
│  Progressive Discipline Information                 │
├─────────────────────────────────────────────────────┤
│  Employee: [Name]                                   │
│  Previous Offenses: [X] in last [Y] months         │
│  ─────────────────────────────────────────────────  │
│  Suggested Action: [Action Type]                    │
│  ┌────────────────────────────────────────────────┐ │
│  │ ⚠️ This is the recommended action based on   │ │
│  │    progressive discipline policy              │ │
│  └────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│  Final Action Selection                            │
├─────────────────────────────────────────────────────┤
│  Action Type: [Dropdown]                            │
│    - Verbal Warning                                 │
│    - Written Warning                                │
│    - Final Written Warning                          │
│    - Suspension (with days input)                   │
│    - Termination                                    │
│    - Case Dismissed                                 │
├─────────────────────────────────────────────────────┤
│  Duration (for suspension): [Input] days            │
│  Remarks: [Textarea]                               │
│  ─────────────────────────────────────────────────  │
│  ☐ Override suggested action (provide reason below)│
│  Override Reason: [Textarea]                       │
└─────────────────────────────────────────────────────┘

[Confirm Action]
```

---

## 7. KEY BACKEND FUNCTIONS

### Model Functions (`models/IncidentModel.php`)

1. **CRUD Operations**
   - `createIncident($data)` - Create new incident
   - `getAllIncidents()` - Get all incidents
   - `getIncidentById($id)` - Get single incident
   - `updateIncident($id, $data)` - Update incident
   - `deleteIncident($id)` - Delete incident

2. **Workflow Operations**
   - `updateWorkflowStep($incidentId, $newStep, $userId)`
   - `getWorkflowHistory($incidentId)`
   - `createWorkflowStep($incidentId, $step, $status, $userId)`

3. **NTE Operations**
   - `createNTE($data)` - Issue NTE
   - `getNTEByIncident($incidentId)` - Get NTE for incident

4. **Progressive Discipline**
   - `countOffenses($employeeId, $incidentType, $months)`
   - `getSuggestedAction($offenseCount)`
   - `getOffenseHistory($employeeId, $months)`
   - `applyDisciplinaryAction($incidentId, $actionType, $details, $userId, $isOverride, $reason)`

### Controller Functions (`controllers/IncidentController.php`)

1. `createIncident($data, $userId)` - Handle incident creation with auto-generated ID
2. `updateIncidentStatus($incidentId, $newStatus, $userId)` - Update workflow step
3. `issueNTE($data, $userId)` - Issue Notice to Explain
4. `receiveExplanation($data, $userId)` - Store employee explanation
5. `applyAction($data, $userId)` - Apply final disciplinary action
6. `validateIncident($incidentId, $userId)` - HR approval step
7. `rejectIncident($incidentId, $reason, $userId)` - HR rejection step

---

## 8. SETUP INSTRUCTIONS

### Step 1: Run Migration
Execute the enhanced migration file:

```sql
-- File: sql/incidents_enhanced_migration.sql
-- This creates all required tables and columns
```

### Step 2: Configure
- Update SMTP settings in `incident_config` table
- Set progressive discipline rules if needed
- Adjust tracking period (default 12 months)

### Step 3: Test
- Create test incidents
- Verify workflow progression
- Test progressive discipline logic with multiple incidents for same employee

---

## 9. FILES REFERENCE

| File | Purpose |
|------|---------|
| `incident.php` | Main UI - forms, dashboard, modals |
| `controllers/IncidentController.php` | Business logic |
| `models/IncidentModel.php` | Database operations |
| `sql/incidents_enhanced_migration.sql` | Database schema |
| `sql/incidents_disciplinary_sample_data.sql` | Sample data |

---

## 10. PROGRESSIVE DISCIPLINE FLOW EXAMPLE

```
Employee: John Doe
Incident Type: Tardiness

1st Incident (Jan 2026):
   - System counts: 0 previous offenses
   - Suggested: Verbal Warning
   - HR applies: Verbal Warning ✓
   - Final status: Closed

2nd Incident (Mar 2026):
   - System counts: 1 previous offense (within 12 months)
   - Suggested: Written Warning
   - HR applies: Written Warning ✓
   - Final status: Closed

3rd Incident (Jun 2026):
   - System counts: 2 previous offenses
   - Suggested: Suspension
   - HR applies: Suspension (3 days) ✓
   - Final status: Closed

4th Incident (Sep 2026):
   - System counts: 3 previous offenses
   - Suggested: Termination
   - HR can override with reason, or proceed with Termination
   - Final status: Closed
```

---

*System Implementation Complete - Bestlink College of the Philippines HR Module*