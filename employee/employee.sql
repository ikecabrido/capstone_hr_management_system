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
('EMP003', 'Mike Johnson', '789 Pine Rd', '555-123-4567', 'mike.johnson@example.com', 'Finance', 'Accountant', '2023-03-10', 'Active');