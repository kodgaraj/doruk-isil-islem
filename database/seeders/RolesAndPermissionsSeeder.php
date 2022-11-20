<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
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
                [
                    'name' => 'fatura_kesildi_listeleme',
                    'guard_name' => 'web',
                    'slug' => 'Fatura kesildi'
                ],
                [
                    'name' => 'siparis_raporu_olusturma',
                    'guard_name' => 'web',
                    'slug' => 'Sipariş raporu oluşturma'
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
                        'siparis_raporu_olusturma',
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
                        'fatura_kesildi_listeleme',
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
        if (!$user)
        {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin',
                'password' => Hash::make('12345'),
            ]);

            $user->assignRole('admin');
        }
    }
}
