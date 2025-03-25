/*9
Definiti tipul medici_sub (vector, dimensiune maxima 10, mentine numere). Creati tabelul
medici_sefi cu urmatoarele câmpuri: cod_ms NUMBER(10), nume VARCHAR2(20), lista
medici_sub. Introduceti 3 linii în tabel. Afisati informatiile din tabel. Stergeti tabelul creat,
apoi tipul.*/
CREATE OR REPLACE TYPE medici_sub AS VARRAY(10) OF NUMBER(4);
/
CREATE TABLE medici_sefi (
    cod_ms NUMBER(10),
    nume VARCHAR2(20),
    lista medici_sub
);
DECLARE
v_sub medici_sub:= medici_sub(1,3,2);
v_lista medici_sefi.lista%TYPE;

BEGIN

    INSERT INTO medici_sefi
    VALUES (1, 'Jack', v_sub);
    
    INSERT INTO medici_sefi
    VALUES (2, 'Alex', null);
    
    INSERT INTO medici_sefi
    VALUES (3, 'John', medici_sub(1,2));
    
    SELECT lista
    INTO v_lista
    FROM medici_sefi
    WHERE cod_ms=1;
    
    FOR j IN v_lista.FIRST..v_lista.LAST loop
        DBMS_OUTPUT.PUT_LINE (v_lista(j));
    END LOOP;
END;
/
SELECT * FROM medici_sefi;
DROP TABLE medici_sefi;
DROP TYPE medici_sub;

/*10:
Creati tabelul medics_test cu coloanele medic_id si medic_name din tabelul medics_t.
Adauga în acest tabel un nou câmp numit phone de tip tablou imbricat. Acest tablou va men?ine
pentru fiecare medic toate numerele de telefon la care poate fi contactat. Inserati o linie noua în
tabel. Actualizati o linie din tabel. Afisati informatiile din tabel. Stergeti tabelul si tipul.*/
CREATE TABLE medics_test AS
SELECT medic_id, medic_name FROM medics_t
WHERE ROWNUM <= 2;

SELECT * FROM medics_test;
CREATE OR REPLACE TYPE phone_type IS TABLE OF VARCHAR(12);
/
ALTER TABLE medics_test
ADD (phone phone_type)
NESTED TABLE phone STORE AS phone_table;

INSERT INTO medics_test
VALUES (6, 'Arda', phone_type('0741234561', '0213123123', '0374451550'));

UPDATE medics_test
SET phone = phone_type('0736657344', '0111233981')
WHERE medic_id=1;

SELECT * FROM medics_test;

SELECT a.medic_id, b.*
FROM medics_test a, TABLE (a.phone) b;
DROP TABLE medics_test;
DROP TYPE phone_type;
