--  /*
-- |--------------------------------------------------------------------------
-- | INSERT DATA
-- |--------------------------------------------------------------------------
-- |
-- */
INSERT INTO
    ep_notifications (
        title,
        message,
        type,
        priority,
        target_url,
        created_by_user_id
    )
VALUES
    (
        'Company Orientation',
        'Welcome to the employee orientation program scheduled on August 5, 2026 at 9:00 AM.',
        'announcement',
        'normal',
        'employee-announcements',
        1
    ),
    (
        'Payslip Available',
        'Your payslip for July 2026 is now available for viewing and download.',
        'payroll',
        'important',
        'employee-payslip',
        1
    ),
    (
        'Leave Request Approved',
        'Your vacation leave request from August 10 to August 12 has been approved.',
        'leave',
        'important',
        'employee-leave-request',
        2
    ),
    (
        'Mandatory Cybersecurity Training',
        'All employees are required to complete the Cybersecurity Awareness Training before August 15.',
        'training',
        'urgent',
        'employee-training-programs',
        2
    ),
    (
        'Performance Evaluation',
        'Your mid-year performance evaluation has been scheduled for next week.',
        'performance',
        'important',
        'employee-performance',
        1
    ),
    (
        'Document Submission Reminder',
        'Please submit your updated PhilHealth and Pag-IBIG documents before the deadline.',
        'document',
        'urgent',
        'employee-documents',
        3
    ),
    (
        'Department Meeting',
        'There will be a department meeting on Friday at 10:00 AM via Google Meet.',
        'meeting',
        'normal',
        'employee-meetings',
        2
    ),
    (
        'Compliance Reminder',
        'Complete your annual compliance acknowledgment before August 20.',
        'compliance',
        'urgent',
        'employee-compliance',
        1
    ),
    (
        'Holiday Announcement',
        'The office will be closed on National Heroes Day. Regular operations will resume the following business day.',
        'announcement',
        'normal',
        'employee-announcements',
        1
    ),
    (
        'Employee Satisfaction Survey',
        'Please complete the Employee Satisfaction Survey. Your feedback will remain confidential.',
        'general',
        'normal',
        'employee-dashboard',
        1
    );

INSERT INTO
    ep_notification_recipients (notification_id, employee_id, is_read, read_at)
VALUES
    (1, 1, 0, NULL),
    (2, 2, 1, '2026-08-01 09:15:00'),
    (3, 3, 1, '2026-08-01 10:20:00'),
    (4, 4, 0, NULL),
    (5, 5, 1, '2026-08-02 08:45:00'),
    (6, 6, 0, NULL),
    (7, 7, 1, '2026-08-03 01:30:00'),
    (8, 8, 0, NULL),
    (9, 9, 1, '2026-08-04 11:10:00'),
    (10, 10, 0, NULL);

INSERT INTO
    ep_payroll_request (
        employee_id,
        request_type,
        purpose,
        remarks,
        payroll_period_start,
        payroll_period_end,
        status,
        requested_at,
        processed_at,
        processed_by,
        rejection_reason,
        document_path
    )
VALUES
    (
        1,
        'Payslip',
        'Loan Application',
        'Requesting a copy of my latest payslip for bank requirements.',
        '2026-07-01',
        '2026-07-15',
        'Completed',
        '2026-08-01 08:30:00',
        '2026-08-01 10:15:00',
        3,
        NULL,
        'uploads/payroll/payslip_001.pdf'
    ),
    (
        2,
        'Certificate of Compensation',
        'Visa Application',
        'Required for embassy document verification.',
        NULL,
        NULL,
        'Approved',
        '2026-08-02 09:15:00',
        '2026-08-02 11:20:00',
        3,
        NULL,
        NULL
    ),
    (
        3,
        'BIR Form 2316',
        'Annual Tax Filing',
        'Need a copy for tax filing.',
        '2025-01-01',
        '2025-12-31',
        'Completed',
        '2026-08-03 08:10:00',
        '2026-08-03 02:00:00',
        3,
        NULL,
        'uploads/payroll/bir2316_003.pdf'
    ),
    (
        4,
        'Payslip',
        'Personal Record',
        'Requesting my July payslip.',
        '2026-07-16',
        '2026-07-31',
        'Pending',
        '2026-08-04 09:00:00',
        NULL,
        NULL,
        NULL,
        NULL
    ),
    (
        5,
        'Annual Payroll Summary',
        'Employment Requirement',
        'Needed for employment verification.',
        '2025-01-01',
        '2025-12-31',
        'Processing',
        '2026-08-05 10:45:00',
        NULL,
        3,
        NULL,
        NULL
    ),
    (
        6,
        'Certificate of Employment with Compensation',
        'Housing Loan',
        'Required for Pag-IBIG housing loan application.',
        NULL,
        NULL,
        'Approved',
        '2026-08-06 11:30:00',
        '2026-08-06 03:10:00',
        3,
        NULL,
        NULL
    ),
    (
        7,
        'Payslip',
        'Personal Record',
        'Requesting latest payroll copy.',
        '2026-08-01',
        '2026-08-15',
        'Completed',
        '2026-08-07 08:40:00',
        '2026-08-07 09:50:00',
        3,
        NULL,
        'uploads/payroll/payslip_007.pdf'
    ),
    (
        8,
        'Other Payroll Document',
        'Salary Verification',
        'Need payroll verification for financial records.',
        NULL,
        NULL,
        'Rejected',
        '2026-08-08 09:30:00',
        '2026-08-08 01:15:00',
        3,
        'Requested document is unavailable.',
        NULL
    ),
    (
        9,
        'Payslip',
        'Bank Requirement',
        'Required by the bank for salary verification.',
        '2026-08-01',
        '2026-08-15',
        'Processing',
        '2026-08-09 08:20:00',
        NULL,
        3,
        NULL,
        NULL
    ),
    (
        10,
        'Certificate of Compensation',
        'Personal Record',
        'Need a copy for my employment files.',
        NULL,
        NULL,
        'Pending',
        '2026-08-10 09:45:00',
        NULL,
        NULL,
        NULL,
        NULL
    );

INSERT INTO
    employees (
        user_id,
        employee_no,
        full_name,
        category_id,
        position_id,
        employment_type_id,
        employment_status,
        department,
        date_hired
    )
VALUES
    (
        1,
        'T001',
        'Juan Dela Cruz',
        1,
        1,
        1,
        'active',
        'College Department',
        '2022-01-10'
    ),
    (
        2,
        'T002',
        'Maria Santos',
        1,
        2,
        1,
        'active',
        'College Department',
        '2021-08-15'
    ),
    (
        3,
        'T003',
        'Carlos Mendoza',
        1,
        3,
        2,
        'active',
        'Senior High School Department',
        '2023-03-01'
    ),
    (
        4,
        'T004',
        'Sofia Ramirez',
        1,
        4,
        1,
        'active',
        'College Department',
        '2022-06-20'
    ),
    (
        5,
        'T005',
        'Michael Torres',
        1,
        5,
        3,
        'active',
        'Human Resources Office',
        '2024-01-15'
    ),
    (
        6,
        'T006',
        'Elizabeth Cruz',
        2,
        6,
        2,
        'active',
        'Registrar Office',
        '2020-09-08'
    ),
    (
        7,
        'T007',
        'Daniel Flores',
        2,
        7,
        1,
        'active',
        'Accounting Office',
        '2019-11-18'
    ),
    (
        8,
        'T008',
        'Patricia Aquino',
        2,
        8,
        2,
        'active',
        'Information Technology Office',
        '2023-07-05'
    ),
    (
        9,
        'T009',
        'Joseph Garcia',
        1,
        2,
        1,
        'active',
        'College Department',
        '2021-01-25'
    ),
    (
        10,
        'T010',
        'Angela Reyes',
        2,
        6,
        1,
        'active',
        'Guidance Office',
        '2022-10-12'
    );

INSERT INTO
    ep_resignation_requests (
        employee_id,
        resignation_type,
        resignation_reason,
        resignation_letter,
        date_submitted,
        intended_last_working_day,
        status,
        employee_remarks,
        hr_remarks,
        reviewed_by,
        reviewed_at
    )
VALUES
    (
        1,
        'With Notice',
        'I have accepted a new career opportunity that aligns with my professional goals.',
        'uploads/resignation_letters/resignation_1.pdf',
        '2026-08-01 09:15:00',
        '2026-08-31',
        'Pending',
        'I will ensure a proper turnover of my responsibilities.',
        NULL,
        NULL,
        NULL
    ),
    (
        2,
        'Immediate',
        'Due to personal and family circumstances, I am requesting immediate resignation.',
        'uploads/resignation_letters/resignation_2.pdf',
        '2026-07-28 14:20:00',
        '2026-07-29',
        'Approved',
        'Thank you for the opportunity to work with the institution.',
        'Approved. Immediate resignation has been accepted.',
        1,
        '2026-07-29 10:30:00'
    ),
    (
        3,
        'With Notice',
        'I am pursuing further studies and will no longer be able to continue my employment.',
        NULL,
        '2026-07-25 08:45:00',
        '2026-08-25',
        'Rejected',
        'I appreciate the support provided during my employment.',
        'Please discuss your concerns with your department head before resubmitting.',
        1,
        '2026-07-26 11:15:00'
    );