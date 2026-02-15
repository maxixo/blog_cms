-- Migration: Add last_login_at column to users table
-- Description: This column tracks when users last logged in for the 48-hour re-verification logic

-- Add last_login_at column
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login_at DATETIME NULL AFTER email_verified_at;

-- Add index for better query performance
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_last_login_at (last_login_at);

-- Success message
SELECT 'Migration completed successfully: last_login_at column added to users table' AS status;