<?php

namespace App\Http\Controllers;

use App\Services\HomepageService;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function index(HomepageService $service)
    {   
        $data = $service->getHomePageData();

        return view('home.index',$data);
    }
}
