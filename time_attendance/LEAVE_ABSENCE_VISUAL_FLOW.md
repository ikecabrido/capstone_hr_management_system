# Leave & Absence Integration - Visual Flow

## System Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                    EMPLOYEE TAKES LEAVE                         │
│                    (March 10-12, 2026)                          │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           ▼
            ┌──────────────────────────────┐
            │ Department Head Review       │
            │ (Approves or Rejects)        │
            └───────────────┬──────────────┘
                            │
              ┌─────────────┴─────────────┐
              │                           │
         APPROVED              REJECTED OR PENDING
              │                           │
              ▼                           ▼
    ┌────────────────────┐      System does nothing
    │ HR Final Review    │      Stays in PENDING
    │ (Approve/Reject)   │      or REJECTED status
    └──────────┬─────────┘
               │
    ┌──────────┴─────────────┐
    │                        │
APPROVED              REJECTED
    │                        │
    ▼                        ▼
┌─────────────────────┐  ┌──────────────────────┐
│ LeaveAbsenceHelper  │  │ LeaveAbsenceHelper   │
│ ::onLeaveApproved() │  │ ::onLeaveRejected()  │
└──────────┬──────────┘  └─────────┬────────────┘
           │                       │
           ▼                       ▼
┌─────────────────────────────┐   ┌──────────────────────────┐
│ For each day (Mar 10-12):   │   │ For leave-linked records:│
│                             │   │                          │
│ 1. Check attendance         │   │ 1. Set is_excused = 0   │
│ 2. If ABSENT/LATE found:    │   │ 2. Clear leave_req_id   │
│    → UPDATE to excused      │   │ 3. Status = PENDING     │
│ 3. If no record:            │   │ 4. Can now be manually  │
│    → INSERT excused record  │   │    appealed              │
│ 4. Link to leave_request_id │   └──────────────┬───────────┘
│ 5. Mark as APPROVED_LEAVE   │                  │
└──────────┬──────────────────┘                  │
           │                                     │
           ▼                                     ▼
┌─────────────────────────────┐   ┌──────────────────────────┐
│ Database Updated:           │   │ Database Reverted:       │
│                             │   │                          │
│ • is_excused = 1           │   │ • is_excused = 0        │
│ • excuse_type = APPROVED   │   │ • excuse_type = MANUAL  │
│ • leave_request_id = 123   │   │ • leave_request_id = NULL
│ • reason = "Approved Leave"│   │ • status = PENDING      │
│ • status = APPROVED        │   └──────────────┬───────────┘
└──────────┬──────────────────┘                 │
           │                                    │
           ▼                                    ▼
┌──────────────────────────┐   ┌────────────────────────────┐
│ UI Shows:                │   │ UI Shows:                  │
│                          │   │                            │
│ ✓ Leave Approved        │   │ ⏳ Unexcused              │
│ (Green Badge)           │   │ (No badge yet)             │
│                          │   │                            │
│ Employee sees NO        │   │ Employee can NOW           │
│ need to appeal          │   │ submit manual appeal        │
└──────────────────────────┘   └────────────────────────────┘
```

---

## Detailed Workflow - Approved Leave

```
┌─────────────────────────────────────────────────────┐
│           EMPLOYEE DASHBOARD                        │
│  Maria's Attendance for March 2026                  │
│                                                     │
│ March 10 (Mon): [ABSENT] → Excused - Leave Approved│
│ March 11 (Tue): [ABSENT] → Excused - Leave Approved│
│ March 12 (Wed): [ABSENT] → Excused - Leave Approved│
│ March 13 (Thu): [PRESENT] → Present (Normal)       │
│                                                     │
│ Leave Request #123: APPROVED                       │
│ Duration: March 10-12 (3 days)                     │
│ Excuse Type: APPROVED_LEAVE                        │
└─────────────────────────────────────────────────────┘
```

---

## Detailed Workflow - Rejected Leave

```
BEFORE REJECTION:
┌─────────────────────────────────────────────────────┐
│ March 10: [ABSENT] → ✓ Excused - Leave Approved    │
│ March 11: [ABSENT] → ✓ Excused - Leave Approved    │
│ March 12: [ABSENT] → ✓ Excused - Leave Approved    │
│ Leave Request #124: APPROVED_BY_HR                 │
└─────────────────────────────────────────────────────┘
                      │
          (HR clicks REJECT)
                      │
                      ▼
AFTER REJECTION:
┌─────────────────────────────────────────────────────┐
│ March 10: [ABSENT] → ⏳ Unexcused                   │
│ March 11: [ABSENT] → ⏳ Unexcused                   │
│ March 12: [ABSENT] → ⏳ Unexcused                   │
│ Leave Request #124: REJECTED                       │
│                                                     │
│ (Employee can now submit manual excuses)           │
└─────────────────────────────────────────────────────┘
```

---

## Database State Changes

### SCENARIO 1: Before Leave Approval

```
ta_absence_late_records:
┌──────┬───────────┬───────────┬────────────┬──────────────────┐
│ rec  │ emp_id    │ date      │ type       │ is_excused       │
├──────┼───────────┼───────────┼────────────┼──────────────────┤
│ 1    │ 5 (Maria) │ 2026-03-10│ ABSENT     │ 0 (false)        │
│ 2    │ 5 (Maria) │ 2026-03-11│ ABSENT     │ 0 (false)        │
│ 3    │ 5 (Maria) │ 2026-03-12│ ABSENT     │ 0 (false)        │
└──────┴───────────┴───────────┴────────────┴──────────────────┘
```

### SCENARIO 2: After Leave Approval

```
ta_absence_late_records:
┌──────┬───────────┬───────────┬────────────┬────────────┬──────────────────┐
│ rec  │ emp_id    │ date      │ type       │ excuse_typ │ leave_request_id │
├──────┼───────────┼───────────┼────────────┼────────────┼──────────────────┤
│ 1    │ 5 (Maria) │ 2026-03-10│ ABSENT     │ APPROVED_  │ 123              │
│      │           │           │            │ LEAVE      │                  │
├──────┼───────────┼───────────┼────────────┼────────────┼──────────────────┤
│ 2    │ 5 (Maria) │ 2026-03-11│ ABSENT     │ APPROVED_  │ 123              │
│      │           │           │            │ LEAVE      │                  │
├──────┼───────────┼───────────┼────────────┼────────────┼──────────────────┤
│ 3    │ 5 (Maria) │ 2026-03-12│ ABSENT     │ APPROVED_  │ 123              │
│      │           │           │            │ LEAVE      │                  │
└──────┴───────────┴───────────┴────────────┴────────────┴──────────────────┘

Plus additional fields updated:
- is_excused: 1 (true)
- excuse_status: APPROVED
- reason: "Approved Leave"
- approval_notes: "Automatically marked as excused due to approved leave request"
- reviewed_date: NOW()
```

### SCENARIO 3: After Leave Rejection

```
ta_absence_late_records:
┌──────┬───────────┬───────────┬────────────┬────────────┬──────────────────┐
│ rec  │ emp_id    │ date      │ type       │ excuse_typ │ leave_request_id │
├──────┼───────────┼───────────┼────────────┼────────────┼──────────────────┤
│ 1    │ 5 (Maria) │ 2026-03-10│ ABSENT     │ MANUAL_    │ NULL             │
│      │           │           │            │ APPEAL     │                  │
├──────┼───────────┼───────────┼────────────┼────────────┼──────────────────┤
│ 2    │ 5 (Maria) │ 2026-03-11│ ABSENT     │ MANUAL_    │ NULL             │
│      │           │           │            │ APPEAL     │                  │
├──────┼───────────┼───────────┼────────────┼────────────┼──────────────────┤
│ 3    │ 5 (Maria) │ 2026-03-12│ ABSENT     │ MANUAL_    │ NULL             │
│      │           │           │            │ APPEAL     │                  │
└──────┴───────────┴───────────┴────────────┴────────────┴──────────────────┘

Reverted fields:
- is_excused: 0 (false)
- excuse_status: PENDING
- leave_request_id: NULL
- approval_notes: "Leave request was rejected"
```

---

## Decision Tree

```
                        ┌──────────────────────┐
                        │ Leave Request ID: 123│
                        │ Status: SUBMITTED    │
                        └──────────┬───────────┘
                                   │
                    ┌──────────────┴──────────────┐
                    │                             │
                    ▼                             ▼
            ┌─────────────────┐        ┌─────────────────┐
            │ Head Approves?  │        │ Head Rejects?   │
            └────────┬────────┘        └────────┬────────┘
                     │ YES                     │ YES
                     ▼                         ▼
            Status: APPROVED_BY_HEAD  Status: REJECTED
            (No absence changes yet)  (End of flow)
                     │
                     ▼
            ┌─────────────────┐
            │ HR Approves?    │
            └────────┬────────┘
                     │
         ┌───────────┴───────────┐
         │ YES                   │ NO (Rejects)
         ▼                       ▼
    ┌─────────────┐      ┌──────────────┐
    │ Status:     │      │ Status:      │
    │ APPROVED_BY │      │ REJECTED     │
    │ HR          │      │              │
    │             │      │ Reverse all  │
    │ TRIGGER:    │      │ leave-linked │
    │ onLeaveApp  │      │ excuses      │
    │ roved()     │      └──────────────┘
    └─────────────┘
         │
         ▼
    Mark all ABSENT/LATE
    for March 10-12 as
    excused with label
    "Leave Approved"
```

---

## Component Interaction

```
┌──────────────────────────────────────────────────────────┐
│                     LEAVE REQUEST APPROVED                │
│                  (HR clicks Approve Button)               │
└────────────────────────┬─────────────────────────────────┘
                         │
                         ▼
            ┌────────────────────────────┐
            │  LeaveController::approve()│
            │                            │
            │  • Update status to        │
            │    APPROVED_BY_HR          │
            │  • Deduct leave balance    │
            │  • CALL HELPER ↓           │
            └────────────┬───────────────┘
                         │
                         ▼
        ┌──────────────────────────────────────┐
        │ LeaveAbsenceHelper::onLeaveApproved()│
        │                                      │
        │ • Get leave request details          │
        │ • Get start/end dates                │
        │ • CALL MODEL ↓                       │
        └────────────┬─────────────────────────┘
                     │
                     ▼
    ┌─────────────────────────────────────┐
    │ AbsenceLateMgmt::                    │
    │ markAbsencesExcusedByLeave()         │
    │                                     │
    │ For each day in leave period:       │
    │ • Check for ABSENT/LATE records     │
    │ • UPDATE or INSERT with:            │
    │   - is_excused = 1                  │
    │   - excuse_type = APPROVED_LEAVE    │
    │   - leave_request_id = 123          │
    │   - reason = "Approved Leave"       │
    │ • Update thresholds                 │
    └──────────┬────────────────────────┘
               │
               ▼
        ┌─────────────────┐
        │ Database Updated│
        │                 │
        │ All records     │
        │ marked as       │
        │ EXCUSED         │
        └─────────────────┘
               │
               ▼
        ┌─────────────────┐
        │ UI Reflects     │
        │ Change          │
        │                 │
        │ Shows "Leave    │
        │ Approved"       │
        │ badge           │
        └─────────────────┘
```

---

## UI State Examples

### Employee Dashboard - Before Leave Approval
```
┌─────────────────────────────────────────────┐
│ My Absence & Late Appeals                   │
├─────────────────────────────────────────────┤
│                                             │
│ March 10: ABSENT [⏳ PENDING]               │
│ Reason: (waiting for leave approval)        │
│                                             │
│ March 11: ABSENT [⏳ PENDING]               │
│ Reason: (waiting for leave approval)        │
│                                             │
│ March 12: ABSENT [⏳ PENDING]               │
│ Reason: (waiting for leave approval)        │
│                                             │
│ Leave Request: IN PROGRESS                 │
│ Status: Awaiting HR Approval                │
└─────────────────────────────────────────────┘
```

### Employee Dashboard - After Leave Approval
```
┌─────────────────────────────────────────────┐
│ My Absence & Late Appeals                   │
├─────────────────────────────────────────────┤
│                                             │
│ March 10: ABSENT [✓ Leave Approved] 🟢     │
│ Reason: Approved Leave                      │
│                                             │
│ March 11: ABSENT [✓ Leave Approved] 🟢     │
│ Reason: Approved Leave                      │
│                                             │
│ March 12: ABSENT [✓ Leave Approved] 🟢     │
│ Reason: Approved Leave                      │
│                                             │
│ Leave Request: APPROVED                    │
│ Status: All absences excused automatically │
└─────────────────────────────────────────────┘
```

### HR Dashboard - Approved Leave Absences
```
┌──────────────────────────────────────────────────────┐
│ Absence & Late Management                            │
├──────────────────────────────────────────────────────┤
│                                                      │
│ Employee: Maria Santos                              │
│ Date: March 10, 2026                                │
│ Type: [ABSENT]                                      │
│ Status: [APPROVED]                                  │
│ Excused: [✓ Leave Approved] 🟢                      │
│ Reason: Approved Leave                              │
│ Leave Req: #123 | Actions: [View] (Read-only)      │
│                                                      │
│ Note: This absence is automatically excused         │
│       due to approved leave request. It cannot      │
│       be manually rejected.                         │
└──────────────────────────────────────────────────────┘
```

---

## Code Execution Path

```
approve_leave_hr.php
    │
    ├─ Validate request
    │
    ├─ Get Leave Request
    │
    ├─ Update status to APPROVED_BY_HR
    │
    ├─ Deduct leave balance
    │
    └─ LeaveController::approve()
            │
            ├─ Update database
            │
            └─ LeaveAbsenceHelper::onLeaveApproved()
                    │
                    ├─ Get leave details
                    │
                    └─ AbsenceLateMgmt::markAbsencesExcusedByLeave()
                            │
                            ├─ Iterate through each day
                            │
                            ├─ Check for existing ABSENT/LATE
                            │
                            ├─ UPDATE or INSERT records
                            │
                            ├─ Link to leave_request_id
                            │
                            └─ Update monthly thresholds
                                    │
                                    └─ Database Committed ✓
```

---

**Visual documentation complete!** 

These diagrams show:
- System workflow from leave submission to absence marking
- Database state changes at each step
- Component interactions
- UI state before and after
- Code execution path
- Decision trees for approval/rejection

All integrated seamlessly! ✨
