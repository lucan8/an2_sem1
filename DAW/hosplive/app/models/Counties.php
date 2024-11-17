<?php
    require_once 'Entity.php';
    class CountiesData extends EntityData{
        public int $county_id;
        public string $county_name;

        function __construct(int $county_id, string $county_name){
            $this->county_id = $county_id;
            $this->county_name = $county_name;
        }
    }

    class Counties extends Entity{
        // public static function getCounties(){
        //     return [new CountiesData(1, "Nairobi"), new CountiesData(2, "Mombasa"), new CountiesData(3, "Kisumu"), new CountiesData(4, "Nakuru")];
        // } 
    }
?>