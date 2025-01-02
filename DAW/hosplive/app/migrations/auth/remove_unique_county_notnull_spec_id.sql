/*Letting more hospitals exist in the same county*/
ALTER TABLE hospitals MODIFY COLUMN county_id INTEGER NOT NULL;

/*Temporarily reomivng the foreign key constraint*/
ALTER TABLE hospitals DROP FOREIGN KEY hospital_county_fk;

/*Dropping the unique constraint on the county_id column*/
DROP INDEX county_id ON hospitals;

/*Re-adding the foreign key constraint*/
ALTER TABLE hospitals ADD CONSTRAINT hospital_county_fk FOREIGN KEY (county_id) REFERENCES counties(county_id);

/*Making the medic specializaiton not null, forgot to add this when creating the table*/
ALTER TABLE medics MODIFY COLUMN specialization_id INTEGER NOT NULL;

INSERT INTO migrations (migration_name) VALUES ('remove_unique_county_notnull_spec_id');