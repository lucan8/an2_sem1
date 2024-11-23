-- Changing the primary key of the appointments table to medic_id, appointment_date, appointment_time
-- The initial config allowed for multiple appointments to be scheduled for the same medic at the same time
DROP PRIMARY KEY;
ALTER TABLE appointments ADD PRIMARY KEY(medic_id, appointment_date, appointment_time);
INSERT INTO migrations(migration_name) VALUES('appointments_primary_key_change1');