<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\PaymentService;

class PaymentController extends Controller
{
    protected $ps;

    public function __construct(PaymentService $ps)
    {
        $this->ps = $ps;
    }

    public function pay()
    {
        $result = $this->ps->processPayment(500);

        return $result;
    }
}
