<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'customer_name' => ['required', 'string'],
            'customer_email' => ['required', 'email'],
            'priority' => ['required', 'in:low,medium,high'],
        ]);

        $ticket = SupportTicket::create($validated);

        return response()->json([
            'message' => 'Support ticket created successfully.',
            'ticket' => $ticket,
        ]);
    }
}
