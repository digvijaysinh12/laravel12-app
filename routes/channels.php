<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin.orders', function($user){
    return $user->role === 'admin';
});