/*Storing the path to the medic's CV*/
ALTER TABLE medics ADD COLUMN cv_path VARCHAR(255) NOT NULL DEFAULT '';
/*Storing the job status of the job application*/
CREATE TABLE job_status(
    job_status_id INTEGER AUTO_INCREMENT PRIMARY KEY,
    job_status_name VARCHAR(255) NOT NULL UNIQUE
);
/*Inserting the job statuses*/
INSERT INTO job_status(job_status_name) VALUES ('Hired');
INSERT INTO job_status(job_status_name) VALUES ('Rejected');
INSERT INTO job_status(job_status_name) VALUES ('Interviewing');
INSERT INTO job_status(job_status_name) VALUES ('Pending');
/*Storing the job status of the medic in the hospital(default is 1 for already inserted medics)*/
ALTER TABLE hospitals_medics ADD COLUMN job_status_id INTEGER NOT NULL DEFAULT 1,
                             ADD CONSTRAINT fk_hosp_med_job_status FOREIGN KEY (job_status_id) REFERENCES job_status(job_status_id);
/*Storing the path to the contract*/
ALTER TABLE hospitals_medics ADD COLUMN contract_path VARCHAR(255) NOT NULL DEFAULT '';

INSERT INTO migrations(migration_name) VALUES ('add_job_status_and_related_columns');