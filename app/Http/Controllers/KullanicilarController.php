<?php

namespace App\Http\Controllers;

use App\Models\Izinler;
use App\Models\Roller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KullanicilarController extends Controller
{
    public function index()
    {
        $roller = Roller::all();
        $izinler = Izinler::all();

        // $roller[0]->syncPermissions($izinler);

        // Auth::user()->removeRole('admin');
        // Auth::user()->assignRole('admin');

        return view('kullanicilar', [
            'roller' => $roller,
            'izinler' => $izinler,
        ]);
    }

    public function kullanicilariGetir()
    {
        try
        {
            $kullanicilar = User::paginate(10);

            foreach ($kullanicilar->items() as &$kullanici)
            {
                $kullanici->roller = implode(", ", array_values(array_column($kullanici->roles->toArray(), 'name')));
            }

            return response()->json([
                'durum' => true,
                "mesaj" => "Kullanıcılar başarılı bir şekilde getirildi.",
                'kullanicilar' => $kullanicilar,
            ], 200);
        }
        catch (\Exception $ex)
        {
            return response()->json([
                "durum" => false,
                "mesaj" => "Kullanıcılar getirilirken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
            ], 500);
        }
    }

    public function kullaniciKaydet(Request $request)
    {
        DB::beginTransaction();

        try
        {
            $kullaniciBilgileri = $request->kullanici;

            if (isset($kullaniciBilgileri['id']))
            {
                $kullanici = User::find($kullaniciBilgileri['id']);
            }
            else
            {
                $kullanici = new User();
            }

            $kullanici->name = $this->buyukHarf($kullaniciBilgileri["name"]);
            $kullanici->email = $kullaniciBilgileri["email"];
            $kullanici->password = isset($kullaniciBilgileri["password"]) && $kullaniciBilgileri["password"]
                ? bcrypt($kullaniciBilgileri["password"])
                : $kullanici->password;

            if (!$kullanici->save())
            {
                DB::rollBack();

                return response()->json([
                    "durum" => false,
                    "mesaj" => "Kullanıcı kaydedilirken bir hata oluştu.",
                    "hataKodu" => "KK001",
                ], 500);
            }

            $kullanici->syncRoles(array_column($kullaniciBilgileri["roles"], "name"));

            DB::commit();

            return response()->json([
                "durum" => true,
                "mesaj" => "Kullanıcı kaydedildi.",
            ], 200);
        }
        catch (\Exception $ex)
        {
            DB::rollBack();

            return response()->json([
                "durum" => false,
                "mesaj" => "Kullanıcı kaydedilirken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
            ], 500);
        }
    }

    public function kullaniciSil(Request $request)
    {
        DB::beginTransaction();

        try
        {
            $kullaniciId = $request->id;
            $kullanici = User::find($kullaniciId);

            if (!$kullanici->delete())
            {
                DB::rollBack();

                return response()->json([
                    "durum" => false,
                    "mesaj" => "Kullanıcı silinirken bir hata oluştu.",
                    "hataKodu" => "KS001",
                ], 500);
            }

            DB::commit();

            if (Auth::user()->id == $kullaniciId)
            {
                Auth::logout();
            }

            return response()->json([
                "durum" => true,
                "mesaj" => "Kullanıcı silindi.",
            ], 200);
        }
        catch (\Exception $ex)
        {
            DB::rollBack();

            return response()->json([
                "durum" => false,
                "mesaj" => "Kullanıcı silinirken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
            ], 500);
        }
    }
}
