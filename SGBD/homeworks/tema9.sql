CREATE OR REPLACE PACKAGE salary_package AS 

    curr_salary_sum hospitals_medics_t.salary%TYPE; 

END salary_package; 

/ 

CREATE OR REPLACE TRIGGER get_sal_sum 

BEFORE UPDATE OF salary ON hospitals_medics_t  

BEGIN 

    SELECT sum(salary) INTO salary_package.curr_salary_sum FROM hospitals_medics_t; 

END; 

/ 

  

CREATE OR REPLACE TRIGGER get_sal_sum_for_each 

BEFORE UPDATE OF salary ON hospitals_medics_t  

FOR EACH ROW 

BEGIN 

    salary_package.curr_salary_sum := salary_package.curr_salary_sum + (:new.salary - :old.salary);  

    DBMS_OUTPUT.PUT_LINE(salary_package.curr_salary_sum); 

END; 

/ 

UPDATE hospitals_medics_t SET salary = 2200 WHERE medic_id > 2