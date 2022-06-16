<?php

namespace App\Http\Controllers;

use App\Models\Izinler;
use App\Models\Roller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
    public function index()
    {
        $izinler = Izinler::all();

        return view("roller", [
            "izinler" => $izinler,
        ]);
    }

    public function rolleriGetir()
    {
        try
        {
            $roller = Roller::paginate(20);

            // dd($roller->toArray());

            foreach ($roller as $rol)
            {
                $rol->permissions = $rol->permissions->pluck('name')->toArray();
            }

            return response()->json([
                'durum' => true,
                "mesaj" => "Roller başarılı bir şekilde getirildi.",
                'roller' => $roller,
            ], 200);
        }
        catch (\Exception $ex)
        {
            return response()->json([
                "durum" => false,
                "mesaj" => "Roller getirilirken bir hata oluştu.",
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

            if (isset($rolBilgileri["id"]))
            {
                $rol = Roller::find($rolBilgileri["id"]);
            }
            else
            {
                $rol = new Roller();
            }

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
                "hataKodu" => "500",
            ], 500);
        }
    }

    public function rolSil(Request $request)
    {
        DB::beginTransaction();

        try
        {
            $rol = Roller::find($request->id);

            if (!$rol->delete())
            {
                DB::rollBack();

                return response()->json([
                    "durum" => false,
                    "mesaj" => "Rol silinirken bir hata oluştu.",
                    "hataKodu" => "RS001",
                ], 500);
            }

            DB::commit();

            return response()->json([
                "durum" => true,
                "mesaj" => "Rol silindi.",
            ], 200);
        }
        catch (\Exception $ex)
        {
            DB::rollBack();

            return response()->json([
                "durum" => false,
                "mesaj" => "Rol silinirken bir hata oluştu.",
                "hata" => $ex->getMessage(),
                "satir" => $ex->getLine(),
                "hataKodu" => "500",
            ], 500);
        }
    }
}
