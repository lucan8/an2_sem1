UPDATE roles SET role_name = 'patient' WHERE role_name = 'pacient';

INSERT INTO migrations(migration_name) VALUES('rename_pacient_role');