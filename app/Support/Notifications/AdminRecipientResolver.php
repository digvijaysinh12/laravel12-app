<?php

namespace App\Support\Notifications;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AdminRecipientResolver
{
    public function getAdmins(): Collection
    {
        return User::query()
            ->where('role', 'admin')
            ->orderBy('id')
            ->get();
    }
}
