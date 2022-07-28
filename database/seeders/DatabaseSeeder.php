<?php

namespace Database\Seeders;

use App\Models\BildirimTurleri;
use App\Models\Firmalar;
use App\Models\IslemTurleri;
use App\Models\Malzemeler;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        // Bildirim tablosu oluşturulması
        if (!Schema::hasTable("bildirimler")) {
            Schema::create("bildirimler", function (Blueprint $table) {
                $table->bigIncrements('id')->comment("Bildirimlerin tutulduğu tablo");
                $table->integer('btid')->comment("Bildirim türü idsi (bildirim_turleri tablosundan)");
                $table->integer('kullaniciId')->comment("Bildirimin alındığı kullanıcı idsi (users tablosundan)");
                $table->string('baslik', 100)->comment("Bildirimin başlığı");
                $table->text('icerik')->comment("Bildirimin içeriği");
                $table->text('json')->nullable()->comment("Bildirimin ekstra verileri (Örn: Bildirime tıklandığında gösterilecek veriler)");
                $table->timestamps();
            });

            $mesajlar[] = 'Bildirimler tablosu oluşturuldu > ' . date('Y-m-d H:i:s');
        }

        // Bildirim türleri tablosu oluşturulması
        if (!Schema::hasTable("bildirim_turleri")) {
            Schema::create("bildirim_turleri", function (Blueprint $table) {
                $table->increments('id')->comment("Bildirim türlerin tutulduğu tablo");
                $table->string('ad', 100)->comment("Bildirim türünün adı");
                $table->string('kod', 100)->nullable()->comment("Bildirim türünün kodu");
                $table->text('json')->nullable()->comment("Bildirim türünün ekstra verileri");
                $table->timestamps();
                $table->softDeletes();
            });

            $bildirimTurleri = [
                [
                    // Sipariş tamamlandı, başlandı vs.
                    "ad" => "Sipariş Bildirimi",
                    "kod" => "SIPARIS_BILDIRIMI",
                    "json" => json_encode([
                        "renk" => "primary",
                    ]),
                ],
                [
                    // Form tamamlandı, başlandı vs.
                    "ad" => "Form Bildirimi",
                    "kod" => "FORM_BILDIRIMI",
                    "json" => json_encode([
                        "renk" => "warning",
                    ]),
                ],
                [
                    // İşlem tekrarı vs.
                    "ad" => "İşlem Bildirimi",
                    "kod" => "ISLEM_BILDIRIMI",
                    "json" => json_encode([
                        "renk" => "info",
                    ]),
                ],
                [
                    // İşlem durumu değiştiğinde bilgilendirme
                    "ad" => "İşlem Durumu Bildirimi",
                    "kod" => "ISLEM_DURUMU_BILDIRIMI",
                    "json" => json_encode([
                        "renk" => "danger",
                    ]),
                ],
                [
                    // Genel bildirimler
                    "ad" => "Genel Bildirim",
                    "kod" => "GENEL_BILDIRIM",
                    "json" => json_encode([
                        "renk" => "secondary",
                    ]),
                ],
            ];

            foreach ($bildirimTurleri as $bildirimTur)
            {
                $bildirim = new BildirimTurleri();
                $bildirim->ad = $bildirimTur['ad'];
                $bildirim->kod = $bildirimTur['kod'];
                $bildirim->json = $bildirimTur['json'];
                $bildirim->save();
            }

            $mesajlar[] = 'Bildirim türleri tablosu oluşturuldu > ' . date('Y-m-d H:i:s');
        }

        // Okunmamış bildirimler tablosu oluşturulması
        if (!Schema::hasTable("okunmamis_bildirimler")) {
            Schema::create("okunmamis_bildirimler", function (Blueprint $table) {
                $table->bigIncrements('id')->comment("Okunmamış bildirimlerin tutulduğu tablo (kolay silebilmek için eklendi)");
                $table->bigInteger('bildirimId')->comment("Okunmamış bildirimlerin idsi (bildirimler tablosundan)");
                $table->integer('kullaniciId')->comment("Okunmamış bildirimlerin alındığı kullanıcı idsi (users tablosundan)");
            });

            $mesajlar[] = 'Okunmamış bildirimler tablosu oluşturuldu > ' . date('Y-m-d H:i:s');
        }

        // Kullanıcılar tablosuna pushToken alanı eklenmesi
        if (!Schema::hasColumn("users", "pushToken")) {
            Schema::table("users", function (Blueprint $table) {
                $table->string('pushToken')->nullable()->after('password');
            });

            $mesajlar[] = 'Kullanıcılar tablosuna pushToken alanı eklendi > ' . date('Y-m-d H:i:s');
        }

        // İşlemler tablosuna miktarFiyatCarp alanı eklenmesi
        if (!Schema::hasColumn("islemler", "miktarFiyatCarp")) {
            Schema::table("islemler", function (Blueprint $table) {
                $table->tinyInteger('miktarFiyatCarp')
                    ->default(1)
                    ->after('birimFiyat');
            });

            $mesajlar[] = 'İşlemler tablosuna miktarFiyatCarp alanı eklendi > ' . date('Y-m-d H:i:s');
        }

        // İşlemler tablosundaki miktar, dara ve birimFiyat alanlarının tipinin değiştirilmesi
        if (Schema::hasColumn("islemler", "miktar") && Schema::hasColumn("islemler", "dara") && Schema::hasColumn("islemler", "birimFiyat")) {
            $miktarTuru = DB::select("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'islemler' AND COLUMN_NAME = 'miktar'")[0]->DATA_TYPE;
            $daraTuru = DB::select("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'islemler' AND COLUMN_NAME = 'dara'")[0]->DATA_TYPE;
            $birimFiyatTuru = DB::select("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'islemler' AND COLUMN_NAME = 'birimFiyat'")[0]->DATA_TYPE;

            if ($miktarTuru !== "double")
            {
                DB::statement('ALTER TABLE islemler MODIFY miktar DOUBLE(15, 2) DEFAULT 0');

                $mesajlar[] = 'İşlemler tablosundaki miktar alanının tipi değiştirildi > ' . date('Y-m-d H:i:s');
            }

            if ($daraTuru !== "double")
            {
                DB::statement('ALTER TABLE islemler MODIFY dara DOUBLE(15, 2) DEFAULT 0');

                $mesajlar[] = 'İşlemler tablosundaki dara alanının tipi değiştirildi > ' . date('Y-m-d H:i:s');
            }

            if ($birimFiyatTuru !== "double")
            {
                DB::statement('ALTER TABLE islemler MODIFY birimFiyat DOUBLE(15, 2) DEFAULT 0');

                $mesajlar[] = 'İşlemler tablosundaki birimFiyat alanının tipi değiştirildi > ' . date('Y-m-d H:i:s');
            }
        }

        // İşlemler tablosuna paraBirimi alanı eklenmesi
        if (!Schema::hasColumn("islemler", "paraBirimi")) {
            Schema::table("islemler", function (Blueprint $table) {
                $table->string('paraBirimi')
                    ->default('TL')
                    ->after('birimFiyat')
                    ->comment("İşlemin para birimi (kod cinsinden; TL, USD vb.)");
            });

            $mesajlar[] = 'İşlemler tablosuna paraBirimi alanı eklendi > ' . date('Y-m-d H:i:s');
        }

        // Formlar tablosuna userId alanı eklenmesi
        if (!Schema::hasColumn("formlar", "userId")) {
            Schema::table("formlar", function (Blueprint $table) {
                $table->integer('userId')
                    ->nullable()
                    ->after('id')
                    ->comment("Formu oluşturan kullanıcı idsi (users tablosundan)");
            });

            $mesajlar[] = 'Formlar tablosuna userId alanı eklendi > ' . date('Y-m-d H:i:s');
        }

        // İşlem tablosuna baslangicTarihi ve bitisTarihi alanlarının eklenmesi
        if (!Schema::hasColumn("islemler", "baslangicTarihi")) {
            Schema::table("islemler", function (Blueprint $table) {
                $table->dateTime('baslangicTarihi')
                    ->nullable()
                    ->after('resimYolu')
                    ->comment("İşlemin başlangıç tarihi");
            });

            $mesajlar[] = 'İşlemler tablosuna baslangicTarihi alanı eklendi > ' . date('Y-m-d H:i:s');
        }

        if (!Schema::hasColumn("islemler", "bitisTarihi")) {
            Schema::table("islemler", function (Blueprint $table) {
                $table->dateTime('bitisTarihi')
                    ->nullable()
                    ->after('baslangicTarihi')
                    ->comment("İşlemin bitiş tarihi");
            });

            $mesajlar[] = 'İşlemler tablosuna bitisTarihi alanı eklendi > ' . date('Y-m-d H:i:s');
        }

        // Siparişler tablosuna bitisTarihi alanı eklenmesi
        if (!Schema::hasColumn("siparisler", "bitisTarihi")) {
            Schema::table("siparisler", function (Blueprint $table) {
                $table->dateTime('bitisTarihi')
                    ->nullable()
                    ->after('tarih')
                    ->comment("Siparişin bitiş tarihi");
            });

            $mesajlar[] = 'Siparişler tablosuna bitisTarihi alanı eklendi > ' . date('Y-m-d H:i:s');
        }

        $mesajlar[] = 'Veritabanı güncellendi > ' . date('Y-m-d H:i:s');

        return implode("<br />", $mesajlar);
    }
}
