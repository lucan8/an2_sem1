/*Storing the possible application statuses*/
CREATE TABLE application_statuses(
    application_status_id INTEGER AUTO_INCREMENT PRIMARY KEY,
    application_status_name VARCHAR(25) NOT NULL UNIQUE
);
/*Inserting the application statuses*/
INSERT INTO application_statuses(application_status_name) VALUES ('Hired');
INSERT INTO application_statuses(application_status_name) VALUES ('Rejected');
INSERT INTO application_statuses(application_status_name) VALUES ('Interviewing');
INSERT INTO application_statuses(application_status_name) VALUES ('Pending');

/*Creating the job_applications table*/
CREATE TABLE job_applications (
    job_application_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    applicant_user_id INTEGER NOT NULL,
    hirer_user_id INTEGER NOT NULL,
    application_date DATETIME NOT NULL DEFAULT NOW(),
    application_status_id INTEGER NOT NULL,

    FOREIGN KEY (applicant_user_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (hirer_user_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (application_status_id) REFERENCES application_statuses(application_status_id) ON DELETE RESTRICT
);
/*No job applications can have the same applicant, hirer and application date*/
ALTER TABLE job_applications ADD CONSTRAINT UNIQUE(applicant_user_id, hirer_user_id, application_date);

INSERT INTO migrations(migration_name) VALUES ('add_job_applications');