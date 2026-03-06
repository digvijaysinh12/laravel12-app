<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $ps;

    public function __construct(PaymentService $ps){
        $this->ps = $ps;
    }

    public function pay(){
        return $this->ps->processPayment(500);
    }
}
