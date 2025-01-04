/*For each users derived table will be set a primary key that references the users table
and so the initial primary key will be dropped and the one referencing users shall be renamed and used as primary key*/
ALTER TABLE medics DROP COLUMN medic_id;
ALTER TABLE medics RENAME COLUMN user_id TO medic_id;

/*Removing duplicate entry*/
DELETE FROM medics WHERE medic_id=34 ORDER BY medic_id DESC LIMIT 1;
ALTER TABLE medics ADD PRIMARY KEY (medic_id);

/*Removing the reference on patients*/
ALTER TABLE appointments DROP FOREIGN KEY appointments_pacient_fk;

/*Changing the pacients table to patients and changing the primary key to be the one that references users*/
RENAME TABLE pacients TO patients;
ALTER TABLE patients DROP COLUMN pacient_id;
ALTER TABLE patients RENAME COLUMN user_id TO patient_id;
ALTER TABLE patients ADD PRIMARY KEY (patient_id);

/* Renaming the column in appointments and adding the foreign key constraint */
ALTER TABLE appointments RENAME COLUMN pacient_id TO patient_id;
ALTER TABLE appointments ADD CONSTRAINT appointments_patient_fk FOREIGN KEY (patient_id) REFERENCES patients(patient_id);

/*Dropping the reference to hospitals*/
ALTER TABLE rooms DROP FOREIGN KEY hospital_room_fk;

/*Changing the hospitals table to have the primary key be the one that references users*/
ALTER TABLE hospitals DROP COLUMN hospital_id;
ALTER TABLE hospitals RENAME COLUMN user_id TO hospital_id;
ALTER TABLE hospitals ADD PRIMARY KEY (hospital_id);

/* Changing from the old hospital_id to the new one */
UPDATE rooms SET hospital_id = 33;
/* Adding the foreign key constraint */
ALTER TABLE rooms ADD CONSTRAINT hospital_room_fk FOREIGN KEY (hospital_id) REFERENCES hospitals(hospital_id);

INSERT INTO migrations(migration_name) VALUES ('derived_users_primary_key_change1');