<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use App\Models\IslemDurumlari;
use App\Models\Islemler;
use App\Models\Siparisler;
use Illuminate\Console\Command;

class IslemTerminBildirim extends Command
{
    // php artisan schedule:run >> /dev/null 2>&1

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'islem-termin-bildirim:send';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Her saat 08:05\'te termin süresi geçen işlemler için kullanıcılara bildirim atar.';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $islemTabloAdi = (new Islemler())->getTable();
        $siparisTabloAdi = (new Siparisler())->getTable();

        $tamamlandiIslemDurum = IslemDurumlari::where('kod', 'TAMAMLANDI')->first();

        $islemler = Islemler::select(
                "$siparisTabloAdi.*",
                "$islemTabloAdi.id as islemId"
            )
            ->join($siparisTabloAdi, "$siparisTabloAdi.id", '=', "$islemTabloAdi.siparisId")
            ->where("$islemTabloAdi.terminBildirim", '=', 0)
            ->whereNull("$siparisTabloAdi.bitisTarihi")
            ->whereNot("$islemTabloAdi.durumId", $tamamlandiIslemDurum->id)
            ->offset(0)
            ->limit(1000)
            ->get()
            ->toArray();

        $controller = new Controller();

        $gecikmisIslemler = [];
        foreach ($islemler as $islem)
        {
            $terminBilgileri = $controller->terminHesapla($islem["tarih"], $islem["terminSuresi"]);

            if (in_array($terminBilgileri["gecenSureKod"], ["BIRINCI_FAZ_GECIKMIS", "IKINCI_FAZ_GECIKMIS"]))
            {
                $gecikmisIslemler[] = [
                    "islem" => $islem,
                    "terminBilgileri" => $terminBilgileri,
                ];
            }
        }

        $bildirimIcerigi = "";
        $gecikmisIslemSayisi = count($gecikmisIslemler);

        if ($gecikmisIslemSayisi > 0)
        {
            if ($gecikmisIslemSayisi === 1)
            {
                $islem = $gecikmisIslemler[0]["islem"];
                $terminBilgileri = $gecikmisIslemler[0]["terminBilgileri"];

                $bildirimIcerigi = "$islem[siparisNo] sipariş numaralı siparişinizin termin süresi ($terminBilgileri[gecenSure] gün) geçmiştir.";
            }
            else
            {
                $bildirimIcerigi = $gecikmisIslemSayisi . " adet işlemin termin süresi geçmiş.";
            }

            $islemler = array_column($gecikmisIslemler, "islem");
            $islemIdler = array_column($islemler, "islemId");

            Islemler::whereIn("id", $islemIdler)->update(["terminBildirim" => 1]);
        }
        else
        {
            $bildirimIcerigi = "Gecikmiş işlem yok.";
        }

        $controller->bildirimAt(0, [
            "baslik" => "Termin Süresi Geçmiş İşlemler",
            "icerik" => $bildirimIcerigi,
            "link" => route("tum-islemler", ["tur" => "GECIKMIS"]),
            "kod" => "ISLEM_BILDIRIMI"
        ]);

        $this->info("Termin süresi geçmiş işlemler için kullanıcılara bildirim olarak gönderildi.");
    }
}