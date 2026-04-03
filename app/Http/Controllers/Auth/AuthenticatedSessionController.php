<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $key = 'cart:user_' . auth()->id();
        $saved = Redis::get($key);

        if($saved){
            session()->put('cart', json_decode($saved, true));
            Redis::del($key); // delete from the redis and its back in session now
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if(auth()->check()){

            $cart = session()->get('cart',[]);

            if(!empty($cart)){

                $userId = auth()->id();

                Redis::setex(
                    'cart:user_' . auth()->id(),
                    60*60*24*30,// 30 Days
                    json_encode($cart)
                );

                Redis::sadd('cart_users',$userId);
            }


        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
