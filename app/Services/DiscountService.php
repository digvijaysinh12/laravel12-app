<?php
    namespace App\Services;

    use Illuminate\Support\Facades\Log;

    class DiscountService{
        public function calculateDiscount($price, $userType = 'regular'){

            Log::info('DiscountService called', [
                'price' => $price,
                'userType' => $userType
            ]);

            if($userType==='premium'){
                return $price*.20; // 20% discount
            }

            if($price>5000){
                return $price * .15;
            }
            
            return $price * .05;
        }
    }
?>