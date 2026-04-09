<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function notFound()
    {
        return response()->view('errors.404', [], 404);
    }
}
