<?php
    namespace App\Services;

    class DiscountService{
        public function calculateDiscount($price){
            return $price * .30;
        }
    }
?>