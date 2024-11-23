-- Split the appointment_date column into appointment_date and appointment_time
ALTER TABLE appointments ADD COLUMN appointment_time TIME;
UPDATE appointments SET appointment_time = TIME(appointment_date);
ALTER TABLE appointments DROP PRIMARY KEY;
ALTER TABLE appointments MODIFY COLUMN appointment_date DATE;
ALTER TABLE appointments ADD PRIMARY KEY (user_id, hospital_id, medic_id, room_id,
                                          appointment_date, appointment_time);
-- Set the default value for the duration column
ALTER TABLE appointments ALTER duration SET DEFAULT 30;

INSERT INTO migrations (migration_name) VALUES ('split_date_time_appointments');