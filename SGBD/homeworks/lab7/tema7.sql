SELECT * FROM hospitals_medics_t;
SELECT hm.hospital_id, COUNT(m.medic_id), m.specialization_id
FROM hospitals_medics_t hm 
JOIN medics_t m ON m.medic_id = hm.medic_id
GROUP BY hm.hospital_id, m.specialization_id;

/*Ex 7: Adaptati cerinta exercitiului 4 pentru diagrama proiectului prezentata la materia Baze de Date din
anul I. Rezolvati acest exercitiu în PL/SQL, folosind baza de date proprie.
Adaptare 4: Definiti un declansator cu ajutorul caruia sa se implementeze restrictia conform careia intr-un
spital nu pot lucra mai mult de 4 medici cu aceeasi specializare
*/
CREATE OR REPLACE TRIGGER max4_hosp_med
AFTER INSERT OR UPDATE OF hospital_id, medic_id ON hospitals_medics_t
DECLARE 
nr_medics NUMBER;
BEGIN
    FOR rec IN (SELECT hm.hospital_id as h_id, COUNT(m.medic_id) as nr_med, m.specialization_id as spec
                FROM hospitals_medics_t hm 
                JOIN medics_t m ON m.medic_id = hm.medic_id
                GROUP BY hm.hospital_id, m.specialization_id) LOOP
        IF rec.nr_med > 3 THEN
            RAISE_APPLICATION_ERROR(-20001, 'Maximum number of medics working at the same hospital (' || rec.h_id || ') with the same specialization(' || rec.spec || ') exceeded');
        END IF;
    END LOOP;
END;
/

UPDATE hospitals_medics_t SET hospital_id = 1 WHERE hospital_id = 2 AND medic_id = 24;
