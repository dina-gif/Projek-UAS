-- Migration: add transaction_id and raw_notification to pembayaran
ALTER TABLE pembayaran
  ADD COLUMN IF NOT EXISTS transaction_id VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS raw_notification LONGTEXT DEFAULT NULL;

-- Note: MySQL versions < 8.0 do not support IF NOT EXISTS for ADD COLUMN.
-- If your MySQL does not support it, run these two statements separately and ignore errors if column already exists:
-- ALTER TABLE pembayaran ADD COLUMN transaction_id VARCHAR(255) DEFAULT NULL;
-- ALTER TABLE pembayaran ADD COLUMN raw_notification LONGTEXT DEFAULT NULL;

-- After running migration, verify with:
-- DESCRIBE pembayaran;