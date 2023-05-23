<?php

namespace App\Http\Middleware;

use App\Models\Kisitlar;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Kontrol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();
        $user = Auth::user();
        $saat = date("H:i");
        $ip = $request->ip();

        $kisitlar = Kisitlar::get()->first();
        if (isset($kisitlar) && $kisitlar != null) {

            $saatBaslangic = $kisitlar->saatBaslangic;
            $saatBitis = $kisitlar->saatBitis;
            $kullanicilar = isset($kisitlar->kullanicilar) && $kisitlar->kullanicilar != null ? json_decode($kisitlar->kullanicilar) : [];
            $roller = isset($kisitlar->roller) && $kisitlar->roller != null ? json_decode($kisitlar->roller) : [];
            $ipler =  isset($kisitlar->ipler) && $kisitlar->ipler != null ? $kisitlar->ipler : [];
            $ipRanges = explode(',', $ipler);
            $ipAddresses = [];

            foreach ($ipRanges as $ipRange) {
                if (strpos($ipRange, '-') !== false) {
                    // IP aralığını işle
                    $range = explode('-', $ipRange);
                    $startIP = ip2long(trim($range[0]));
                    $endIP = ip2long(trim($range[1]));

                    for ($i = $startIP; $i <= $endIP; $i++) {
                        $ipAddresses[] = long2ip($i);
                    }
                } else {
                    // Tek IP adresini işle
                    $ipAddresses[] = trim($ipRange);
                }
            }

            foreach($kullanicilar as $kullanici){
                if($kullanici->id == $request->user()->id){
                    return $next($request);
                }
            }
            foreach($roller as $rol){
                if($user->hasRole($rol->name)){
                    return $next($request);
                }
            }

            if (!in_array($ip, $ipAddresses)) {
                return redirect()->route("logout");
            }

            if(isset($saatBaslangic) && isset($saatBitis) && $saatBaslangic != null && $saatBitis != null){
                if ($saat >= $saatBaslangic && $saat <= $saatBitis) {
                    return redirect()->route("logout");
                }
            }

        }
        return $next($request);
    }
}
