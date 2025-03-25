/*4. Sa se creeze un pachet care sa contina:
- o functie prin care se vor returna toti medicii care indeplinesc o anumita conditie, data ca
parametru;
- o functie prin care se vor returna toti medicii care lucreaza intr-un anumit spital al carui id e dat ca parametru
*/
CREATE OR REPLACE PACKAGE test_package AS
    TYPE r_cursor IS REF CURSOR;
    FUNCTION get_by_cond(cond VARCHAR2) RETURN r_cursor;
    FUNCTION get_by_h_id(h_id hospitals_medics_t.hospital_id%TYPE) RETURN r_cursor;
END test_package;
/

CREATE OR REPLACE PACKAGE BODY test_package AS
    FUNCTION get_by_cond(cond VARCHAR2) RETURN r_cursor IS
    by_cond_cursor r_cursor ;
    query_stmt VARCHAR(500);
    BEGIN
        query_stmt := 'SELECT * FROM medics_t ' || cond;
        OPEN by_cond_cursor FOR query_stmt;
        RETURN by_cond_cursor;
    END;
    
    FUNCTION get_by_h_id(h_id hospitals_medics_t.hospital_id%TYPE) RETURN r_cursor IS
    by_h_id_cursor r_cursor;
    query_stmt VARCHAR(500);
    
    BEGIN
        query_stmt := 'SELECT m.medic_id, m.medic_name, m.years_exp, m.specialization_id
                       FROM hospitals_medics_t hm 
                       JOIN medics_t m ON m.medic_id = hm.medic_id
                       WHERE hospital_id = :h_id';
        
        OPEN by_h_id_cursor FOR query_stmt USING h_id;
        RETURN by_h_id_cursor;
    END;
END test_package;
/

DECLARE 
    test_cursor test_package.r_cursor;
    medic_rec medics_t%ROWTYPE;
BEGIN 
    DBMS_OUTPUT.PUT_LINE('By condition test');
    test_cursor := test_package.get_by_cond('WHERE years_exp > 5');
    LOOP
        FETCH test_cursor INTO medic_rec;
        EXIT WHEN test_cursor%NOTFOUND;
        
        DBMS_OUTPUT.PUT_LINE('Medic with id ' || medic_rec.medic_id || ' is called ' || medic_rec.medic_name || ' has specialization ' ||
                             medic_rec.specialization_id || ' and has ' || medic_rec.years_exp || ' years experience');
    END LOOP;
    CLOSE test_cursor;
    
    DBMS_OUTPUT.PUT_LINE('********************************************************');
    
    DBMS_OUTPUT.PUT_LINE('By hospital_id test');
    test_cursor := test_package.get_by_h_id(4);
    LOOP
        FETCH test_cursor INTO medic_rec;
        EXIT WHEN test_cursor%NOTFOUND;
        
        DBMS_OUTPUT.PUT_LINE('Medic with id ' || medic_rec.medic_id || ' is called ' || medic_rec.medic_name || ' has specialization ' ||
                             medic_rec.specialization_id || ' and has ' || medic_rec.years_exp || ' years experience');
    END LOOP;
    CLOSE test_cursor;
END;
/