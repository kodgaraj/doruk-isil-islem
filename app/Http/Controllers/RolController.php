<?php

namespace App\Http\Controllers;

use App\Models\Roller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
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
                "hataKodu" => "500",
            ], 500);
        }
    }

    public function rolKaydet(Request $request)
    {
        DB::beginTransaction();

        try
        {
            $rolBilgileri = $request->rol;

            // $rol = Roller::create([
            //     'name' => $rolBilgileri['name'],
            //     'slug' => $this->buyukHarf($rolBilgileri['slug']),
            //     'description' => $rolBilgileri['description'] ?? null,
            //     'guard_name' => 'web',
            // ]);

            $rol = new Roller();

            $rol->name = $rolBilgileri['name'];
            $rol->slug = $this->buyukHarf($rolBilgileri['slug']);
            $rol->description = $rolBilgileri['description'] ?? null;
            $rol->guard_name = 'web';

            if (!$rol->save())
            {
                DB::rollBack();

                return response()->json([
                    "durum" => false,
                    "mesaj" => "Rol kaydedilirken bir hata oluştu.",
                    "hataKodu" => "RK001",
                ], 500);
            }

            $rol->syncPermissions(array_column($rolBilgileri['permissions'], "name"));

            DB::commit();

            return response()->json([
                "durum" => true,
                "mesaj" => "Rol kaydedildi.",
                "rol" => $rol,
            ], 200);
        }
        catch (\Exception $ex)
        {
            DB::rollBack();

            return response()->json([
                "durum" => false,
                "mesaj" => "Rol kaydedilirken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
            ], 500);
        }
    }
}
