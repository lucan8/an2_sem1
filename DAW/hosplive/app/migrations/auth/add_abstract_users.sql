/*Table users will become a reference point for all user types: (pacient, medic and hospital)*/
/*Taking into account that we already have existing medics and hospitals we will let the user_id be nullable*/
/*Normaly user_id would be unique and not null*/
/*Also we should remove medic_name from medics and phone_number from hospitals because they already exist in users
we will have duplicate data, but all code will be based on users table*/
/*Create table pacients*/
CREATE TABLE pacients(
    pacient_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER,

    CONSTRAINT pacient_user_fk FOREIGN KEY(user_id) REFERENCES users(user_id)
);

UPDATE roles SET role_name = 'pacient' WHERE role_name = 'user';
/* Medics references users*/
/*New medics will have medic_name null because it will be in users table*/
ALTER TABLE medics ADD COLUMN user_id INTEGER DEFAULT NULL
                   MODIFY COLUMN medic_name VARCHAR(20) DEFAULT NULL;
ALTER TABLE medics ADD CONSTRAINT fk_medics_users FOREIGN KEY (user_id) REFERENCES users(user_id);

/* Hospitals references users*/
/*New hospitals will have phone_number null because it will be in users table*/
ALTER TABLE hospitals ADD COLUMN user_id INTEGER DEFAULT NULL,
                      MODIFY COLUMN phone_number CHAR(10) DEFAULT NULL;
ALTER TABLE hospitals ADD CONSTRAINT fk_hospitals_users FOREIGN KEY (user_id) REFERENCES users(user_id);
INSERT INTO migrations (migration_name) VALUES ('add_abstract_users');