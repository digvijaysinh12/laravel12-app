<?php

namespace App\Http\Controllers;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    public function showCompany()
    {

        Log::info('Company config accessed');

        return response()->json([
            'name' => config('company.name'),
            'email' => config('company.email')
        ]);
    }
}
