<?php

namespace App\Http\Controllers;

use App\Models\Firmalar;
use App\Models\SiparisDurumlari;
use App\Models\Siparisler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiparisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function siparisEklemeFormu()
    {
        return view('siparis-formu');
    }

    public function siparisler(Request $request)
    {
        try
        {
            $sayfalamaSayisi = $request->sayfalamaSayisi ?? 10;
            $firmaTabloAdi = (new Firmalar())->getTable();
            $siparisDurumTabloAdi = (new SiparisDurumlari())->getTable();
            $siparisTabloAdi = (new Siparisler())->getTable();

            $siparisler = Siparisler::select(DB::raw("
                    $siparisTabloAdi.id as siparisId,
                    $siparisTabloAdi.ad as siparisAdi,
                    $siparisTabloAdi.irsaliyeNo,
                    $siparisTabloAdi.siparisNo,
                    $siparisTabloAdi.tarih,
                    $siparisTabloAdi.tutar,
                    $siparisTabloAdi.firmaId,
                    $siparisTabloAdi.durumId,
                    $siparisDurumTabloAdi.ad as siparisDurumAdi,
                    $firmaTabloAdi.firmaAdi,
                    $firmaTabloAdi.sorumluKisi
                "))
                ->join($firmaTabloAdi, $firmaTabloAdi . '.id', '=', $siparisTabloAdi . '.firmaId')
                ->join($siparisDurumTabloAdi, $siparisDurumTabloAdi . '.id', '=', $siparisTabloAdi . '.durumId')
                ->paginate($sayfalamaSayisi);

            // dd($siparisler->getPageName());

            return response()->json([
                'durum' => true,
                'mesaj' => 'Siparişler başarıyla getirildi.',
                'siparisler' => $siparisler
            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Son sipariş ve irsaliye numarasının bir sonraki numarasını döndürür.
     * Örnek: SPR000001 -> SPR000002
     */
    public function numaralariGetir(Request $request)
    {
        try
        {
            $siparisNo = Siparisler::max('siparisNo');

            if(!$siparisNo)
            {
                $siparisNo = 'SPR0000001';
            }
            else
            {
                $siparisNo = substr($siparisNo, 3);
                $siparisNo = 'SPR' . sprintf('%07d', $siparisNo + 1);
            }

            $irsaliyeNo = Siparisler::max('irsaliyeNo');

            if(!$irsaliyeNo)
            {
                $irsaliyeNo = 'IR0000001';
            }
            else
            {
                $irsaliyeNo = substr($irsaliyeNo, 2);
                $irsaliyeNo = 'IR' . sprintf('%07d', $irsaliyeNo + 1);
            }

            return response()->json([
                'durum' => true,
                'mesaj' => 'Sipariş başarıyla getirildi.',
                'numaralar' => [
                    "siparisNo" => $siparisNo,
                    "irsaliyeNo" => $irsaliyeNo
                ]
            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }
    }

    public function siparisDurumlariGetir()
    {
        try
        {
            $siparisDurumlari = SiparisDurumlari::all();

            return response()->json([
                'durum' => true,
                'mesaj' => 'Sipariş durumları başarıyla getirildi.',
                'siparisDurumlari' => $siparisDurumlari
            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }
    }

    public function firmalariGetir()
    {
        try
        {
            $firmalar = Firmalar::all();

            return response()->json([
                'durum' => true,
                'mesaj' => 'Firmalar başarıyla getirildi.',
                'firmalar' => $firmalar
            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }
    }

    public function siparisDetay(Request $request)
    {
        try
        {
            $siparis = $request->siparis;


        }
        catch(\Exception $e)
        {
            return response()->json([
                'durum' => false,
                'mesaj' => $e->getMessage()
            ], 500);
        }
    }
}
