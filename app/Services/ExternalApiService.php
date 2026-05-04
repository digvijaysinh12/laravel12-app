<?php

namespace App\Services;

use App\Exceptions\ExternalApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalApiService
{
    protected function client()
    {
        return Http::baseUrl(config('services.fake_store.base_url'))
            ->withToken(config('services.fake_store.token'))
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->timeout(5)
            ->retry(3, 200);
    }

    public function getProducts()
    {
        try {
            return $this->client()
                ->get('/products')
                ->throw()
                ->throwIf(fn ($res) => $res->json('status') === 'error');

        } catch (RequestException $e) {

            Log::channel('products')->error('API response error', [
                'message' => $e->getMessage(),
            ]);

            throw new ExternalApiException('API responded with error');
        } catch (ConnectionException $e) {

            Log::channel('products')->error('API connection failed', [
                'message' => $e->getMessage(),
            ]);

            throw new ExternalApiException('Unable to connect to external service');
        }
    }
}
