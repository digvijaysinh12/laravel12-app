<?php
    namespace App\Services;

    class PaymentService
    {
        public function processPayment($amount){
            return "Payment of Rs. " . $amount. " process success.";
        }
    }
?>