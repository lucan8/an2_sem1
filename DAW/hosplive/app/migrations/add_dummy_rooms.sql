/*Adding dummy rooms to hospital 50 for testing purposes until I add a feature to add rooms to hospitals*/
INSERT INTO rooms (room_id, hospital_id) VALUES (1, 50),
                                                (2, 50),
                                                (3, 50),
                                                (4, 50),
                                                (5, 50),
                                                (6, 50),
                                                (7, 50),
                                                (8, 50),
                                                (9, 50),
                                                (10, 50);

INSERT INTO migrations(migration_name) VALUES ('add_dummy_rooms');