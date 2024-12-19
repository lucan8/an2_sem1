ALTER TABLE appointments DROP PRIMARY KEY;
ALTER TABLE appointments ADD COLUMN appointment_id INT AUTO_INCREMENT PRIMARY KEY FIRST;
INSERT INTO migrations (migration_name) VALUES ('appointments_primary_key_change1');