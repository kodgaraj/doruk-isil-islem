<?php

namespace Database\Seeders;

use App\Models\Firmalar;
use App\Models\IslemTurleri;
use App\Models\Malzemeler;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $mesajlar = [];

        $kullaniciTabloAdi = (new User())->getTable();
        if (!Schema::hasColumn($kullaniciTabloAdi, "jwt")) {
            Schema::table($kullaniciTabloAdi, function ($table) {
                $table->text("jwt")->after("password")->nullable();
            });

            $mesajlar[] = "Kullanıcıların JWT bilgileri eklendi > " . date('Y-m-d H:i:s');
        }

        $firmaTabloAdi = (new Firmalar())->getTable();
        if (!Schema::hasColumn($firmaTabloAdi, "deleted_at")) {
            Schema::table($firmaTabloAdi, function ($table) {
                $table->softDeletes();
            });

            $mesajlar[] = "Firmalar tablosuna deleted_at sütunu eklendi > " . date('Y-m-d H:i:s');
        }

        $islemTurleriTabloAdi = (new IslemTurleri())->getTable();
        if (!Schema::hasColumn($islemTurleriTabloAdi, "deleted_at")) {
            Schema::table($islemTurleriTabloAdi, function ($table) {
                $table->softDeletes();
            });

            $mesajlar[] = "IslemTurleri tablosuna deleted_at sütunu eklendi > " . date('Y-m-d H:i:s');
        }

        $malzemeTabloAdi = (new Malzemeler())->getTable();
        if (!Schema::hasColumn($malzemeTabloAdi, "deleted_at")) {
            Schema::table($malzemeTabloAdi, function ($table) {
                $table->softDeletes();
            });

            $mesajlar[] = "Malzemeler tablosun deleted_at sütunu eklendi > " . date('Y-m-d H:i:s');
        }

        if (!Schema::hasTable(config('activitylog.table_name'))) {
            $baglantiBilgisi = config('activitylog.database_connection') ?? null;
            $tabloAdi = config('activitylog.table_name');
            Schema::connection($baglantiBilgisi)->create($tabloAdi, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('log_name')->nullable();
                $table->text('description');
                $table->nullableMorphs('subject', 'subject');
                $table->nullableMorphs('causer', 'causer');
                $table->json('properties')->nullable();
                $table->timestamps();
                $table->index('log_name');
            });
            Schema::connection($baglantiBilgisi)->table($tabloAdi, function (Blueprint $table) {
                $table->string('event')->nullable()->after('subject_type');
            });
            Schema::connection($baglantiBilgisi)->table($tabloAdi, function (Blueprint $table) {
                $table->uuid('batch_uuid')->nullable()->after('properties');
            });

            $mesajlar[] = 'Activity Log tablosu oluşturuldu > ' . date('Y-m-d H:i:s');
        }

        $mesajlar[] = 'Veritabanı güncellendi > ' . date('Y-m-d H:i:s');

        return implode("<br />", $mesajlar);
    }
}
