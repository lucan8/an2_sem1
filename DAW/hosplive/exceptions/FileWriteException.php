<?php
class FileWriteException extends Exception{
    private $file_name;
    public function __construct($file_name){
        $this->file_name = $file_name;
        parent::__construct("Could not write to file: " . $this->file_name);
    }
}
?>