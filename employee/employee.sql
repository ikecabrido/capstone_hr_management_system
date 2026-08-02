CREATE TABLE IF NOT EXISTS employees (
    employee_id VARCHAR(50) PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    address TEXT,
    contact_number VARCHAR(20),
    email VARCHAR(255),
    department VARCHAR(100),
    position VARCHAR(100),
    date_hired DATE,
    employment_status VARCHAR(50) DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO employees (employee_id, full_name, address, contact_number, email, department, position, date_hired, employment_status)
VALUES 
('EMP001', 'John Doe', '123 Main St', '123-456-7890', 'john.doe@example.com', 'IT', 'Software Engineer', '2023-01-01', 'Active'),
('EMP002', 'Jane Smith', '456 Oak Ave', '098-765-4321', 'jane.smith@example.com', 'HR', 'HR Manager', '2023-02-15', 'Active'),
('EMP003', 'Mike Johnson', '789 Pine Rd', '555-123-4567', 'mike.johnson@example.com', 'Finance', 'Accountant', '2023-03-10', 'Active'),
('EMP004', 'Test User 1', '100 Test Blvd', '111-222-3333', 'test.user1@example.com', 'IT', '', '2024-01-01', 'Active'),
('EMP005', 'Test User 2', '101 Test Blvd', '111-222-3334', 'test.user2@example.com', 'IT', '', '2024-01-02', 'Active'),
('EMP006', 'Test User 3', '102 Test Blvd', '111-222-3335', 'test.user3@example.com', 'IT', '', '2024-01-03', 'Active'),
('EMP007', 'Test User 4', '103 Test Blvd', '111-222-3336', 'test.user4@example.com', 'IT', '', '2024-01-04', 'Active'),
('EMP008', 'Test User 5', '104 Test Blvd', '111-222-3337', 'test.user5@example.com', 'IT', '', '2024-01-05', 'Active'),
('EMP009', 'Test User 6', '105 Test Blvd', '111-222-3338', 'test.user6@example.com', 'IT', '', '2024-01-06', 'Active');