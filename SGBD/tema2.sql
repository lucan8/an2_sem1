--CREATE TABLE hospitals_medics_t( 
--    hospital_id NUMBER, 
--    medic_id NUMBER, 
--    hire_date DATE NOT NULL, 
--    salary NUMBER NOT NULL,
--    CONSTRAINT hospitals_medics_pk_t PRIMARY KEY(hospital_id, medic_id), 
--    CONSTRAINT hosp_med_hosp_fk_t FOREIGN KEY(hospital_id) REFERENCES hospitals_t(hospital_id), 
--    CONSTRAINT hosp_med_med_fk_t FOREIGN KEY(medic_id) REFERENCES medics_t(medic_id) 
--);
--
--INSERT ALL
--INTO hospitals_medics_t VALUES (1, 1, SYSDATE, 10000)
--INTO hospitals_medics_t VALUES (1, 3, SYSDATE, 5500)
--INTO hospitals_medics_t VALUES (2, 3, SYSDATE, 12000)
--INTO hospitals_medics_t VALUES (2, 2, SYSDATE, 11000)
--INTO hospitals_medics_t VALUES (2, 4, SYSDATE, 5800)
--INTO hospitals_medics_t VALUES (3, 5, SYSDATE, 2700)
--INTO hospitals_medics_t VALUES (4, 4, SYSDATE, 3500)
--INTO hospitals_medics_t VALUES (4, 5, SYSDATE, 4700)
--INTO hospitals_medics_t VALUES (5, 1, SYSDATE, 6900)
--INTO hospitals_medics_t VALUES (3, 2, SYSDATE, 2200)
--SELECT 1 FROM DUAL;


/*5: Id-ul spitalului cu cei mai multi medici
VARIABLE hosp_id NUMBER
BEGIN
    SELECT hospital_id
    INTO :hosp_id
    FROM hospitals_medics_t
    GROUP BY hospital_id
    HAVING COUNT(medic_id) = 
    (SELECT MAX(COUNT(medic_id))
     FROM hospitals_medics_t
     GROUP BY hospital_id);
     dbms_output.put_line('Spitalul cu numar maxim de angajati: ' || :hosp_id);
     
     EXCEPTION
        WHEN TOO_MANY_ROWS THEN
            dbms_output.put_line('Mai mult de un spital are numar maxim de medici');
END;
/
PRINT hosp_id;
*/
/*7: Salariul anual si bonusul unui medic primit de la tastatura
     Avand in vedere ca un medic poate lucra la mai multe spitale
     Vom insuma salariile anuale de la fiecare
     Regula de bonus este prezentata mai jos

VARIABLE m_salary NUMBER
VARIABLE m_bonus NUMBER
DECLARE medic_id_in medics.medic_id%TYPE := '&medic_id_in';
BEGIN
    SELECT SUM(salary * 12) INTO :m_salary
    FROM hospitals_medics_t
    GROUP BY medic_id HAVING medic_id = medic_id_in;
    
    IF :m_salary >= 100000
        THEN :m_bonus:= 20000;
    ELSIF :m_salary BETWEEN 55000 AND 100000
        THEN :m_bonus:= 10000;
    ELSE :m_bonus:= 5500;
    END IF;
            
    dbms_output.put_line('Medicul cu id-ul ' || medic_id_in || ' are bonusul ' || :m_bonus);
    
    --In cazul in care id_ul nu se afla in baza de date afisam pe ecran astfel
    EXCEPTION
        WHEN NO_DATA_FOUND THEN
            dbms_output.put_line('Medicul cu id-ul ' || medic_id_in || ' nu exista in baza de date!');
END;
/
*/
/*9 Primind de la tastatura
    un medic_id, o specializare, si un procent
    schimbam specializare medicului si la anii de experienta adaugam procentul
    
SET VERIFY OFF;
--Inainte de update
SELECT * FROM medics_t;
DECLARE
    v_medic_id medics_t.medic_id%TYPE := '&m_id';
    v_spec_id medics_t.specialization_id%TYPE := '&spec_id';
    v_year_percent NUMBER(8) := '&y_percent';
BEGIN
    UPDATE medics_t SET
    specialization_id = v_spec_id,
    years_exp = years_exp + (v_year_percent * years_exp) / 100
    WHERE medic_id = v_medic_id;
    
    IF SQL%ROWCOUNT =0 THEN
        dbms_output.put_line('Medicul cu id-ul ' || v_medic_id || ' nu exista in baza de date!'); 
    ELSE DBMS_OUTPUT.PUT_LINE('Actualizare realizata');
    END IF;
            
END;
/
--Dupa update
SELECT * FROM medics_t;
ROLLBACK;
SET VERIFY ON;
*/




    