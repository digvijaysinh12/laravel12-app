<?php

namespace App\Services\Reports;

use App\Models\User;

class CustomerReportService
{
    public function getData()
    {
        return User::withCount('orders')->get()->map(function($user){
            return[
                $user->name,
                $user->orders_count
            ];
        })->toArray();
    }
}

