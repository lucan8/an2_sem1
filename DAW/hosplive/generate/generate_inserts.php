<?php
    require_once "../exceptions/FileOpenException.php";
    require_once "../exceptions/FileWriteException.php";
    $tables = [
        "rooms", "counties", "medics",
        "hospitals", "hospitals_medics", "specializations"
      ];
    generateWriteInserts($tables);
    
    //Generates and writes the insert queries for the tables in $tables
    //Each table will have different script file generated
    function generateWriteInserts(array $tables){
        $RESOURCE_DIR = "../resources/";
        $MIGRATIONS_DIR = "../app/migrations/";
        
        foreach ($tables as $table){
                //Opening input file
                $file_in_name = $RESOURCE_DIR . $table . ".txt";
                $file_in = fopen($file_in_name, "r");
                if (!$file_in)
                    throw new FileOpenException($file_in_name);

                //Creating(or opening) the sql script file for writing
                $file_out_name = $MIGRATIONS_DIR . $table . "_insert.sql";
                $file_out = fopen($file_out_name, "w");
                if (!$file_out)
                    throw new FileOpenException($file_out_name);

                # Generating and writing the insert script + the insert migration script
                generateWriteInsert($table, $file_in, $file_out);
                fwrite($file_out, "INSERT INTO migrations(migration_name) VALUES('" . $table . "_insert.sql');\n");

                fclose($file_in);
                fclose($file_out);
        }
    }

    //Generates and writes the insert queries for the table $table
    function generateWriteInsert($table, $file_in, $file_out){
        //Ignoring header
        $columns = fgets($file_in);

        while (!feof($file_in)){
            $columns_values = fgets($file_in);
            if (!$columns_values)
                break;
            
            $query = "INSERT INTO " . $table . " VALUES(";
            $query .= parseColumnsValues($columns_values) . ");\n";

            if (!fwrite($file_out, $query))
                throw new FileWriteException(stream_get_meta_data($file_out)["uri"]);
        }
    }

    //Parses string of column values and adds quotes to string values
    function parseColumnsValues(string $columns_values){
        $parsed_columns_values = "";
        //Adding quotes to columns which hold strings
        foreach (explode(", ", $columns_values) as $column_value) {
            $column_value = rtrim($column_value);
            if (!is_numeric($column_value) || isPhoneNumber($column_value))
                $parsed_columns_values .= "'" . $column_value . "', ";
            else
                $parsed_columns_values .= $column_value . ", ";
        }
        //Removing the last comma and space
        return rtrim($parsed_columns_values, ", ");
    }

    function isPhoneNumber(string $column_value){
        return $column_value[0] == "0" && strlen($column_value) == 10 && is_numeric($column_value);
    }
?>
