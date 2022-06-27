<?php

namespace Database\Seeders;

use AddBatchUuidColumnToActivityLogTable;
use AddEventColumnToActivityLogTable;
use App\Models\Firmalar;
use App\Models\IslemTurleri;
use App\Models\Malzemeler;
use App\Models\User;
use CreateActivityLogTable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            $this->call(CreateActivityLogTable::class);
            $this->call(AddEventColumnToActivityLogTable::class);
            $this->call(AddBatchUuidColumnToActivityLogTable::class);

            $mesajlar[] = 'Activity Log tablosu oluşturuldu > ' . date('Y-m-d H:i:s');
        }

        $mesajlar[] = 'Veritabanı güncellendi > ' . date('Y-m-d H:i:s');

        return implode("<br />", $mesajlar);
    }
}
