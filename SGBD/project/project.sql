/*4: Implementati in Oracle diagrama conceptuala realizata: definiti toate tabelele, adaugand toate
constrangerile de integritate necesare (chei primare, cheile externe etc).*/
CREATE TABLE genuri(
    cod_gen INTEGER,
    nume_gen VARCHAR(20) NOT NULL UNIQUE,

    CONSTRAINT genuri1_pk PRIMARY KEY(cod_gen)
);

CREATE TABLE utilizatori(
    cod_utilizator INTEGER,
    data_nastere DATE NOT NULL,
    prenume VARCHAR(20) NOT NULL,
    nume_de_famile VARCHAR(20) NOT NULL,
    numar_telefon CHAR(10) NOT NULL UNIQUE,

    nume_utilizator VARCHAR(20) NOT NULL UNIQUE,
    parola varchar(100) NOT NULL,
    
    cod_gen INTEGER NOT NULL,
    cod_rol INTEGER NOT NULL,
    
    CONSTRAINT utilizatori_pk PRIMARY KEY(cod_utilizator),
    CONSTRAINT utilizatori_rol_fk FOREIGN KEY(cod_rol) REFERENCES roluri(cod_rol),
    CONSTRAINT utilizatori_gen_fk FOREIGN KEY(cod_gen) REFERENCES genuri(cod_gen)
);

CREATE TABLE judete(
    cod_judet INTEGER ,
    nume_judet VARCHAR(20) NOT NULL UNIQUE,
    
    CONSTRAINT judete_pk PRIMARY KEY(cod_judet) 
);

CREATE TABLE spitale(
    cod_spital INTEGER ,
    cod_judet INTEGER NOT NULL UNIQUE,
    numar_telefon CHAR(10) NOT NULL UNIQUE,
    
    CONSTRAINT spital_pk PRIMARY KEY(cod_spital),

    CONSTRAINT spital_judet_fk FOREIGN KEY(cod_judet) REFERENCES judete(cod_judet)
);

CREATE TABLE camere(
    cod_camera INTEGER,
    cod_spital INTEGER,
    
    CONSTRAINT spital_camera_pk PRIMARY KEY(cod_camera, cod_spital),
    CONSTRAINT spital_camera_fk FOREIGN KEY(cod_spital) REFERENCES spitale(cod_spital)
);

CREATE TABLE specializari(
    cod_specializare INTEGER,
    nume_specializare VARCHAR(20) NOT NULL UNIQUE,
    
    CONSTRAINT specializari_pk PRIMARY KEY(cod_specializare)
);

CREATE TABLE medici(
    cod_medic INTEGER , 
    nume_medic VARCHAR(20) NOT NULL UNIQUE,
    cod_specializare INTEGER NOT NULL,
    ani_experienta INTEGER NOT NULL,
    
    CONSTRAINT medic_pk PRIMARY KEY(cod_medic),
    CONSTRAINT medic_spec_fk FOREIGN KEY(cod_specializare) REFERENCES specializari(cod_specializare)
);

CREATE TABLE spitale_medici(
    cod_spital INTEGER,
    cod_medic INTEGER,
    data_angajare DATE NOT NULL,
    salariu NUMBER(6, 2) NOT NULL,
    
    CONSTRAINT spitale_medici_pk PRIMARY KEY(cod_spital, cod_medic),
    CONSTRAINT hosp_med_hosp_fk FOREIGN KEY(cod_spital)  REFERENCES spitale(cod_spital),
    CONSTRAINT hosp_med_med_fk FOREIGN KEY(cod_medic) REFERENCES medici(cod_medic)
);
CREATE TABLE programari(
    cod_utilizator INTEGER,
    cod_spital INTEGER,
    cod_medic INTEGER,
    cod_camera INTEGER,
    data_programare DATE,
    durata INTEGER DEFAULT 30 NOT NULL, -- IN MINUTES
    
    CONSTRAINT programari_pk PRIMARY KEY(cod_utilizator, cod_spital, cod_medic, cod_camera, data_programare),

    CONSTRAINT programari_camera_fk FOREIGN KEY(cod_camera, cod_spital)  REFERENCES camere(cod_camera, cod_spital),
    CONSTRAINT programari_user_fk FOREIGN KEY(cod_utilizator)  REFERENCES utilizatori(cod_utilizator),
    CONSTRAINT programari_hosp_med_fk FOREIGN KEY(cod_spital, cod_medic)  REFERENCES spitale_medici(cod_spital, cod_medic)
);

/*5. Adauga?i informatii coerente in tabelele create (minim 5 inregistrari pentru
fiecare entitate
independenta; minim 10 inregistrari pentru fiecare tabela asociativa).*/
--genuri data
INSERT INTO genuri VALUES(1, 'feminin');
INSERT INTO genuri VALUES(2, 'masculin');
INSERT INTO genuri VALUES(3, 'nebinar');
INSERT INTO genuri VALUES(4, 'omnigen');
INSERT INTO genuri VALUES(5, 'neurogen');


-- judet data
INSERT INTO judete VALUES(1, 'Bucuresti');
INSERT INTO judete VALUES(2, 'Cluj');
INSERT INTO judete VALUES(3, 'Iasi');
INSERT INTO judete VALUES(4, 'Constanta');
INSERT INTO judete VALUES(5, 'Timis');

-- spital data
INSERT INTO spitale VALUES(1, 1, '0218713860');
INSERT INTO spitale VALUES(2, 2, '0362952363');
INSERT INTO spitale VALUES(3, 3, '0786537062');
INSERT INTO spitale VALUES(4, 4, '0255249892');
INSERT INTO spitale VALUES(5, 5, '0351066109');

-- camere data
INSERT INTO camere VALUES(1, 1);
INSERT INTO camere VALUES(2, 1);
INSERT INTO camere VALUES(1, 2);
INSERT INTO camere VALUES(2, 2);
INSERT INTO camere VALUES(1, 3);
INSERT INTO camere VALUES(2, 3);
INSERT INTO camere VALUES(1, 4);
INSERT INTO camere VALUES(2, 4);
INSERT INTO camere VALUES(1, 5);
INSERT INTO camere VALUES(2, 5);

-- Specialization data
INSERT INTO specializari VALUES(1, 'Cardiologie');
INSERT INTO specializari VALUES(2, 'Dermatologie');
INSERT INTO specializari VALUES(3, 'Endocrinologie');
INSERT INTO specializari VALUES(4, 'Neurologie');
INSERT INTO specializari VALUES(5, 'Oftalmologie');

-- medici data
INSERT INTO medici VALUES(1, 'Teodor Paraschiv', 1, 1);
INSERT INTO medici VALUES(2, 'Antoaneta Oprea', 2, 5);
INSERT INTO medici VALUES(3, 'Doru Fodor', 3, 14);
INSERT INTO medici VALUES(4, 'Ina Mihalcea', 4, 15);
INSERT INTO medici VALUES(5, 'Sidonia Banica', 5, 15);

-- spitale_medici data
-- spital medic data

INSERT INTO spitale_medici VALUES(1, 1, SYSDATE, 4000);
INSERT INTO spitale_medici VALUES(2, 2, SYSDATE, 5200);
INSERT INTO spitale_medici VALUES(3, 3, SYSDATE, 3000);
INSERT INTO spitale_medici VALUES(4, 4, SYSDATE, 1300);
INSERT INTO spitale_medici VALUES(5, 5, SYSDATE, 1400);
INSERT INTO spitale_medici VALUES(1, 2, SYSDATE, 2000);
INSERT INTO spitale_medici VALUES(2, 3, SYSDATE, 4000);
INSERT INTO spitale_medici VALUES(3, 4, SYSDATE, 2500);
INSERT INTO spitale_medici VALUES(4, 5, SYSDATE, 3230);
INSERT INTO spitale_medici VALUES(5, 1, SYSDATE, 6000);
    
INSERT INTO utilizatori VALUES(1, '12-Oct-1990', 'Alex', 'Marian', '1111111111', 'al_m1', 'pass', 2);
INSERT INTO utilizatori VALUES(2, '22-Aug-2000', 'Mihai', 'Joe', '2222222222', 'mh_j1', 'pass1', 2);
INSERT INTO utilizatori VALUES(3, '17-Nov-2000', 'Gabriela', 'Balan', '3333333333', 'gb_b2', 'pass2', 1);
INSERT INTO utilizatori VALUES(4, '5-Dec-2002', 'Kyle', 'Brock', '4444444444', 'kl_b0', 'pass3', 4);
INSERT INTO utilizatori VALUES(5, '23-May-2004', 'Alex', 'Nistor', '5555555555', 'al_n1', 'pass4', 3);


INSERT INTO programari VALUES(1, 1, 1, 1, SYSDATE, 30);
INSERT INTO programari VALUES(2, 2, 2, 1, SYSDATE, 30);
INSERT INTO programari VALUES(3, 3, 3, 1, SYSDATE, 30);
INSERT INTO programari VALUES(4, 4, 4, 1, SYSDATE, 30);
INSERT INTO programari VALUES(5, 5, 5, 1, SYSDATE, 30);
INSERT INTO programari VALUES(1, 1, 1, 2, SYSDATE, 30);
INSERT INTO programari VALUES(2, 2, 2, 2, SYSDATE, 30);
INSERT INTO programari VALUES(3, 3, 3, 2, SYSDATE, 30);
INSERT INTO programari VALUES(4, 4, 4, 2, SYSDATE, 30);
INSERT INTO programari VALUES(5, 5, 5, 2, SYSDATE, 30);

--/*6. Formulati in limbaj natural o problema pe care sa o rezolvati folosind un subprogram stocat
--independent care sa utilizeze toate cele 3 tipuri de colectii studiate. Apelati subprogramul.*/

/* Afisarea medicilor dintr-un spital(a salarilor si specializarilor lor*/
CREATE OR REPLACE PROCEDURE raport_medici_din_spital(p_cod_spital IN spitale.cod_spital%TYPE) IS
    TYPE medic_array_t IS VARRAY(50) OF medici.cod_medic%TYPE;
    lista_medici medic_array_t := medic_array_t();

    TYPE tabela_salarii_t IS TABLE OF spitale_medici.salariu%TYPE;
    lista_salarii tabela_salarii_t := tabela_salarii_t();

    TYPE spec_array_t IS TABLE OF specializari.nume_specializare%TYPE INDEX BY PLS_INTEGER;
    lista_spec spec_array_t;
    
    CURSOR m_cursor(s_cod spitale.cod_spital%TYPE) IS
            SELECT m.cod_medic, sm.salariu, s.nume_specializare
            FROM medici m
            JOIN spitale_medici sm ON m.cod_medic = sm.cod_medic
            JOIN specializari s ON m.cod_specializare = s.cod_specializare
            WHERE sm.cod_spital = s_cod;
    
    m_info m_cursor%ROWTYPE;
BEGIN
    OPEN m_cursor(p_cod_spital);
    LOOP
        FETCH m_cursor INTO m_info;
        EXIT WHEN m_cursor%NOTFOUND;
        
        lista_medici.EXTEND;
        lista_medici(lista_medici.COUNT) := m_info.cod_medic;

        lista_salarii.EXTEND;
        lista_salarii(lista_salarii.COUNT) := m_info.salariu;
        
        lista_spec(lista_medici.COUNT) := m_info.nume_specializare;
    END LOOP;

   
    DBMS_OUTPUT.PUT_LINE('Medicii din spitalul cu ID ' || p_cod_spital || ':');
    FOR i IN 1 .. lista_medici.COUNT LOOP
        DBMS_OUTPUT.PUT_LINE('Cod Medic: ' || lista_medici(i) || ', Salariu: ' || lista_salarii(i) || ', Specializare: ' || lista_spec(i));
    END LOOP;
    
    CLOSE m_cursor;
END raport_medici_din_spital;
/
BEGIN
    raport_medici_din_spital(1);
END;
/
/*7. Formulati �n limbaj natural o problema pe care sa o rezolvati folosind un subprogram stocat
independent care sa utilizeze 2 tipuri diferite de cursoare studiate, unul dintre acestea fiind cursor*/

/* Afisarea tuturor spitalelor si a medicilor din acestea*/
CREATE PROCEDURE afis_medici_spitale IS
CURSOR c_spitale IS SELECT cod_spital FROM spitale;
CURSOR c_medici(p_cod_spital spitale.cod_spital%TYPE) IS SELECT m.cod_medic as cod, m.nume_medic as nume, m.cod_specializare as spec,
                                                                m.ani_experienta as ani_exp
                                                         FROM medici m JOIN spitale_medici sm ON sm.cod_medic = m.cod_medic
                                                         WHERE sm.cod_spital = p_cod_spital;

spital_info c_spitale%ROWTYPE;
medic_info c_medici%ROWTYPE;
BEGIN
  OPEN c_spitale;
  LOOP
    FETCH c_spitale INTO spital_info;
    EXIT WHEN c_spitale%NOTFOUND;

    DBMS_OUTPUT.PUT_LINE('Cod spital: ' || spital_info.cod_spital);

    OPEN c_medici(spital_info.cod_spital);
    LOOP
      FETCH c_medici INTO medic_info;
      EXIT WHEN c_medici%NOTFOUND;
      DBMS_OUTPUT.PUT_LINE('Medic: ' || medic_info.cod || ' - ' || medic_info.nume || ' ' || medic_info.spec || ' ' || medic_info.ani_exp);
    END LOOP;
    CLOSE c_medici;
  END LOOP;
  CLOSE c_spitale;
END afis_medici_spitale;
/

BEGIN
    afis_medici_spitale;
END;
/

/*8. Formulati in limbaj natural o problema pe care sa o rezolvati folosind un subprogram stocat
independent de tip functie care si utilizeze �ntr-o singura comanda SQL 3 dintre tabelele create.
Tratati toate exceptiile care pot aparea, incluzand exceptiile predefinite NO_DATA_FOUND ?i
TOO_MANY_ROWS. Apelati subprogramul astfel incat sa evidentiati toate cazurile tratate.*/

/*Afisarea numarului de telefon al spitalului, numelui medicului si a salariului pe care il castiga, cunoscand codul de specializare
si codul spitalului*/
--Pentru a putea testa si too_many_rows
INSERT INTO medici VALUES (6, 'Marius Andrei', 1, 4);
INSERT INTO spitale_medici VALUES(1, 6, SYSDATE, 2700);

CREATE OR REPLACE FUNCTION medic_interesant(spec_id specializari.cod_specializare%TYPE, s_id spitale.cod_spital%TYPE) RETURN VARCHAR2 IS
TYPE medic_info IS RECORD(
    nr_tel_spital spitale.numar_telefon%TYPE,
    nume medici.nume_medic%TYPE,
    salariu spitale_medici.salariu%TYPE);

-- Folosit in timpul prezentarii le cererea profesoarei
-- CURSOR test_cursor(spec_id specializari.cod_specializare%TYPE, s_id spitale.cod_spital%TYPE) IS 
--                    SELECT s.numar_telefon, m.nume_medic, sm.salariu 
--                    FROM medici m
--                    JOIN spitale_medici sm ON sm.cod_medic = m.cod_medic
--                    JOIN spitale s ON s.cod_spital = sm.cod_spital
--                    WHERE m.cod_specializare = spec_id AND s.cod_spital = s_id;

m_info medic_info;

BEGIN
--    OPEN test_cursor(spec_id, s_id);
--   
--    FETCH test_cursor INTO m_info;
--    
--    CLOSE test_cursor;
    SELECT s.numar_telefon, m.nume_medic, sm.salariu INTO m_info
    FROM medici m
    JOIN spitale_medici sm ON sm.cod_medic = m.cod_medic
    JOIN spitale s ON s.cod_spital = sm.cod_spital
    WHERE m.cod_specializare = spec_id AND s.cod_spital = s_id;
    RETURN 'Spitalul ' || s_id || ' are numarul de telefon ' || m_info.nr_tel_spital || ' si pentru specializare ' || spec_id ||
       ' pune la dispozitie medicul ' || m_info.nume || ' cu salariul ' || m_info.salariu;

    EXCEPTION
      WHEN NO_DATA_FOUND THEN
        RETURN 'Medicul interesant nu a fost gasit...';
      WHEN TOO_MANY_ROWS THEN
        RETURN 'Prea multi medici interesanti!!!';
END medic_interesant;
/

BEGIN
    --Output normal
    DBMS_OUTPUT.PUT_LINE('Output Normal');
    DBMS_OUTPUT.PUT_LINE(medic_interesant(2, 2));
    --NO_DATA_FOUND
    DBMS_OUTPUT.PUT_LINE('Output NO_DATA_FOUND');
    DBMS_OUTPUT.PUT_LINE(medic_interesant(100, 1000));
    --TOO_MANY_ROWS
    DBMS_OUTPUT.PUT_LINE('Output TOO_MANY_ROWS');
    DBMS_OUTPUT.PUT_LINE(medic_interesant(1, 1));
END;
/

/*9. Formulati in limbaj natural o problema pe care sa o rezolvati folosind un subprogram stocat
independent de tip procedura care si aiba minim 2 parametri si sa utilizeze intr-o singur?
comanda SQL 5 dintre tabelele create. Definiti minim 2 exceptii proprii, altele dec�t cele
predefinite la nivel de sistem. Apelati subprogramul astfel �ncat sa evidentiati toate cazurile definite
si tratate.*/

/* Afisarea judetului din care face parte spitalul cu numarul de telefon pasat ca parametru impreuna cu numele medicului
ce are numarul de ani de experienta pasati ca parametru, salariul pe care il castiga la spitalul mentionat,
numele utilizatorului care este programat si durata programarii*/

--Pentru cazul TOO_MANY_ROWS
INSERT INTO programari VALUES (1, 4, 5, 1, SYSDATE, 30);

CREATE OR REPLACE PROCEDURE programari_interesante(ani_exp medici.ani_experienta%TYPE, nr_tel_spital spitale.numar_telefon%TYPE) IS
TYPE prog_info IS RECORD(
    nume_judet judete.nume_judet%TYPE,
    nume_utilizator utilizatori.nume_utilizator%TYPE,
    nume_medic medici.nume_medic%TYPE,
    durata programari.durata%TYPE,
    salariu_medic spitale_medici.salariu%TYPE);

p_info prog_info;
tel_invalid Exception;
nr_ani_exp_invalizi Exception;
BEGIN   
    IF ani_exp < 0 OR ani_exp > 50 THEN
        RAISE nr_ani_exp_invalizi;
    END IF;
    
    IF REGEXP_LIKE(nr_tel_spital, '^\d+$') = False THEN
        RAISE tel_invalid;
    END IF;
        
    SELECT DISTINCT j.nume_judet, u.nume_utilizator, m.nume_medic, p.durata, sm.salariu 
    INTO p_info.nume_judet, p_info.nume_utilizator, p_info.nume_medic, p_info.durata, p_info.salariu_medic
    FROM medici m
    JOIN spitale_medici sm ON m.cod_medic = sm.cod_medic
    JOIN spitale s ON sm.cod_spital = s.cod_spital
    JOIN judete j ON j.cod_judet = s.cod_judet
    JOIN programari p ON (p.cod_spital = s.cod_spital AND p.cod_medic = m.cod_medic)
    JOIN utilizatori u ON u.cod_utilizator = p.cod_utilizator
    WHERE m.ani_experienta = ani_exp AND s.numar_telefon = nr_tel_spital;
    
    DBMS_OUTPUT.PUT_LINE('Programarea are o durata de ' || p_info.durata || ' minute, pacientul ' || p_info.nume_utilizator ||
                         ' va fi tratat de ' || p_info.nume_medic || ' , angajat al spitalului din judetul ' || p_info.nume_judet ||
                         ' , ce are un salariu de ' || p_info.salariu_medic);
    EXCEPTION
        WHEN tel_invalid THEN
            DBMS_OUTPUT.PUT_LINE('Numar de telefon invalid!');
        WHEN nr_ani_exp_invalizi THEN
            DBMS_OUTPUT.PUT_LINE('Numarul de ani primit nu este in limitele legale!');
        WHEN NO_DATA_FOUND THEN
            DBMS_OUTPUT.PUT_LINE('Nu exista programari interesante...');
        WHEN TOO_MANY_ROWS THEN
            DBMS_OUTPUT.PUT_LINE('Prea multe programari interesante!!!');
END;
/

BEGIN
    DBMS_OUTPUT.PUT_LINE('Output normal');
    programari_interesante(1, '0218713860');
    
    DBMS_OUTPUT.PUT_LINE('Output NO_DATA_FOUND');
    programari_interesante(25, '0211713863');
    
    DBMS_OUTPUT.PUT_LINE('Output TOO_MANY_ROWS');
    programari_interesante(15, '0255249892');
    
    DBMS_OUTPUT.PUT_LINE('Output telefon invalid');
    programari_interesante(15, 'sus25jos01');
    
    DBMS_OUTPUT.PUT_LINE('Output nr ani experienta invalizi');
    programari_interesante(1000, '0218713860');
END;
/

/*10. Definiti un trigger de tip LMD la nivel de comanda. Declansati trigger-ul.*/
CREATE OR REPLACE TRIGGER maxim_2_m_spec
AFTER INSERT ON spitale_medici

DECLARE 
    CURSOR m_cursor IS SELECT COUNT(cod_medic) nr_medici, cod_spital FROM spitale_medici
                        GROUP BY cod_spital;
    info m_cursor%ROWTYPE;
BEGIN
    OPEN m_cursor;
    
    LOOP
        FETCH m_cursor INTO info;
        EXIT WHEN m_cursor%NOTFOUND;
        IF info.nr_medici = 4 THEN
            RAISE_APPLICATION_ERROR(-20001, 'Prea multi medici la spitalul ' || info.cod_spital);
        END IF;
    END LOOP;
    
    CLOSE m_cursor;
END;
/

SELECT * FROM spitale_medici;
INSERT INTO spitale_medici VALUES(1, 3, '20-DEC-2024', 2500); 
INSERT INTO spitale_medici VALUES(1, 4, '20-DEC-2024', 2500); 


/*11. Definiti un trigger de tip LMD la nivel de linie. Declansati trigger-ul.*/
CREATE OR REPLACE TRIGGER medic_max_prog
BEFORE INSERT OR UPDATE ON programari
FOR EACH ROW
DECLARE
   nr_prog NUMBER;
BEGIN
  SELECT COUNT(*)
  INTO nr_prog
  FROM programari
  WHERE cod_medic = :NEW.cod_medic AND TRUNC(data_programare) = TRUNC(:NEW.data_programare);

  IF nr_prog >= 4 THEN
    RAISE_APPLICATION_ERROR(-20002, 'Medicul acesta si-a atins numarul maxim de programari pentru o zi...');
  END IF;
  
END medic_max_prog;
/

DROP table programari;
INSERT INTO programari VALUES(2, 2, 2, 2, to_date('12-JUN-2022 02:38:25', 'DD-MON-YYYY HH24:MI:SS'), 30); 
INSERT INTO programari VALUES(2, 2, 2, 2, to_date('12-JUN-2022 04:38:25', 'DD-MON-YYYY HH24:MI:SS'), 30); 
INSERT INTO programari VALUES(2, 2, 2, 2, to_date('12-JUN-2022 06:38:25', 'DD-MON-YYYY HH24:MI:SS'), 30); 
INSERT INTO programari VALUES(2, 2, 2, 2, to_date('12-JUN-2022 08:39:25', 'DD-MON-YYYY HH24:MI:SS'), 30);
INSERT INTO programari VALUES(2, 2, 2, 2, to_date('12-JUN-2022 10:39:25', 'DD-MON-YYYY HH24:MI:SS'), 30);
INSERT INTO programari VALUES(1, 1, 1, 1, to_date('12-JUN-2022 12:39:25', 'DD-MON-YYYY HH24:MI:SS'), 30);

/*12. Definiti un trigger de tip LDD. Declansati trigger-ul.*/
CREATE OR REPLACE TRIGGER programari_pe_vecie
AFTER DROP ON SCHEMA
BEGIN
    IF (ORA_DICT_OBJ_TYPE = 'TABLE' AND ORA_DICT_OBJ_NAME = 'PROGRAMARI') THEN
        RAISE_APPLICATION_ERROR(-20001, 'Nu poti sterge tabelul programari...');
    END IF;
END;
/