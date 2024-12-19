/*Storing the secret key, the active code and the date and time of the creation of the 2FA
Also storing the verified status of the user*/

/*Add neccesary columns for authenticating the user(dropping hid, no longer needed)*/
ALTER TABLE users ADD COLUMN secret VARCHAR(50),
                  ADD COLUMN active_code VARCHAR(10),
                  ADD COLUMN active_code_date DATETIME DEFAULT NOW(),
                  ADD COLUMN verified BOOLEAN DEFAULT FALSE,
                  DROP COLUMN hid;

/*Set the secret and active code to 0 for the dummy user*/
UPDATE users SET secret = 0, active_code = 0 WHERE user_id = 1;

/*Make columns not null*/
ALTER TABLE users MODIFY COLUMN secret VARCHAR(50) NOT NULL ,
                  MODIFY COLUMN active_code VARCHAR(10) NOT NULL,
                  MODIFY COLUMN active_code_date DATETIME NOT NULL,
                  MODIFY COLUMN verified BOOLEAN NOT NULL;

/*Test user*/
INSERT INTO Users(birth_date, gender_id, first_name, last_name, phone_number, user_name,
				  password, email, role_id, secret, active_code)
                  VALUES('2024-12-11', 2, 'L', 'C', '0', 'v',
						 '$2y$10$tmVMmDCl4lBpigrgW7CuiOTFunt4rphsuSLBHIyXFl0qq/20BfKTK', 'gamer.lucan72@gmail.com',
                          2, 'ALGP5XBOQIUAD73I', '297718');

INSERT INTO migrations(name) VALUES ('add_2FA_neccesary_data');