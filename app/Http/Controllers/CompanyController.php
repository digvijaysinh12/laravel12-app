<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanyController extends Controller
{
        public function showCompany(){
        $name = config('company.name');
        $email = config('company.email');

        return $name . " - " . $email;
    }
}
