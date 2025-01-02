-- No two appointments can be scheduled at the same time for the same user, medic or room
ALTER TABLE appointments ADD CONSTRAINT UNIQUE(user_id, appointment_date, appointment_time),
						 ADD CONSTRAINT UNIQUE(medic_id, appointment_date, appointment_time),
                         ADD CONSTRAINT UNIQUE(hospital_id, room_id, appointment_date, appointment_time);

INSERT INTO migrations (migration_name) VALUES ('appointments_add_uniques1');