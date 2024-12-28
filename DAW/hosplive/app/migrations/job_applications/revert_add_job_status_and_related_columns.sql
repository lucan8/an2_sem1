use hospitaldb;
/* This script is used to revert the changes made by the script add_job_status_and_related_columns.sql */
ALTER TABLE medics DROP COLUMN cv_path;
ALTER TABLE hospitals_medics DROP CONSTRAINT fk_hosp_med_job_status,
							 DROP COLUMN job_status_id,
                             DROP COLUMN contract_path;
DROP TABLE job_status;

DELETE FROM migrations WHERE migration_name = 'add_job_status_and_related_columns';