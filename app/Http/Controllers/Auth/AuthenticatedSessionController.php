<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::Anasayfa);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $request->ajax()
            ? response()->json([
                "durum" => true,
                "url" => route('login'),
            ])
            : redirect('/');
    }

    public function jwtLogin(Request $request)
    {
        $jwt = $request->jwt;

        if (!$jwt) {
            return response()->json([
                'durum' => false,
                'mesaj' => 'JWT bulunamadı',
            ]);
        }

        try
        {
            $kullanici = JWT::decode($jwt, config('app.jwt.secret'), ['HS256']);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'durum' => false,
                'mesaj' => 'JWT hatası',
                "hataKodu" => "JWT_HATASI",
            ]);
        }

        $kullaniciBilgileri = User::find($kullanici->id);

        if (!$kullaniciBilgileri) {
            return response()->json([
                'durum' => false,
                'mesaj' => 'Kullanıcı bulunamadı',
            ]);
        }

        auth()->login($kullaniciBilgileri);

        $request->session()->regenerate();

        return $request->ajax()
            ? response()->json([
                'durum' => true,
                'mesaj' => 'Giriş başarılı',
                'kullanici' => $kullanici,
            ])
            : redirect()->intended(RouteServiceProvider::Anasayfa);
    }
}
