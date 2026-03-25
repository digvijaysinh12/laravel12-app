<?php
    namespace App\Services;

    use Exception;
    use Illuminate\Support\Facades\Log;

    class PaymentService
    {
        public function processPayment($amount){

            Log::info('PayementService processing',[
                'amount' => $amount
            ]);

            if($amount<=0){
                throw new Exception("Invalid payment amount");
            }

            return [
                'status' => true,
                'message' => "Payement of Rs. $amount successful"
            ];
        }
    }
?>