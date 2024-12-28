/* Removing all the past medics and hospitals from the database(the generated data)*/
DELETE FROM hospitals_medics;
DELETE FROM medics;
/*Medic name is not needed anymore, because it is stored in the user table*/
ALTER TABLE medics DROP COLUMN medic_name;

DELETE FROM rooms;
DELETE FROM hospitals;
/*The phone number is not needed anymore, because it is stored in the user table
county_id is now unique again*/
ALTER TABLE hospitals DROP COLUMN phone_number,
                      ADD UNIQUE(county_id);

INSERT INTO migrations(migration_name) VALUES('remove_past_medics_hospitals');

