<?php

namespace App\Listeners;

use App\Models\LogLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Auth;
class LogSuccessfull
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }



    public function handleUserLogin(): void {
        LogLogin::insert(
            [
                "user_id"=>Auth::user()->id,
                "aciklama"=>Auth::user()->name . " Giriş Yaptı",
                "islem_kodu"=>"1",
                "ip"=>\Illuminate\Support\Facades\Request::ip(),
                "created_at"=>now(),
            ]);
    }

    /**
     * Handle user logout events.
     */
    public function handleUserLogout(): void {
        LogLogin::insert(
            [
              "user_id"=>Auth::user()->id,
              "aciklama"=>Auth::user()->name . " Çıkış Yaptı",
              "islem_kodu"=>"0",
              "ip"=>\Illuminate\Support\Facades\Request::ip(),
              "created_at"=>now(),
            ]);
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            Login::class,
            [LogSuccessfull::class, 'handleUserLogin']
        );

        $events->listen(
            Logout::class,
            [LogSuccessfull::class, 'handleUserLogout']
        );
    }
}
