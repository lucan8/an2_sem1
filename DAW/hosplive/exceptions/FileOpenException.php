<?php
class FileOpenException extends Exception{
    private $file_name;
    public function __construct($file_name){
        $this->file_name = $file_name;
        parent::__construct("Could not open file: " . $this->file_name);
    }
}
?>