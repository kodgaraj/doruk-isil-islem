<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    // ROLLER
    // admin - web - ADMİN
    // personel - web - PERSONEL
    // muhendis - web - MÜHENDİS
    // usta-basi - web - USTA BAŞI
    // saha-elemani - web - SAHA ELEMANI
    // muhasebeci - web - MUHASEBECİ

    // İZİNLER
    // siparis_ucreti_goruntuleme	web	Ücret görüntüleme
    // siparis_kaydetme	web	Sipariş kaydetme
    // siparis_duzenleme	web	Sipariş düzenleme
    // siparis_silme	web	Sipariş silme
    // siparis_listeleme	web	Sipariş listeleme
    // isil_islem_formu_kaydetme	web	Isıl işlem formu kaydetme
    // isil_islem_formu_duzenleme	web	Isıl işlem formu düzenleme
    // isil_islem_formu_silme	web	Isıl işlem formu silme
    // isil_islem_formu_listeleme	web	Isıl işlem formu listeleme
    // yonetim_menusu	web	Yönetim menüsü görme
    // kullanici_kaydetme	web	Kullanıcı kaydetme
    // kullanici_duzenleme	web	Kullanıcı düzenleme
    // kullanici_silme	web	Kullanıcı silme
    // kullanici_listeleme	web	Kullanıcı listeleme
    // rol_kaydetme	web	Rol kaydetme
    // rol_duzenleme	web	Rol düzenleme
    // rol_silme	web	Rol silme
    // rol_listeleme	web	Rol listeleme
    // isil_islem_kaydetme	web	Isıl işlem kaydetme
    // isil_islem_duzenleme	web	Isıl işlem düzenleme
    // isil_islem_silme	web	Isıl işlem silme
    // isil_islem_listeleme	web	Isıl işlem listeleme
    // firma_kaydetme	web	Firma kaydetme
    // firma_duzenleme	web	Firma düzenleme
    // firma_silme	web	Firma silme
    // firma_listeleme	web	Firma listeleme
    // malzeme_kaydetme	web	Malzeme kaydetme
    // malzeme_duzenleme	web	Malzeme düzenleme
    // malzeme_silme	web	Malzeme silme
    // malzeme_listeleme	web	Malzeme listeleme
    // islem_turu_kaydetme	web	İşlem türü kaydetme
    // islem_turu_duzenleme	web	İşlem türü düzenleme
    // islem_turu_silme	web	İşlem türü silme
    // islem_turu_listeleme	web	İşlem türü listeleme

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /** İzinler */
            $izinler = [
                [
                    'name' => 'siparis_ucreti_goruntuleme',
                    'guard_name' => 'web',
                    'slug' => 'Ücret görüntüleme'
                ],
                [
                    'name' => 'siparis_kaydetme',
                    'guard_name' => 'web',
                    'slug' => 'Sipariş kaydetme'
                ],
                [
                    'name' => 'siparis_duzenleme',
                    'guard_name' => 'web',
                    'slug' => 'Sipariş düzenleme'
                ],
                [
                    'name' => 'siparis_silme',
                    'guard_name' => 'web',
                    'slug' => 'Sipariş silme'
                ],
                [
                    'name' => 'siparis_listeleme',
                    'guard_name' => 'web',
                    'slug' => 'Sipariş listeleme'
                ],
                [
                    'name' => 'isil_islem_formu_kaydetme',
                    'guard_name' => 'web',
                    'slug' => 'Isıl işlem formu kaydetme'
                ],
                [
                    'name' => 'isil_islem_formu_duzenleme',
                    'guard_name' => 'web',
                    'slug' => 'Isıl işlem formu düzenleme'
                ],
                [
                    'name' => 'isil_islem_formu_silme',
                    'guard_name' => 'web',
                    'slug' => 'Isıl işlem formu silme'
                ],
                [
                    'name' => 'isil_islem_formu_listeleme',
                    'guard_name' => 'web',
                    'slug' => 'Isıl işlem formu listeleme'
                ],
                [
                    'name' => 'yonetim_menusu',
                    'guard_name' => 'web',
                    'slug' => 'Yönetim menüsü'
                ],
                [
                    'name' => 'kullanici_kaydetme',
                    'guard_name' => 'web',
                    'slug' => 'Kullanıcı kaydetme'
                ],
                [
                    'name' => 'kullanici_duzenleme',
                    'guard_name' => 'web',
                    'slug' => 'Kullanıcı düzenleme'
                ],
                [
                    'name' => 'kullanici_silme',
                    'guard_name' => 'web',
                    'slug' => 'Kullanıcı silme'
                ],
                [
                    'name' => 'kullanici_listeleme',
                    'guard_name' => 'web',
                    'slug' => 'Kullanıcı listeleme'
                ],
                [
                    'name' => 'rol_kaydetme',
                    'guard_name' => 'web',
                    'slug' => 'Rol kaydetme'
                ],
                [
                    'name' => 'rol_duzenleme',
                    'guard_name' => 'web',
                    'slug' => 'Rol düzenleme'
                ],
                [
                    'name' => 'rol_silme',
                    'guard_name' => 'web',
                    'slug' => 'Rol silme'
                ],
                [
                    'name' => 'rol_listeleme',
                    'guard_name' => 'web',
                    'slug' => 'Rol listeleme'
                ],
                [
                    'name' => 'isil_islem_kaydetme',
                    'guard_name' => 'web',
                    'slug' => 'Isıl işlem kaydetme'
                ],
                [
                    'name' => 'isil_islem_duzenleme',
                    'guard_name' => 'web',
                    'slug' => 'Isıl işlem düzenleme'
                ],
                [
                    'name' => 'isil_islem_silme',
                    'guard_name' => 'web',
                    'slug' => 'Isıl işlem silme'
                ],
                [
                    'name' => 'isil_islem_listeleme',
                    'guard_name' => 'web',
                    'slug' => 'Isıl işlem listeleme'
                ],
                [
                    'name' => 'firma_kaydetme',
                    'guard_name' => 'web',
                    'slug' => 'Firma kaydetme'
                ],
                [
                    'name' => 'firma_duzenleme',
                    'guard_name' => 'web',
                    'slug' => 'Firma düzenleme'
                ],
                [
                    'name' => 'firma_silme',
                    'guard_name' => 'web',
                    'slug' => 'Firma silme'
                ],
                [
                    'name' => 'firma_listeleme',
                    'guard_name' => 'web',
                    'slug' => 'Firma listeleme'
                ],
                [
                    'name' => 'malzeme_kaydetme',
                    'guard_name' => 'web',
                    'slug' => 'Malzeme kaydetme'
                ],
                [
                    'name' => 'malzeme_duzenleme',
                    'guard_name' => 'web',
                    'slug' => 'Malzeme düzenleme'
                ],
                [
                    'name' => 'malzeme_silme',
                    'guard_name' => 'web',
                    'slug' => 'Malzeme silme'
                ],
                [
                    'name' => 'malzeme_listeleme',
                    'guard_name' => 'web',
                    'slug' => 'Malzeme listeleme'
                ],
                [
                    'name' => 'islem_turu_kaydetme',
                    'guard_name' => 'web',
                    'slug' => 'İşlem türü kaydetme'
                ],
                [
                    'name' => 'islem_turu_duzenleme',
                    'guard_name' => 'web',
                    'slug' => 'İşlem türü düzenleme'
                ],
                [
                    'name' => 'islem_turu_silme',
                    'guard_name' => 'web',
                    'slug' => 'İşlem türü silme'
                ],
                [
                    'name' => 'islem_turu_listeleme',
                    'guard_name' => 'web',
                    'slug' => 'İşlem türü listeleme'
                ],
                [
                    'name' => 'rapor_listeleme',
                    'guard_name' => 'web',
                    'slug' => 'Rapor listeleme'
                ],
                [
                    'name' => 'log_listeleme',
                    'guard_name' => 'web',
                    'slug' => 'Log kayıtları listeleme'
                ],
                [
                    'name' => 'firin_kaydetme',
                    'guard_name' => 'web',
                    'slug' => 'Fırın kaydetme'
                ],
                [
                    'name' => 'firin_duzenleme',
                    'guard_name' => 'web',
                    'slug' => 'Fırın düzenleme'
                ],
                [
                    'name' => 'firin_silme',
                    'guard_name' => 'web',
                    'slug' => 'Fırın silme'
                ],
                [
                    'name' => 'firin_listeleme',
                    'guard_name' => 'web',
                    'slug' => 'Fırın listeleme'
                ],
            ];

            foreach ($izinler as $izin)
            {
                if (!Permission::where('name', $izin["name"])->exists())
                {
                    Permission::create($izin);
                }
            }
        /** # İzinler # */

        /** Rol İzinleri */
            // create roles and assign created permissions
            $roller = [
                [
                    'name' => 'admin',
                    'guard_name' => 'web',
                    'slug' => 'ADMİN',
                    'description' => 'Sistem yöneticisi',
                    'izinler' => Permission::all()->pluck('name')->toArray()
                ],
                [
                    'name' => 'personel',
                    'guard_name' => 'web',
                    'slug' => 'PERSONEL',
                    'description' => 'Personel',
                    'izinler' => [
                        'siparis_kaydetme',
                        'siparis_duzenleme',
                        'siparis_listeleme',
                        'isil_islem_formu_kaydetme',
                        'isil_islem_formu_duzenleme',
                        'isil_islem_formu_listeleme',
                        'isil_islem_kaydetme',
                        'isil_islem_duzenleme',
                        'isil_islem_listeleme',
                        'islem_turu_kaydetme',
                        'islem_turu_duzenleme',
                        'islem_turu_listeleme',
                        'firma_kaydetme',
                        'firma_duzenleme',
                        'firma_listeleme',
                        'malzeme_kaydetme',
                        'malzeme_duzenleme',
                        'malzeme_listeleme',
                        'islem_turu_kaydetme',
                        'islem_turu_duzenleme',
                        'islem_turu_listeleme',
                        'rapor_listeleme',
                    ],
                ],
                [
                    'name' => 'muhendis',
                    'guard_name' => 'web',
                    'slug' => 'MÜHENDİS',
                    'description' => 'Mühendis',
                    'izinler' => [
                        'siparis_kaydetme',
                        'siparis_duzenleme',
                        'siparis_silme',
                        'siparis_listeleme',
                        'isil_islem_formu_kaydetme',
                        'isil_islem_formu_duzenleme',
                        'isil_islem_formu_silme',
                        'isil_islem_formu_listeleme',
                        'isil_islem_kaydetme',
                        'isil_islem_duzenleme',
                        'isil_islem_silme',
                        'isil_islem_listeleme',
                        'islem_turu_kaydetme',
                        'islem_turu_duzenleme',
                        'islem_turu_silme',
                        'islem_turu_listeleme',
                        'firma_kaydetme',
                        'firma_duzenleme',
                        'firma_silme',
                        'firma_listeleme',
                        'malzeme_kaydetme',
                        'malzeme_duzenleme',
                        'malzeme_silme',
                        'malzeme_listeleme',
                        'islem_turu_kaydetme',
                        'islem_turu_duzenleme',
                        'islem_turu_silme',
                        'islem_turu_listeleme',
                        'rapor_listeleme',
                    ],
                ],
                [
                    'name' => 'usta_basi',
                    'guard_name' => 'web',
                    'slug' => 'USTA BAŞI',
                    'description' => 'Usta başı',
                    'izinler' => [
                        'siparis_kaydetme',
                        'siparis_duzenleme',
                        'siparis_listeleme',
                        'isil_islem_formu_kaydetme',
                        'isil_islem_formu_duzenleme',
                        'isil_islem_formu_listeleme',
                        'isil_islem_kaydetme',
                        'isil_islem_duzenleme',
                        'isil_islem_listeleme',
                        'islem_turu_kaydetme',
                        'islem_turu_duzenleme',
                        'islem_turu_listeleme',
                        'firma_kaydetme',
                        'firma_duzenleme',
                        'firma_listeleme',
                        'malzeme_kaydetme',
                        'malzeme_duzenleme',
                        'malzeme_listeleme',
                        'islem_turu_kaydetme',
                        'islem_turu_duzenleme',
                        'islem_turu_listeleme',
                        'rapor_listeleme',
                    ],
                ],
                [
                    'name' => 'saha_elemani',
                    'guard_name' => 'web',
                    'slug' => 'SAHA ELEMANI',
                    'description' => 'Saha elemanı',
                    'izinler' => [
                        'siparis_kaydetme',
                        'siparis_duzenleme',
                        'siparis_listeleme',
                        'isil_islem_formu_kaydetme',
                        'isil_islem_formu_duzenleme',
                        'isil_islem_formu_listeleme',
                        'isil_islem_kaydetme',
                        'isil_islem_duzenleme',
                        'isil_islem_listeleme',
                        'islem_turu_kaydetme',
                        'islem_turu_duzenleme',
                        'islem_turu_listeleme',
                        'firma_kaydetme',
                        'firma_duzenleme',
                        'firma_listeleme',
                        'malzeme_kaydetme',
                        'malzeme_duzenleme',
                        'malzeme_listeleme',
                        'islem_turu_kaydetme',
                        'islem_turu_duzenleme',
                        'islem_turu_listeleme',
                        'rapor_listeleme',
                    ],
                ],
                [
                    'name' => 'muhasebeci',
                    'guard_name' => 'web',
                    'slug' => 'MUHASEBECİ',
                    'description' => 'Muhasebeci',
                    'izinler' => [
                        'siparis_listeleme',
                        'isil_islem_formu_listeleme',
                        'isil_islem_listeleme',
                        'islem_turu_listeleme',
                        'firma_listeleme',
                        'malzeme_listeleme',
                        'islem_turu_listeleme',
                        'rapor_listeleme',
                        'siparis_ucreti_goruntuleme',
                    ],
                ],
            ];

            foreach ($roller as $rol)
            {
                $rolIzinleri = $rol["izinler"];

                $rolBilgisi = Role::where('name', $rol["name"])->first();

                if (!$rolBilgisi)
                {
                    unset($rol["izinler"]);
                    $rolBilgisi = Role::create($rol);
                }

                $rolBilgisi->syncPermissions($rolIzinleri);
            }
        /** # Rol İzinleri */

        // admin kullanıcısına rol atama
        $user = User::where('email', 'admin')->first();
        $user->assignRole('admin');
    }
}
