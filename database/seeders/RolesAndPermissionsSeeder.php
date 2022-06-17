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
    // isil_islem_durumu_guncelleme	web	Isıl işlem durumu güncelleme
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

        if (Permission::count() > 0)
        {
            return;
        }

        /** İzinler */
            // create or find permissions
            Permission::create([
                'name' => 'siparis_ucreti_goruntuleme',
                'guard_name' => 'web',
                'slug' => 'Ücret görüntüleme'
            ]);
            Permission::create([
                'name' => 'isil_islem_durumu_guncelleme',
                'guard_name' => 'web',
                'slug' => 'Isıl işlem durumu güncelleme'
            ]);
            Permission::create([
                'name' => 'siparis_kaydetme',
                'guard_name' => 'web',
                'slug' => 'Sipariş kaydetme'
            ]);
            Permission::create([
                'name' => 'siparis_duzenleme',
                'guard_name' => 'web',
                'slug' => 'Sipariş düzenleme'
            ]);
            Permission::create([
                'name' => 'siparis_silme',
                'guard_name' => 'web',
                'slug' => 'Sipariş silme'
            ]);
            Permission::create([
                'name' => 'siparis_listeleme',
                'guard_name' => 'web',
                'slug' => 'Sipariş listeleme'
            ]);
            Permission::create([
                'name' => 'isil_islem_formu_kaydetme',
                'guard_name' => 'web',
                'slug' => 'Isıl işlem formu kaydetme'
            ]);
            Permission::create([
                'name' => 'isil_islem_formu_duzenleme',
                'guard_name' => 'web',
                'slug' => 'Isıl işlem formu düzenleme'
            ]);
            Permission::create([
                'name' => 'isil_islem_formu_silme',
                'guard_name' => 'web',
                'slug' => 'Isıl işlem formu silme'
            ]);
            Permission::create([
                'name' => 'isil_islem_formu_listeleme',
                'guard_name' => 'web',
                'slug' => 'Isıl işlem formu listeleme'
            ]);
            Permission::create([
                'name' => 'yonetim_menusu',
                'guard_name' => 'web',
                'slug' => 'Yönetim menüsü görme'
            ]);
            Permission::create([
                'name' => 'kullanici_kaydetme',
                'guard_name' => 'web',
                'slug' => 'Kullanıcı kaydetme'
            ]);
            Permission::create([
                'name' => 'kullanici_duzenleme',
                'guard_name' => 'web',
                'slug' => 'Kullanıcı düzenleme'
            ]);
            Permission::create([
                'name' => 'kullanici_silme',
                'guard_name' => 'web',
                'slug' => 'Kullanıcı silme'
            ]);
            Permission::create([
                'name' => 'kullanici_listeleme',
                'guard_name' => 'web',
                'slug' => 'Kullanıcı listeleme'
            ]);
            Permission::create([
                'name' => 'rol_kaydetme',
                'guard_name' => 'web',
                'slug' => 'Rol kaydetme'
            ]);
            Permission::create([
                'name' => 'rol_duzenleme',
                'guard_name' => 'web',
                'slug' => 'Rol düzenleme'
            ]);
            Permission::create([
                'name' => 'rol_silme',
                'guard_name' => 'web',
                'slug' => 'Rol silme'
            ]);
            Permission::create([
                'name' => 'rol_listeleme',
                'guard_name' => 'web',
                'slug' => 'Rol listeleme'
            ]);
            Permission::create([
                'name' => 'isil_islem_kaydetme',
                'guard_name' => 'web',
                'slug' => 'Isıl işlem kaydetme'
            ]);
            Permission::create([
                'name' => 'isil_islem_duzenleme',
                'guard_name' => 'web',
                'slug' => 'Isıl işlem düzenleme'
            ]);
            Permission::create([
                'name' => 'isil_islem_silme',
                'guard_name' => 'web',
                'slug' => 'Isıl işlem silme'
            ]);
            Permission::create([
                'name' => 'isil_islem_listeleme',
                'guard_name' => 'web',
                'slug' => 'Isıl işlem listeleme'
            ]);
            Permission::create([
                'name' => 'firma_kaydetme',
                'guard_name' => 'web',
                'slug' => 'Firma kaydetme'
            ]);
            Permission::create([
                'name' => 'firma_duzenleme',
                'guard_name' => 'web',
                'slug' => 'Firma düzenleme'
            ]);
            Permission::create([
                'name' => 'firma_silme',
                'guard_name' => 'web',
                'slug' => 'Firma silme'
            ]);
            Permission::create([
                'name' => 'firma_listeleme',
                'guard_name' => 'web',
                'slug' => 'Firma listeleme'
            ]);
            Permission::create([
                'name' => 'malzeme_kaydetme',
                'guard_name' => 'web',
                'slug' => 'Malzeme kaydetme'
            ]);
            Permission::create([
                'name' => 'malzeme_duzenleme',
                'guard_name' => 'web',
                'slug' => 'Malzeme düzenleme'
            ]);
            Permission::create([
                'name' => 'malzeme_silme',
                'guard_name' => 'web',
                'slug' => 'Malzeme silme'
            ]);
            Permission::create([
                'name' => 'malzeme_listeleme',
                'guard_name' => 'web',
                'slug' => 'Malzeme listeleme'
            ]);
            Permission::create([
                'name' => 'islem_turu_kaydetme',
                'guard_name' => 'web',
                'slug' => 'İşlem türü kaydetme'
            ]);
            Permission::create([
                'name' => 'islem_turu_duzenleme',
                'guard_name' => 'web',
                'slug' => 'İşlem türü düzenleme'
            ]);
            Permission::create([
                'name' => 'islem_turu_silme',
                'guard_name' => 'web',
                'slug' => 'İşlem türü silme'
            ]);
            Permission::create([
                'name' => 'islem_turu_listeleme',
                'guard_name' => 'web',
                'slug' => 'İşlem türü listeleme'
            ]);
        /** # İzinler # */

        /** Rol İzinleri */
            // create roles and assign created permissions
            // Admin rolü
            $role = Role::create([
                'name' => 'admin',
                'guard_name' => 'web',
                'slug' => 'ADMİN',
                'description' => 'Sistem yöneticisi'
            ]);
            $role->givePermissionTo(Permission::all());

            // Personel rolü
            // Tüm silme yetkisi, rol, yönetim yetkileri bulunmuyor
            $role = Role::create([
                'name' => 'personel',
                'guard_name' => 'web',
                'slug' => 'PERSONEL',
                'description' => 'Personel'
            ]);
            $role->givePermissionTo([
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
                'isil_islem_durumu_guncelleme',
            ]);

            // Mühendis rolü
            // Fiyat görüntüleme ve yönetim hariç tüm yetkiler
            $role = Role::create([
                'name' => 'muhendis',
                'guard_name' => 'web',
                'slug' => 'MÜHENDİS',
                'description' => 'Mühendis'
            ]);
            $role->givePermissionTo([
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
                'isil_islem_durumu_guncelleme',
            ]);

            // Usta başı rolü
            $role = Role::create([
                'name' => 'usta_basi',
                'guard_name' => 'web',
                'slug' => 'USTA BAŞI',
                'description' => 'Usta başı'
            ]);
            $role->givePermissionTo([
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
                'isil_islem_durumu_guncelleme',
            ]);

            // Saha elemanı rolü
            $role = Role::create([
                'name' => 'saha_elemani',
                'guard_name' => 'web',
                'slug' => 'SAHA ELEMANI',
                'description' => 'Saha elemanı'
            ]);
            $role->givePermissionTo([
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
                'isil_islem_durumu_guncelleme',
            ]);

            // Muhasebeci rolü
            // Sadece listeleme rolleri
            $role = Role::create([
                'name' => 'muhasebeci',
                'guard_name' => 'web',
                'slug' => 'MUHASEBECİ',
                'description' => 'Muhasebeci'
            ]);
            $role->givePermissionTo([
                'siparis_listeleme',
                'isil_islem_formu_listeleme',
                'isil_islem_listeleme',
                'islem_turu_listeleme',
                'firma_listeleme',
                'malzeme_listeleme',
                'islem_turu_listeleme',
            ]);
        /** # Rol İzinleri */

        // admin kullanıcısına rol atama
        $user = User::where('email', 'admin')->first();
        $user->assignRole('admin');
    }
}
