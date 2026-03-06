<?php

namespace App\Http\Controllers;

use App\Services\DiscountService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function discount(){
        $dsc = new DiscountService();
        $d = $dsc->calculateDiscount(1000);

        return "Discount is: ". $d;
    }
}
