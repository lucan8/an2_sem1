/*Now that there are only user ids there is no need to specify that this is the user id of a hirer or applicant*/
ALTER TABLE job_applications RENAME COLUMN hirer_user_id TO hirer_id,
                             RENAME COLUMN applicant_user_id TO applicant_id;

/*Appointments now references hirer_id and applicant_id from job_applications
instead of medic_id and hospital_id from hospitals_medics*/
ALTER TABLE appointments DROP CONSTRAINT appointments_hosp_med_fk;
ALTER TABLE appointments ADD CONSTRAINT appointments_hosp_med_fk1 FOREIGN KEY (medic_id, hospital_id) REFERENCES job_applications(applicant_id, hirer_id);

/*Dropping table hospitals_medics because it is no longer needed*/
DROP TABLE hospitals_medics;
INSERT INTO migrations(migration_name) VALUES ('rename_hirer_and_applicant');