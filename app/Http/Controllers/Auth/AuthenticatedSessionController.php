<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show login form
     */
    public function create(): View
    {
        return view('user.auth.login');
    }

    /**
     * Handle login
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $this->restoreCartFromRedis();


        $route = $request->user()?->role === 'admin'
            ? route('admin.dashboard')
            : route('dashboard');

        return redirect($route);
    }

    /**
     * Handle logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (auth()->check()) {
            $this->storeCartInRedis();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Restore cart from Redis → Session
     */
    private function restoreCartFromRedis(): void
    {
        try {
            $key = 'cart:user_' . auth()->id();

            $saved = Redis::get($key);

            if ($saved) {
                session()->put('cart', json_decode($saved, true));
                Redis::del($key);
            }

        } catch (\Exception $e) {
            Log::warning('Redis restore failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Store cart in Redis on logout
     */
    private function storeCartInRedis(): void
    {
        try {
            $cart = session()->get('cart', []);

            if (!empty($cart)) {
                $userId = auth()->id();

                Redis::setex(
                    'cart:user_' . $userId,
                    60 * 60 * 24 * 30, // 30 days
                    json_encode($cart)
                );

                Redis::sadd('cart_users', $userId);
            }

        } catch (\Exception $e) {
            Log::warning('Redis store failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
        }
    }
}
