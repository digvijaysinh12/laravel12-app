<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'subject',
        'message',
        'customer_name',
        'customer_email',
        'priority',
        'status',
        'assigned_to',
        'slack_thread_ts',
    ];
}
