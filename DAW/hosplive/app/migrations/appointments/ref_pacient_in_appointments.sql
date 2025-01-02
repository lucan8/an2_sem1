/*Deleting the appointments created by the dummy user*/
DELETE FROM appointments WHERE user_id = 1;

/*Removing the dummy user from the users table*/
DELETE FROM users WHERE user_id = 1;

/*Making sure appointments have references to pacients instead of users*/
ALTER TABLE appointments DROP CONSTRAINT appointments_user_fk,
                         RENAME COLUMN user_id TO pacient_id,
                         ADD CONSTRAINT appointments_pacient_fk FOREIGN KEY (pacient_id) REFERENCES pacients(pacient_id);

INSERT INTO migrations(migration_name) VALUES ('ref_pacient_in_appointments');