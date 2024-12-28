/*Deleting the job_application_id 6, which is a duplicate of the job_application_id 5.*/
DELETE FROM job_applications WHERE job_application_id = 6;
/*Job applications are now unique only by the applicant and hirer user ids.*/
ALTER TABLE job_applications DROP INDEX appliant_user_id,
            ADD UNIQUE(applicant_user_id, hirer_user_id);

INSERT INTO migrations(migration_name) VALUES ('remove_unique_by_date');
