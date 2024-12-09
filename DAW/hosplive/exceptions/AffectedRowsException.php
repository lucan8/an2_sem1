<?php
class AffectedRowsException extends Exception{
    private $affected_rows;
    private $expected_affected_rows;
    public function __construct($affected_rows, $expected_affected_rows){
        $this->affected_rows = $affected_rows;
        $this->expected_affected_rows = $expected_affected_rows;
        parent::__construct("Affected rows: " . $this->affected_rows . " Expected affected rows: " . $this->expected_affected_rows);
    }
}
?>