<?php
    require_once 'exceptions/FileOpenException.php';
    require_once 'constants.php';

    function getNrLines($file_name){
        $file = fopen($file_name, "r");
        if (!$file)
            throw new FileOpenException($file_name);

        $nr_lines = 0;
        while (!feof($file)){
            $line = fgets($file);
            $nr_lines++;
        }
        fclose($file);
        return $nr_lines;
    }

    function getHoursAndMinutes(string $time): string{
        return substr($time, 0, strlen(Constants :: TIME_FORMAT));
    }
?>