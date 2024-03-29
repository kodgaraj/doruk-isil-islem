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
                $table->time('btid')->comment("Bildirim türü idsi (bildirim_turleri tablosundan)");
                $table->time('kullaniciId')->comment("Bildirimin alındığı kullanıcı idsi (users tablosundan)");
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

            foreach ($bildirimTurleri as $bildirimTur) {
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
        if (Schema::hasColumn("islemler", "miktar") && Schema::hasColumn("islemler", "dara") && Schema::hasColumn("islemler", "birimFiyat") && Schema::hasColumn("islemler", "sicaklik") && Schema::hasColumn("islemler", "carbon")&& Schema::hasColumn("islemler", "beklenenSure")) {
            $miktarTuru = DB::select("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'islemler' AND COLUMN_NAME = 'miktar'")[0]->DATA_TYPE;
            $daraTuru = DB::select("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'islemler' AND COLUMN_NAME = 'dara'")[0]->DATA_TYPE;
            $birimFiyatTuru = DB::select("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'islemler' AND COLUMN_NAME = 'birimFiyat'")[0]->DATA_TYPE;
            $sicaklikTuru = DB::select("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'islemler' AND COLUMN_NAME = 'sicaklik'")[0]->DATA_TYPE;
            $carbonTuru = DB::select("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'islemler' AND COLUMN_NAME = 'carbon'")[0]->DATA_TYPE;
            $beklenenSureTuru = DB::select("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'islemler' AND COLUMN_NAME = 'beklenenSure'")[0]->DATA_TYPE;

            if ($miktarTuru !== "double") {
                DB::statement('ALTER TABLE islemler MODIFY miktar DOUBLE(15, 2) DEFAULT 0');

                $mesajlar[] = 'İşlemler tablosundaki miktar alanının tipi değiştirildi > ' . date('Y-m-d H:i:s');
            }

            if ($daraTuru !== "double") {
                DB::statement('ALTER TABLE islemler MODIFY dara DOUBLE(15, 2) DEFAULT 0');

                $mesajlar[] = 'İşlemler tablosundaki dara alanının tipi değiştirildi > ' . date('Y-m-d H:i:s');
            }

            if ($birimFiyatTuru !== "double") {
                DB::statement('ALTER TABLE islemler MODIFY birimFiyat DOUBLE(15, 2) DEFAULT 0');

                $mesajlar[] = 'İşlemler tablosundaki birimFiyat alanının tipi değiştirildi > ' . date('Y-m-d H:i:s');
            }
            //sicaklik varcar değilse varchar yap
            if ($sicaklikTuru !== "varchar") {
                DB::statement('ALTER TABLE islemler MODIFY sicaklik VARCHAR(50) DEFAULT null');

                $mesajlar[] = 'İşlemler tablosundaki sicaklik alanının tipi değiştirildi > ' . date('Y-m-d H:i:s');
            }
            //carbon varcar değilse varchar yap
            if ($carbonTuru !== "varchar") {
                DB::statement('ALTER TABLE islemler MODIFY carbon VARCHAR(50) DEFAULT null');

                $mesajlar[] = 'İşlemler tablosundaki carbon alanının tipi değiştirildi > ' . date('Y-m-d H:i:s');
            }
            //beklenenSure varcar değilse varchar yap
            if ($beklenenSureTuru !== "varchar") {
                DB::statement('ALTER TABLE islemler MODIFY beklenenSure VARCHAR(50) DEFAULT null');

                $mesajlar[] = 'İşlemler tablosundaki beklenenSure alanının tipi değiştirildi > ' . date('Y-m-d H:i:s');
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

        // İşlemler tablosuna terminBildirim alanı eklenmesi
        if (!Schema::hasColumn("islemler", "terminBildirim")) {
            Schema::table("islemler", function (Blueprint $table) {
                $table->tinyInteger('terminBildirim')
                    ->default(0)
                    ->after('bitisTarihi')
                    ->comment("İşlemin termin süresi geçtiyse bildirim atılması (0: atılmadı, 1: atıldı)");
            });

            $mesajlar[] = 'İşlemler tablosuna terminBildirim alanı eklendi > ' . date('Y-m-d H:i:s');
        }

        // İşlemler tablosundan bildirim alanının kaldırılması
        if (Schema::hasColumn("islemler", "bildirim")) {
            Schema::table("islemler", function (Blueprint $table) {
                $table->dropColumn('bildirim');
            });

            $mesajlar[] = 'İşlemler tablosundan bildirim alanı kaldırıldı > ' . date('Y-m-d H:i:s');
        }

        // İşlemler tablosuna bolunmusId alanı eklenmesi
        if (!Schema::hasColumn("islemler", "bolunmusId")) {
            Schema::table("islemler", function (Blueprint $table) {
                $table->integer('bolunmusId')
                    ->nullable()
                    ->after('tekrarEdilenId')
                    ->comment("İşlemin bölünmüş olduğu işlem idsi (islemler tablosundan)");
            });

            $mesajlar[] = 'İşlemler tablosuna bolunmusId alanı eklendi > ' . date('Y-m-d H:i:s');
        }

        // İşlemler tablosuna bolunmusId alanı eklenmesi
        if (!Schema::hasColumn("siparisler", "faturaKesildi")) {
            Schema::table("siparisler", function (Blueprint $table) {
                $table->tinyInteger('faturaKesildi')
                    ->default(0)
                    ->after('terminSuresi')
                    ->comment("Siparişe ait fatura kesilip kesilmediği bilgisini tutar, 1 -> kesildi, 0 -> kesilmedi)");
            });

            $mesajlar[] = 'Siparişler tablosuna faturaKesildi alanı eklendi > ' . date('Y-m-d H:i:s');
        }

        if (!Schema::hasColumn("firmalar", "eposta")) {
            Schema::table("firmalar", function (Blueprint $table) {
                $table->string('eposta', 100)
                    ->nullable()
                    ->after('telefon')
                    ->comment("Firma eposta bilgisi");
            });

            $mesajlar[] = 'Firmalar tablosuna eposta alanı eklendi > ' . date('Y-m-d H:i:s');
        }

        if (!Schema::hasColumn("firmalar", "adres")) {
            Schema::table("firmalar", function (Blueprint $table) {
                $table->string('adres')
                    ->nullable()
                    ->after('eposta')
                    ->comment("Firma adres bilgisi");
            });

            $mesajlar[] = 'Firmalar tablosuna adres alanı eklendi > ' . date('Y-m-d H:i:s');
        }

        // Siparişler tablosuna faturaTarihi alanı eklenmesi
        if (!Schema::hasColumn("siparisler", "faturaTarihi")) {
            Schema::table("siparisler", function (Blueprint $table) {
                $table->dateTime('faturaTarihi')
                    ->nullable()
                    ->after('faturaKesildi')
                    ->comment("Siparişin fatura kesilme tarihi");
            });

            $mesajlar[] = 'Siparişler tablosuna faturaTarihi alanı eklendi > ' . date('Y-m-d H:i:s');
        }
        // sablonlar tablosu oluşturulması
        if (!Schema::hasTable("sablonlar")) {
            Schema::create("sablonlar", function (Blueprint $table) {
                $table->bigIncrements('id')->comment("Şablonların tutulduğu tablo idsi");
                $table->string('sablonAdi', 100)->comment("Şablonların Adı");
                $table->longText('icerik')->comment("Şablonların içeriğinin delta türünde tutulduğu alan")->nullable();
                $table->longText('icerik_html')->comment("Şablonların içeriğinin html türünde tutulduğu alan")->nullable();
                $table->string('tur', 10)->comment("Şablon Türü (TEKLIF, ISLEM, MAIL)");
                $table->string('kullanilabilirOgeler', 255)->comment("Şablonların içerisinde geçen öğelerin listesi");
                $table->timestamps();
                $table->softDeletes();
            });

            $mesajlar[] = 'Şablonlar tablosu oluşturuldu > ' . date('Y-m-d H:i:s');
        }
        // teklifler tablosu oluşturulması
        if (!Schema::hasTable("teklifler")) {
            Schema::create("teklifler", function (Blueprint $table) {
                $table->bigIncrements('id')->comment("Tekliflerin tutulduğu tablo idsi");
                $table->integer('firmaId')->comment("Tekliflerin alındığı firma idsi (firmalarım tablosundan)");
                $table->string('teklifAdi', 100)->comment("Tekliflerin başlığı")->nullable();
                $table->string('url', 255)->comment("Tekliflerin başlığı")->nullable();
                $table->string('tur', 10)->comment("Tekliflerin Türü (TEKLIF, ISLEM, MAIL)");
                $table->longText('icerik_html')->comment("Teklifin içeriğinin html türünde tutulduğu alan")->nullable();
                $table->text('json')->nullable()->comment("Teklifin ekstra verileri (Örn: Mail gönderildiğinde tutulacak veriler)");
                $table->timestamps();
                $table->softDeletes();
            });

            $mesajlar[] = 'Teklifler tablosu oluşturuldu > ' . date('Y-m-d H:i:s');
        }
        // Teklifler tablosuna topluKey alanlarının eklenmesi
        if (!Schema::hasColumn("teklifler", "topluKey")) {
            Schema::table("teklifler", function (Blueprint $table) {
                $table->bigInteger('topluKey')
                    ->nullable()
                    ->after('firmaId')
                    ->comment("Toplu oluşturulan tekliflerin aynı key ile gruplanması için kullanılan alan");
            });

            $mesajlar[] = 'Teklifler tablosuna topluKey alanlarının eklenmesi > ' . date('Y-m-d H:i:s');
        }

        // kisitlar tablosu oluşturulması
        if (!Schema::hasTable("kisitlar")) {
            Schema::create("kisitlar", function (Blueprint $table) {
                $table->bigIncrements('id')->comment("Kısıtların oluştuğu tablo idsi");
                $table->time('saatBaslangic')->comment("Kısıtlama Saat Başlangıç Değeri")->nullable();
                $table->time('saatBitis')->comment("Kısıtlama Saat Bitiş Değeri")->nullable();
                $table->text('ipler')->nullable()->comment("Kısıtlanacak ip'lerin verileri virgül ile ayrılarak girilir")->nullable();
                $table->text('kullanicilar')->nullable()->comment("Kısıtlanacak kullanıcıların verileri virgül ile ayrılarak girilir")->nullable();
                $table->text('roller')->nullable()->comment("Kısıtlanacak rollerin verileri virgül ile ayrılarak girilir")->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            $mesajlar[] = 'Kısıtlar tablosu oluşturuldu > ' . date('Y-m-d H:i:s');
        }

        if (!Schema::hasTable("login_log")) {
            Schema::create('login_log', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('user_id');
                $table->string('aciklama', 100);
                $table->integer('islem_kodu');
                $table->string('ip', 50);
                $table->timestamps();
            });


            $mesajlar[] = 'Log Login tablosu oluşturuldu > ' . date('Y-m-d H:i:s');
        }

        $mesajlar[] = 'Veritabanı güncellendi > ' . date('Y-m-d H:i:s');

        return implode("<br />", $mesajlar);
    }
}
