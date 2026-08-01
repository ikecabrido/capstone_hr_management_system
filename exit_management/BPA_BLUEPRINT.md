# Exit Management BPA Blueprint

## 1. Purpose
This BPA automates the end-to-end employee exit process so HR can move from resignation intake to final clearance in a controlled, auditable way.

## 2. Business Process Architecture

### 2.1 Process domains
The exit management architecture is organized as business domains, not just a linear flow.

- Resignation management
  - Intake resignation requests
  - Validate employee eligibility
  - Record resignation metadata
  - Route for approval
- Review and approval
  - HR / manager decisioning
  - Status update and audit trail
- Interview management
  - Schedule exit interviews
  - Record interview completion and feedback
- Knowledge transfer management
  - Create and assign knowledge transfer plans
  - Track deliverables and successor handover
- Settlement management
  - Calculate exit pay, deductions, gratuity, and PF
  - Generate settlement documents
- Payroll clearance and final approval
  - Submit settlement for payroll verification
  - Record payroll clearance status
- Document and survey management
  - Store exit documents and clearance forms
  - Run post-exit surveys and collect feedback

### 2.2 Architecture layers
The BPA is composed of three layers:

1. Business process layer
   - Defines the exit stages and decision points.
   - Coordinates tasks across modules.
2. Data integration layer
   - Exchanges employee, payroll, attendance, workforce, performance, and compliance data.
3. Application layer
   - Implements the current UI sections and controller endpoints.

### 2.3 Process architecture map
This is the architecture structure for your exit management BPA.

- Resignation Intake
  - Input: employee details, resignation type, reason, notice date, last working date.
  - Output: `exit_resignations` record with `pending` status.
  - Consumer: Review and approval.
- Review and Approval
  - Input: resignation request, manager/HR decision, employee reporting line.
  - Output: status change to `approved` or `rejected`, audit fields.
  - Consumer: Interview scheduling, settlement initiation, closure.
- Interview Management
  - Input: approved resignation, interviewer selection, schedule.
  - Output: `exit_interviews` entry, feedback, completion status.
  - Consumer: HR knowledge insights, exit closure.
- Knowledge Transfer Management
  - Input: approved resignation, successor assignment, task list.
  - Output: transfer plan and item completion statuses.
  - Consumer: continuity planning, handover completion.
- Settlement Management
  - Input: employee salary data, accumulated benefits, leave/attendance, approved resignation.
  - Output: `exit_employee_settlements`, payout amount, settlement status.
  - Consumer: payroll clearance, finance approval.
- Payroll Clearance
  - Input: settlement record, payroll approval decision.
  - Output: `payroll_clearances` entry, approved payout readiness.
  - Consumer: pay release, exit closure.
- Document and Survey Management
  - Input: documents, exit survey design, responses.
  - Output: archived documents, survey results.
  - Consumer: compliance and retention analytics.

### 2.4 System responsibilities
Each supporting system is a data provider or consumer in the exit BPA.

- Workforce / Organization Structure
  - Provides: department, position, job code, reporting manager, org unit, team assignment.
  - Consumed by: resignation eligibility, approval routing, successor assignment.
- Employee Master / Core HR
  - Provides: employee profile, employment status, hire date, termination date, contact data.
  - Consumed by: resignation intake, settlement calculation, documentation.
- Time and Attendance
  - Provides: last attendance date, leave balance, absence/late records.
  - Consumed by: resignation eligibility, notice period validation, settlement deductions.
- Payroll
  - Provides: salary components, loans, benefits, payout formula, clearance status.
  - Consumed by: settlement creation, payroll approval, net payable confirmation.
- Performance Management
  - Provides: disciplinary records, performance ratings, probation status.
  - Consumed by: approval risk review and exit decision support.
- Learning and Development
  - Provides: training records, certifications, successor readiness.
  - Consumed by: knowledge transfer assignment and gap planning.
- Recruitment
  - Provides: open replacement requisitions, candidate pipeline.
  - Consumed by: workforce planning for backfill after exit.
- Legal Compliance
  - Provides: contract clauses, policy violations, clearance requirements.
  - Consumed by: final exit approval, document retention, compliance checks.
- Engagement Relations
  - Provides: grievances, exit survey trends, retention intelligence.
  - Consumed by: feedback analysis and retention improvement.

### 2.5 Data exchange table
| Source / System | Data provided to Exit Management | Purpose in BPA |
|---|---|---|
| Workforce | department, position, org unit, manager, job code | route approvals, assign successor, validate role impact |
| Employee Master | employee status, hire date, contact, employee_id | create resignation, identify employee, validate eligibility |
| Time Attendance | last attendance date, leave balance, absence history | confirm last working day, apply deductions, approve clearance |
| Payroll | salary components, loan balances, benefits, payout rules | build settlement, check gross/net payable, clear payroll |
| Performance | disciplinary flags, ratings, probation | risk review, exit approvals, audit notes |
| Learning Development | training status, successor readiness | assign handover tasks, validate knowledge transfer |
| Recruitment | replacement status, open requisitions | trigger refill planning, reduce vacancy time |
| Legal Compliance | clearance checklist, contract issues | verify legal exit conditions, archive documentation |
| Engagement Relations | survey results, grievances | capture exit sentiment, improve retention process |

### 2.6 Architecture outcome
This BPA is a system of process domains and data integrations rather than a simple flowchart.

- The business process architecture shows:
  - what each exit domain does
  - what data it needs
  - which systems supply it
  - what output is produced

- For your BPA, use the architecture map above as the core.
- Translate it into diagrams or documentation using:
  - process domain blocks
  - system interfaces
  - inputs and outputs
  - decision points
  - supporting system data flows

## 3. Suggested BPA statuses
Use a simple lifecycle:
- draft
- submitted
- under_review
- approved
- preclearance_complete
- settlement_pending
- settlement_approved
- closed
- rejected
- withdrawn

## 4. Roles involved
- Employee
- Immediate Supervisor
- HR Admin
- Payroll Officer
- IT / Admin Support
- Department Head
- Finance / Compliance

## 5. Systems in the HR ecosystem that should connect
These are the most relevant modules in the current repository that should be integrated with exit management:

### Core employee data
- employee/employee.php
- auth/user.php
- user_profile/
- Why: employee profile, manager hierarchy, approved users, reporting lines

### Payroll
- payroll/
- Why: final pay, benefits, loans, gratuity, provident fund, clearance

### Time and Attendance
- time_attendance/
- Why: attendance history, leave balance, absences, final timekeeping records

### Recruitment / Onboarding
- recruitment/
- Why: replacement hiring, onboarding handoff, new hire info, candidate pipeline

### Performance Management
- performance/
- Why: performance reviews, disciplinary records, probation status

### Learning and Development
- learning_development/
- Why: training records, successor readiness, knowledge transfer support

### Legal Compliance
- legal_compliance/
- Why: contract compliance, policy violations, investigation records

### Workforce / Organization Structure
- workforce/
- Why: departments, positions, reporting hierarchy, organizational changes

### Engagement Relations
- engagement_relations/
- Why: grievances, employee feedback, survey insights, retention trends

## 6. Suggested integration points
- Employee Master → verify employee exists and get department/position
- Payroll → trigger final settlement and payout
- Time Attendance → validate leave balances and attendance history
- Performance → check disciplinary or performance issues
- Learning Development → assign successor and training path
- Recruitment → open replacement requisition
- Legal Compliance → check contract and policy compliance
- User / Auth → assign approval rights and notifications

## 7. Suggested implementation approach in this project
1. Keep the existing exit tables as the main source of truth.
2. Add workflow status and approval history fields to exit_resignations.
3. Add notification support for HR, payroll, and supervisors.
4. Add API-style endpoints to pull data from connected modules.
5. Create dashboard widgets for:
   - pending exits
   - approval queue
   - payroll clearance pending
   - knowledge transfer overdue
   - exit interview completion rate

## 8. Recommended next step
Implement the first automation cycle:
- resignation submitted
- manager approval
- HR review
- settlement request
- payroll clearance
- closure

That gives you a minimal but useful BPA without overbuilding the system at once.
