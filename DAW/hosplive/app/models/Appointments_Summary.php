<?php
require_once "Entity.php";
class Appointments_SummaryData extends EntityData{
    public int|null $appointment_id;
    public string|null $patient_reason;
    public string|null $symptoms;
    public string|null $diagnosis;
    public string|null $treatment;
    public string|null $other_observations;

    public function __construct(int $appointment_id = null, string $patient_reason = null, string $symptoms = null,
                                string $diagnosis = null, string $treatment = null, string $other_observations = null){
        $this->appointment_id = $appointment_id;
        $this->patient_reason = $patient_reason;
        $this->symptoms = $symptoms;
        $this->diagnosis = $diagnosis;
        $this->treatment = $treatment;
        $this->other_observations = $other_observations;
    }
} 

class Appointments_Summary extends Entity{
    public static function getIdColumn(): string{
        return "appointment_id";
    }

    public static function getNeccessaryRows(): array{
        return array_keys(get_class_vars(Appointments_SummaryData::class));
    }
}

?>