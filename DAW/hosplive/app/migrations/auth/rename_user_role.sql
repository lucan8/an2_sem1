/*user is now pacient*/
UPDATE roles SET role_name = 'pacient' WHERE role_name = 'user';

INSERT INTO migrations (migration_name) VALUES ('rename_user_role');