-- Create default admin user
-- Email: admin@university.edu
-- Password: admin
-- Registration Number: ADMIN001

INSERT INTO users (name, email, password, registration_number, year, role) 
VALUES ('Admin User', 'admin@university.edu', '$2y$12$drEGwWIcs8.TxBA5I5po4Oi0lP8TD4QyFiq3Wgm4..3fvGBUC3Tpi', 'ADMIN001', 1, 'admin');
