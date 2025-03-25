/*12. Definiti un trigger de tip LDD. Declansati trigger-ul.*/
CREATE OR REPLACE PROCEDURE test_trigger_proc IS
BEGIN
    EXECUTE IMMEDIATE 'DROP TABLE programari';
END;
/

BEGIN
    test_trigger_proc;
END;
/


