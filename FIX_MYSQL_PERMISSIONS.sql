-- Fix MySQL permissions to allow root access from all IP addresses
-- This is needed for mobile devices to access the database

-- Allow root to connect from any IP address
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY '' WITH GRANT OPTION;

-- Allow root to connect from specific IPs
GRANT ALL PRIVILEGES ON *.* TO 'root'@'192.168.68.151' IDENTIFIED BY '' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY '' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' IDENTIFIED BY '' WITH GRANT OPTION;

-- Flush privileges to apply changes
FLUSH PRIVILEGES;

-- Verify the permissions were set
SELECT user, host FROM mysql.user WHERE user='root';
