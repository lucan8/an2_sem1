/* Cerinta ex 7:
Adaptati cerinta exercitiului 5 pentru diagrama proiectului prezentata la materia Baze de Date din
anul I. Rezolvati acest exercitiu in PL/SQL, folosind baza de date proprie.

Adaptare ex 5:
Sa se creeze un bloc PL/SQL prin care se afiseaza numarul de medici care au venitul anual mai
mare decat o valoare data. Sa se trateze cazul in care niciun medic nu indeplineste aceasta conditie
(exceptii externe).*/

SET VERIFY OFF
SET SERVEROUTPUT ON

/*Getting user input*/
ACCEPT i_m_salary PROMPT 'Please insert a medic anual salary: ';
DECLARE
    v_m_salary hospitals_medics_t.salary%TYPE := &i_m_salary;
    v_medics_count NUMBER;
    v_e EXCEPTION;
BEGIN
    SELECT COUNT(*) INTO v_medics_count
    FROM hospitals_medics_t WHERE salary * 12 > v_m_salary;
    IF v_medics_count = 0 THEN
        RAISE v_e;
    ELSE
        DBMS_OUTPUT.PUT_LINE(v_medics_count || ' medics have an anual salary that is greater than ' || v_m_salary);
    END IF;
EXCEPTION
    WHEN v_e THEN
        DBMS_OUTPUT.PUT_LINE('No medics have an anual salary that is greater than ' || v_m_salary);
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('Other error');
END;
/
SET VERIFY ON
SET SERVEROUTPUT OFF