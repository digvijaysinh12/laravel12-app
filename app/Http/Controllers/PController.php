<?php

namespace App\Http\Controllers;

use App\Exceptions\ExternalApiException;
use App\Services\ExternalApiService;

class PController extends Controller
{
    protected $api;

    public function __construct(ExternalApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        try {
            $response = $this->api->getProducts();
            $products = $response->collect();

        } catch (ExternalApiException $e) {

            return view('api', [
                'products' => collect(),
            ])->withErrors($e->getMessage());
        }

        return view('api', compact('products'));
    }
}
