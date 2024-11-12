/* CREATE DATABASE HospitalDB;
CREATE USER 'hospital_user'@'127.0.0.1' IDENTIFIED BY 'hospital_pass';
GRANT ALL ON HospitalDB.* TO 'hospital_user'@' */

/* -- The user can select from any table
GRANT SELECT ON HospitalDB.appointments TO 'hospital_user'@'127.0.0.1';
GRANT SELECT ON HospitalDB.users TO 'hospital_user'@'127.0.0.1';
GRANT SELECT ON HospitalDB.counties TO 'hospital_user'@'127.0.0.1';
GRANT SELECT ON HospitalDB.hospitals TO 'hospital_user'@'127.0.0.1';
GRANT SELECT ON HospitalDB.medics TO 'hospital_user'@'127.0.0.1';
GRANT SELECT ON HospitalDB.hospitals_medics TO 'hospital_user'@'127.0.0.1';
GRANT SELECT ON HospitalDB.rooms TO 'hospital_user'@'127.0.0.1';
GRANT SELECT ON HospitalDB.specializations TO 'hospital_user'@'127.0.0.1'; */

-- The user only alter data on appointments and users
/* GRANT INSERT, DELETE, UPDATE ON HospitalDB.users TO 'hospital_user'@'127.0.0.1';
GRANT INSERT, DELETE, UPDATE ON HospitalDB.appointments TO 'hospital_user'@'127.0.0.1'; */
/* USE HospitalDB; */

CREATE TABLE users(
    user_id INTEGER AUTO_INCREMENT,
    user_name VARCHAR(20) NOT NULL,
    password varchar(100) NOT NULL,
    email varchar(100) NOT NULL UNIQUE,
    role_id INTEGER NOT NULL,
    
    CONSTRAINT users_pk PRIMARY KEY(user_id)
    CONSTRAINT users_role_fk FOREIGN KEY(role_id) REFERENCES roles(role_id) ON DELETE RESTRICT
);

CREATE TABLE roles(
    role_id INTEGER AUTO_INCREMENT,
    role_name VARCHAR(20) NOT NULL UNIQUE,
    
    CONSTRAINT roles_pk PRIMARY KEY(role_id)
);

CREATE TABLE counties(
    county_id INTEGER AUTO_INCREMENT,
    county_name VARCHAR(20) NOT NULL UNIQUE,
    
    CONSTRAINT counties_pk PRIMARY KEY(county_id) 
);

CREATE TABLE hospitals(
    hospital_id INTEGER AUTO_INCREMENT,
    county_id INTEGER UNIQUE,
    phone_number char(10) NOT NULL UNIQUE,
    
    CONSTRAINT hospital_pk PRIMARY KEY(hospital_id),

    CONSTRAINT hospital_county_fk FOREIGN KEY(county_id)
    REFERENCES counties(county_id) ON DELETE RESTRICT
);

CREATE TABLE rooms(
    room_id INTEGER,
    hospital_id INTEGER,
    
    CONSTRAINT hospital_room_pk PRIMARY KEY(room_id, hospital_id),

    CONSTRAINT hospital_room_fk FOREIGN KEY(hospital_id) 
    REFERENCES hospitals(hospital_id) ON DELETE RESTRICT
);

CREATE TABLE specializations(
    specialization_id INTEGER AUTO_INCREMENT,
    specialization_name VARCHAR(20) NOT NULL UNIQUE,
    
    CONSTRAINT specializations_pk PRIMARY KEY(specialization_id)
);

CREATE TABLE medics(
    medic_id INTEGER AUTO_INCREMENT, 
    medic_name VARCHAR(20) NOT NULL UNIQUE,
    specialization_id INTEGER,
    years_exp INTEGER NOT NULL,
    
    CONSTRAINT medic_pk PRIMARY KEY(medic_id),
    CONSTRAINT medic_spec_fk FOREIGN KEY(specialization_id) REFERENCES specializations(specialization_id) ON DELETE RESTRICT
);

CREATE TABLE hospitals_medics(
    hospital_id INTEGER,
    medic_id INTEGER,
    hire_date DATE NOT NULL,
    
    CONSTRAINT hospitals_medics_pk PRIMARY KEY(hospital_id, medic_id),

    CONSTRAINT hosp_med_hosp_fk FOREIGN KEY(hospital_id) 
    REFERENCES hospitals(hospital_id) ON DELETE RESTRICT,

    CONSTRAINT hosp_med_med_fk FOREIGN KEY(medic_id)
    REFERENCES medics(medic_id) ON DELETE RESTRICT
);
CREATE TABLE appointments(
    user_id INTEGER,
    hospital_id INTEGER,
    medic_id INTEGER,
    room_id INTEGER,
    appointment_date DATE,
    duration INTEGER NOT NULL, -- IN MINUTES
    
    CONSTRAINT appointments_pk PRIMARY KEY(room_id, user_id, hospital_id, medic_id, appointment_date),

    CONSTRAINT appointments_room_fk FOREIGN KEY(room_id, hospital_id) 
    REFERENCES rooms(room_id, hospital_id) ON DELETE RESTRICT,

    CONSTRAINT appointments_user_fk FOREIGN KEY(user_id) 
    REFERENCES users(user_id) ON DELETE RESTRICT,

    CONSTRAINT appointments_hosp_med_fk FOREIGN KEY(hospital_id, medic_id) 
    REFERENCES hospitals_medics(hospital_id, medic_id) ON DELETE RESTRICT
);

-- this table is used to keep track of the migrations that have been run
CREATE TABLE migrations (
    migration_id INTEGER AUTO_INCREMENT,
    migration_name VARCHAR(128) NOT NULL UNIQUE,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT migrations_pk PRIMARY KEY(migration_id)
);

INSERT INTO migrations (migration_name) VALUES ('create_db_1');