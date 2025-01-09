CREATE TABLE appointments_summary (
    appointment_id INTEGER NOT NULL,
    patient_reason VARCHAR(100) NOT NULL,
    symptoms VARCHAR(100) NOT NULL,
    diagnosis VARCHAR(100) NOT NULL,
    treatment VARCHAR(100) NOT NULL,
    other_observations VARCHAR(100) NOT NULL,
    
    CONSTRAINT appointments_summary_pk PRIMARY KEY (appointment_id),
    CONSTRAINT appointments_summary_fk FOREIGN KEY (appointment_id) REFERENCES appointments(appointment_id) ON DELETE RESTRICT
)ENGINE = InnoDB;

INSERT INTO migrations(migration_name) VALUES ('add_appointments_summary.sql');