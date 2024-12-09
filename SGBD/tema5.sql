/*Adaptare ex 7:
Defini?i dou? func?ii locale cu acela?i nume (overload) care s? calculeze media anilor de experienta al medicilor astfel:
- prima func?ie va avea ca argument codul spitalului, adic? func?ia calculeaz? media
anilor de experienta din spitalul specificat;
- a doua func?ie va avea dou? argumente, unul reprezentând codul spitalului, iar cel?lalt
reprezentând codul de specializare al medicului, adic? func?ia va calcula media anilor de experienta ale medicilor dintr-un
anumit spital ?i care au o anumita specializare.*/

DECLARE
    -- Avg by hospital
    FUNCTION avg_func (h_id hospitals_t.hospital_id%TYPE)
    RETURN NUMBER IS
    res NUMBER(10,2);
    
    BEGIN
        SELECT AVG(m.years_exp)
        INTO res
        FROM medics_t m
        JOIN hospitals_medics_t hm 
        ON hm.medic_id = m.medic_id
        WHERE hm.hospital_id = h_id;
        
        RETURN res;
    END;
    
    -- Avg by hospital and specialization
    FUNCTION avg_func (h_id hospitals_t.hospital_id%TYPE, spec_id medics_t.specialization_id%TYPE)
    RETURN NUMBER IS
    res NUMBER(10,2);
    
    BEGIN
        SELECT AVG(m.years_exp)
        INTO res
        FROM medics_t m
        JOIN hospitals_medics_t hm 
        ON hm.medic_id = m.medic_id
        WHERE hm.hospital_id = h_id AND m.specialization_id = spec_id;
        
        RETURN res;
    END;
    
    -- Testing above functions
    PROCEDURE test_avg
    IS avg1 NUMBER(10,2); avg2 NUMBER(10,2);
    BEGIN
        FOR hosp IN (SELECT hospital_id id FROM hospitals_t) LOOP
            FOR spec IN (SELECT DISTINCT specialization_id id FROM medics_t) LOOP
                avg2 := avg_func(hosp.id, spec.id);
                IF avg2 IS NULL THEN
                    DBMS_OUTPUT.PUT_LINE('No medics in hospital ' || hosp.id || ' have specialization ' || spec.id);
                ELSE
                    DBMS_OUTPUT.PUT_LINE('Avg years of experience for medics in hospital with id ' || hosp.id ||
                                         ' that have the specialization with id ' || spec.id || ': ' || avg2);
                END IF;
            END LOOP;
            avg1:=avg_func(hosp.id);
            IF avg1 IS NULL THEN 
                DBMS_OUTPUT.PUT_LINE('No medics work in hospital ' || hosp.id);
            ELSE
                DBMS_OUTPUT.PUT_LINE('  Avg years of experience for medics in hospital with id ' || hosp.id || ': ' || avg1);
            END IF;
        END LOOP;
    END test_avg;

BEGIN 
    test_avg();
END;
/
