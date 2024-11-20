<?php
    require_once 'Entity.php';
    class MedicsData extends EntityData{
        public int $medic_id;
        public string $medic_name;
        public int $specialization_id;
        public int $years_exp;

        function __construct(int $medic_id, string $medic_name, int $specialization_id, int $years_exp){
            $this->medic_id = $medic_id;
            $this->medic_name = $medic_name;
            $this->specialization_id = $specialization_id;
            $this->years_exp = $years_exp;
        }
    }

    class Medics extends Entity{
        public static function getMedicsBySpec($spec_id): array{
            $query = "SELECT * FROM " . static::class . " WHERE specialization_id = ?";
            self::printQuery($query, [$spec_id]);

            $stm = self :: $conn->prepare($query);
            $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");

            $stm->execute([$spec_id]);
            return $stm->fetchAll();
            //return [new MedicsData(1, "Dr. John Doe", 1, 5), new MedicsData(2, "Dr. Jane Doe", 2, 10), new MedicsData(3, "Dr. James Doe", 3, 15), new MedicsData(4, "Dr. Janet Doe", 4, 20)];
        }
    }
?>