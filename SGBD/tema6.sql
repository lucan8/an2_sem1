--CREATE table counties_t(
--    county_id NUMBER PRIMARY KEY,
--    county_name VARCHAR(15) UNIQUE NOT NULL
--);
--
--INSERT INTO counties_t VALUES(1, 'Bucuresti');
--INSERT INTO counties_t VALUES(2, 'Arges');
--INSERT INTO counties_t VALUES(3, 'Brasov');
--INSERT INTO counties_t VALUES(4, 'Valcea');
--INSERT INTO counties_t VALUES(5, 'Iasi');
--
--ALTER TABLE hospitals_t ADD county_id NUMBER;
--ALTER TABLE hospitals_t ADD FOREIGN KEY(county_id) REFERENCES counties_t(county_id);
--UPDATE hospitals_t SET county_id = hospital_id;

CREATE OR REPLACE PACKAGE medics_hosp_pack AS
    PROCEDURE add_hosp (hosp_id hospitals_t.hospital_id%TYPE,
                        phone_nr hospitals_t.phone_number%TYPE,
                        county_id hospitals_t.county_id%TYPE);
    PROCEDURE add_medic (medic_id medics_t.medic_id%TYPE,
                         medic_name medics_t.medic_name%TYPE,
                         years_exp medics_t.years_exp%TYPE,
                         spec_id medics_t.specialization_id%TYPE);
    FUNCTION valid_spec(spec_id medics_t.specialization_id%TYPE) RETURN NUMBER;
    FUNCTION valid_county(i_county_id counties_t.county_id%TYPE) RETURN NUMBER;
    FUNCTION valid_phone(phone_number hospitals_t.phone_number%TYPE) RETURN NUMBER;
    
END medics_hosp_pack;
/

CREATE OR REPLACE PACKAGE BODY medics_hosp_pack AS
    PROCEDURE add_hosp (hosp_id hospitals_t.hospital_id%TYPE,
                        phone_nr hospitals_t.phone_number%TYPE,
                        county_id hospitals_t.county_id%TYPE) IS
        BEGIN
            IF valid_county(county_id) = 0 THEN
                DBMS_OUTPUT.PUT_LINE('Invalid hospital county ' || county_id);
            ELSE IF valid_phone(phone_nr) = 0 THEN
                DBMS_OUTPUT.PUT_LINE('Invalid hospital phone number ' || phone_nr);
            ELSE
                INSERT INTO hospitals_t VALUES(hosp_id, phone_nr, county_id);
            END IF;
            END IF;
        END add_hosp;
        
   PROCEDURE add_medic (medic_id medics_t.medic_id%TYPE,
                        medic_name medics_t.medic_name%TYPE,
                        years_exp medics_t.years_exp%TYPE,
                        spec_id medics_t.specialization_id%TYPE) IS
        BEGIN
            IF valid_spec(spec_id) = 0 THEN
                DBMS_OUTPUT.PUT_LINE('Invalid medic specialization ' || spec_id);
            ELSE
                INSERT INTO medics_t VALUES(medic_id, medic_name, years_exp, spec_id);
            END IF;
        END add_medic;
    
    FUNCTION valid_spec(spec_id medics_t.specialization_id%TYPE) RETURN NUMBER IS ret_val NUMBER;
        BEGIN 
            SELECT COUNT(*) INTO ret_val FROM specializations_t WHERE specialization_id = spec_id;
            RETURN ret_val;
        END;
    
    FUNCTION valid_county(i_county_id counties_t.county_id%TYPE) RETURN NUMBER IS ret_val NUMBER;
        BEGIN 
            SELECT COUNT(*) INTO ret_val FROM counties_t WHERE county_id = i_county_id;
            RETURN ret_val;
        END;
    
    FUNCTION valid_phone(phone_number hospitals_t.phone_number%TYPE) RETURN NUMBER IS 
        ret_val NUMBER := 1;
        curr_char CHAR(1);
        BEGIN
            FOR i IN 1..length(phone_number) LOOP
                curr_char := substr(phone_number, i, 1);
                IF curr_char < '0' OR curr_char > '9' THEN
                    ret_val := 0;
                END IF;
            END LOOP;
            RETURN ret_val;
        END;
    END medics_hosp_pack;
/
            
EXECUTE medics_hosp_pack.add_hosp(40, '075e893022', 1);
EXECUTE medics_hosp_pack.add_medic(175, 'Reaper_joe', 4, 1);

select * from medics_t;
select * from hospitals_t;

BEGIN
    medics_hosp_pack.add_hosp(174, '0758276999', 100);
    medics_hosp_pack.add_medic(77, 'CvrreveveG', 3, 74);
END;
/
SELECT * FROM medics_t;
select * from hospitals_t;
                        
                        