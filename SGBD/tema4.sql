/*Adaptare 10 
    Obtineti toate spitalele si listele cu medici ce lucreaza pentru acestea
    Rezolvati problema folosind:
        a. cele trei tipuri de cursoare studiate;
        b. expresii cursor.
*/

/*Explicit cursor*/
DECLARE CURSOR hidc IS
    SELECT hospital_id
    FROM hospitals_t;
    
    TYPE medics_table IS TABLE OF medics_t.medic_name%TYPE;
    h_id hospitals.hospital_id%TYPE;
    h_medics_names medics_table;
BEGIN
    DBMS_OUTPUT.PUT_LINE('Explicit cursor');
    OPEN hidc;
    LOOP
        FETCH hidc INTO h_id;
        EXIT WHEN hidc%NOTFOUND;
        -- Getting the medic names
        SELECT m.medic_name BULK COLLECT INTO h_medics_names
        FROM hospitals_medics_t hm JOIN medics_t m
        ON m.medic_id = hm.medic_id WHERE hm.hospital_id = h_id;
        
        --Printing the list of medics for current hospital
        IF h_medics_names.COUNT = 0 THEN
            DBMS_OUTPUT.PUT_LINE('  Hospital with id ' || to_char(h_id) || ' has no medics');
        ELSE
            DBMS_OUTPUT.PUT_LINE('  Hospital with id ' || to_char(h_id) || ' medic list: ');
            FOR i in h_medics_names.FIRST..h_medics_names.LAST LOOP
                DBMS_OUTPUT.PUT_LINE('      ' || h_medics_names(i));
            END LOOP;
        END IF;
    END LOOP;
    CLOSE hidc;
END;
/

/*Loop cursor*/
DECLARE CURSOR hidc IS
    SELECT hospital_id
    FROM hospitals_t;
    
    TYPE medics_table IS TABLE OF medics_t.medic_name%TYPE;
    h_medics_names medics_table;
BEGIN
    DBMS_OUTPUT.PUT_LINE('Loop cursor');
    FOR h_id IN hidc LOOP
         -- Getting the medic names
        SELECT m.medic_name BULK COLLECT INTO h_medics_names
        FROM hospitals_medics_t hm JOIN medics_t m
        ON m.medic_id = hm.medic_id WHERE hm.hospital_id = h_id.hospital_id;
        
        --Printing the list of medics for current hospital
        IF h_medics_names.COUNT = 0 THEN
            DBMS_OUTPUT.PUT_LINE('  Hospital with id ' || to_char(h_id.hospital_id) || ' has no medics');
        ELSE
            DBMS_OUTPUT.PUT_LINE('  Hospital with id ' || to_char(h_id.hospital_id) || ' medic list: ');
            FOR i in h_medics_names.FIRST..h_medics_names.LAST LOOP
                DBMS_OUTPUT.PUT_LINE('      ' || h_medics_names(i));
            END LOOP;
        END IF;
    END LOOP;
END;
/

DECLARE 
    has_medics BOOLEAN;
/* Loop cursor subquery*/
BEGIN
    DBMS_OUTPUT.PUT_LINE('Loop cursor subquery');
    FOR h_id IN (SELECT hospital_id FROM hospitals_t) LOOP
        has_medics := False;
         -- Getting the medic names
        FOR m_name in (SELECT m.medic_name medic_name
                       FROM hospitals_medics_t hm JOIN medics_t m
                       ON m.medic_id = hm.medic_id WHERE hm.hospital_id = h_id.hospital_id) LOOP
            --If at first iteration we also print the hospital
            if has_medics = False THEN
                DBMS_OUTPUT.PUT_LINE('  Hospital with id ' || to_char(h_id.hospital_id) || ' medic list: ');
            END IF;
            
            -- Printing the current medic and setting has_medics to True
            DBMS_OUTPUT.PUT_LINE('      ' || m_name.medic_name);
            has_medics := True;
        END LOOP;
            
        -- If no medics are in current hospital we print appropriate message
        IF has_medics = False THEN
            DBMS_OUTPUT.PUT_LINE('  Hospital with id ' || to_char(h_id.hospital_id) || ' has no medics');
        END IF;
    END LOOP;
END;
/

/*Cursor expressions*/
DECLARE
TYPE refcursor IS REF CURSOR;
CURSOR hidc IS
    SELECT hospital_id,
    CURSOR (SELECT m.medic_name
            FROM hospitals_medics_t hm JOIN medics_t m
            ON m.medic_id = hm.medic_id WHERE hm.hospital_id = h.hospital_id)
    FROM hospitals_t h;
    
h_id hospitals_t.hospital_id%TYPE;
medics_names_c refcursor;
medic_name medics_t.medic_name%TYPE;
has_medics BOOLEAN;
BEGIN
    OPEN hidc;
    LOOP
        FETCH hidc INTO h_id, medics_names_c;
        EXIT WHEN hidc%NOTFOUND;
        has_medics := False;
        LOOP
            FETCH medics_names_c INTO medic_name;
            EXIT WHEN medics_names_c%NOTFOUND;
            
            if has_medics = False THEN
                DBMS_OUTPUT.PUT_LINE('  Hospital with id ' || to_char(h_id) || ' medic list: ');
                has_medics := True;
            END IF;
            
            DBMS_OUTPUT.PUT_LINE('      ' || medic_name);
        END LOOP;
           IF has_medics = False THEN
            DBMS_OUTPUT.PUT_LINE('  Hospital with id ' || to_char(h_id) || ' has no medics');
        END IF;
    END LOOP;
    CLOSE hidc;
END;
/