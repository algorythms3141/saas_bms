-- Fix Admin Password
-- This will reset the admin password to: admin123

USE saas_bms;

UPDATE users 
SET password = '$2y$10$ee9iyfC5TrRaxCirKfKtm.915Gimpb1kYrvNV/xwCPP7vKHl.raLa' 
WHERE username = 'admin';

-- Verify the update
SELECT id, username, email, role, status 
FROM users 
WHERE username = 'admin';

-- Made with Bob
