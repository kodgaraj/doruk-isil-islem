@extends('layout') 
@section('content')
<div class="row doruk-content">
    <h4 style="color:#999"><i class="fab fa-wpforms"> </i> SİPARİŞ FORMU</h4>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <template v-if="aktifSiparis === null">
                    <div class="row d-flex align-items-center">
                        <div class="col">
                            <h4 class="card-title">SİPARİŞLER</h4>
                        </div>
                        <div class="col-auto">
                            <div class="row d-flex align-items-center">
                                <div class="col">
                                    <div class="input-group">
                                        <input
                                            v-model="filtrelemeObjesi.arama"
                                            type="text"
                                            class="form-control"
                                            placeholder="Arama"
                                            aria-label="Arama"
                                            aria-describedby="arama"
                                            @keyup.enter="filtrele()"
                                        />
                                        <span @click="filtrele()" class="input-group-text waves-effect" id="arama">
                                            <i class="mdi mdi-magnify"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-auto ps-0">
                                    <!-- Filtreleme butonu -->
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#filtrelemeModal">
                                        <i class="fa fa-filter"></i>
                                    </button>
                                </div>

                                <div class="col-auto">
                                    @can("siparis_kaydetme")
                                        <button @click="siparisEklemeAc" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> SİPARİŞ EKLE</button>
                                    @endcan
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <small class="text-muted">
                                        Sipariş no, firma, irsaliye no...
                                    </small>
                                </div>
                            </div>

                            <div class="modal fade" id="filtrelemeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Filtreleme</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row gap-3">
                                                <div class="col-12 m-0">
                                                    <div class="form-group">
                                                        <label for="terminFiltre">Termin Süresi</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Minimum</span>
                                                            <input
                                                                v-model.number="filtrelemeObjesi.termin"
                                                                id="terminFiltre"
                                                                type="number"
                                                                class="form-control"
                                                                aria-label="Termin günü"
                                                                placeholder="Termin günü"
                                                            />
                                                            <span class="input-group-text">Gün</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 m-0">
                                                    <div class="form-group">
                                                        <label for="firmaFiltre">Firma</label>
                                                        <v-select
                                                            v-model="filtrelemeObjesi.firma"
                                                            :options="firmalar"
                                                            label="firmaAdi"
                                                            multiple
                                                            id="firmaFiltre"
                                                        ></v-select>
                                                    </div>
                                                </div>
                                                <div class="col-12 m-0">
                                                    <div class="form-group">
                                                        <div class="row d-flex align-items-center justify-space-between">
                                                            <div class="col">
                                                                <label for="tarihFiltre">Tarih</label>
                                                            </div>
                                                            <div class="col-auto">
                                                                <button
                                                                    v-if="filtrelemeObjesi.baslangicTarihi || filtrelemeObjesi.bitisTarihi"
                                                                    @click="filtrelemeTarihTemizle()"
                                                                    class="btn btn-sm btn-outline-danger p-0 m-0"
                                                                    type="button"
                                                                    aria-label="Tarih temizle"
                                                                >
                                                                    <span class="px-1">
                                                                        Tarihleri Temizle
                                                                        <i class="fa fa-times"></i>
                                                                    </span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text">Başlangıç</span>
                                                            <input
                                                                v-model="filtrelemeObjesi.baslangicTarihi"
                                                                type="date"
                                                                class="form-control"
                                                                placeholder="Başlangıç"
                                                                data-date-container='#datepicker2'
                                                                data-provide="datepicker"
                                                                data-date-autoclose="true"
                                                                id="tarih"
                                                                aria-label="Başlangıç"
                                                            />
                                                            <span class="input-group-text">Bitiş</span>
                                                            <input
                                                                v-model="filtrelemeObjesi.bitisTarihi"
                                                                type="date"
                                                                class="form-control"
                                                                placeholder="Bitiş"
                                                                data-date-container='#datepicker2'
                                                                data-provide="datepicker"
                                                                data-date-autoclose="true"
                                                                id="tarih"
                                                                aria-label="Bitiş"
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">VAZGEÇ</button>
                                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="filtrele()">ARA</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-3">
                            <template v-if="yukleniyor">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Yükleniyor...</span>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <template v-if="siparisler.data && siparisler.data.length">
                                    <div class="table-rep-plugin">
                                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                                            <table id="tech-companies-1" class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Termin</th>
                                                        <th>Sipariş No</th>
                                                        <th data-priority="2">Firma</th>
                                                        <th data-priority="2">Sipariş</th>
                                                        <th data-priority="3" class="text-center">İşlem Sayısı</th>
                                                        <th data-priority="1">İrsaliye No</th>
                                                        @can("siparis_ucreti_goruntuleme")
                                                            <th data-priority="4">Tutar</th>
                                                        @endcan
                                                        <th data-priority="5">Sipariş Tarihi</th>
                                                        <th data-priority="6" class="text-center">İşlemler</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(siparis, index) in siparisler.data" :key="index">
                                                        <td class="kisa-uzunluk">
                                                            <span class="badge badge-pill" :class="`bg-${ siparis.gecenSureRenk }`">@{{ siparis.gecenSure }} Gün</span>
                                                        </td>
                                                        <td class="kisa-uzunluk">@{{ siparis.siparisNo }}</td>
                                                        <td class="orta-uzunluk">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    @{{ siparis.firmaAdi }}
                                                                </div>
                                                                <div class="col-12">
                                                                    <h6>@{{ siparis.sorumluKisi }}</h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="uzun-uzunluk">@{{ siparis.siparisAdi }}</td>
                                                        <td class="kisa-uzunluk text-center">@{{ siparis.islemSayisi }}</td>
                                                        <td class="kisa-uzunluk">@{{ siparis.irsaliyeNo }}</td>
                                                        @can("siparis_ucreti_goruntuleme")
                                                            <td class="kisa-uzunluk">@{{ siparis.tutar ? siparis.tutar + "₺" : "-" }}</td>
                                                        @endcan
                                                        <td class="kisa-uzunluk">@{{ m(siparis.tarih).format("L") }}</td>
                                                        <td class="uzun-uzunluk text-center">
                                                            <div class="btn-group row d-inline-flex g-1">
                                                                <div class="col">
                                                                    <button @click="siparisDetayAc(siparis)" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></button>
                                                                </div>

                                                                @can("siparis_duzenleme")
                                                                    <div class="col">
                                                                        <button @click="siparisDuzenle(siparis)" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>
                                                                    </div>
                                                                @endcan

                                                                @can("siparis_silme")
                                                                    <div class="col">
                                                                        <button @click="siparisSil(siparis)" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                                                    </div>
                                                                @endcan
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer">
                                            <div class="row d-flex align-items-center justify-content-between">
                                                <div class="col-auto"></div>
                                                <div class="col">
                                                    <ul class="pagination pagination-rounded justify-content-center mb-0">
                                                        <li class="page-item">
                                                            <button class="page-link" :disabled="!siparisler.prev_page_url" @click="siparisleriGetir(siparisler.prev_page_url)">Önceki</button>
                                                        </li>
                                                        <li
                                                            v-for="sayfa in siparisler.last_page"
                                                            class="page-item"
                                                            :class="[siparisler.current_page === sayfa ? 'active' : '']"
                                                        >
                                                            <button class="page-link" @click="siparisleriGetir('/siparisler?page=' + sayfa)">@{{ sayfa }}</button>
                                                        </li>
                                                        <li class="page-item">
                                                            <button class="page-link" :disabled="!siparisler.next_page_url" @click="siparisleriGetir(siparisler.next_page_url)">Sonraki</button>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col-auto">
                                                    <small class="text-muted">Toplam Kayıt: @{{ siparisler.total }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="text-center">
                                        <h4>Sipariş Bulunamadı</h4>
                                    </div>
                                </template>
                            </template>
                        </div>
                        <!-- end col -->
                    </div>
                </template>
                <template v-else>
                    <div class="row">
                        <div class="col-8">
                            <div class="d-flex flex-row align-items-center">
                                <button @click="geri" class="btn btn-warning"><i class="fas fa-arrow-left"></i> GERİ</button>
                                <h4 class="card-title m-0 ms-2">
                                    <template v-if="aktifSiparis.siparisId">
                                        @{{ aktifSiparis.siparisAdi }}
                                    </template>
                                    <template v-else>
                                        SİPARİŞ EKLEME
                                    </template>
                                    <div class="d-inline-flex" v-if="araYukleniyor">
                                        <div class="spinner-grow text-primary m-1 spinner-grow-sm" role="status">
                                            <span class="sr-only">Yükleniyor...</span>
                                        </div>
                                    </div>
                                </h4>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            @can("siparis_duzenleme")
                                <button @click="moduDegistir" class="btn btn-outline-info">
                                    <i class="fas fa-eye" v-if="!aktifSiparis.onizlemeModu"></i>
                                    <i class="fas fa-eye-slash" v-else></i>
                                </button>
                            @endcan
                            <button v-if="aktifSiparis.onizlemeModu" @click="ciktiAl" class="btn btn-primary">
                                <i class="fas fa-file-export"></i>
                                ÇIKTI
                            </button>
                            @canany(["siparis_duzenleme", "siparis_kaydetme"])
                                <button
                                    @click="siparisKaydet"
                                    class="btn btn-success"
                                    :disabled="_.size(aktifSiparis.islemler) === 0"
                                >
                                    <i class="fas fa-save"></i> KAYDET
                                </button>
                            @endcan
                        </div>
                    </div>

                    <div class="container p-1" id="onizlemeGorunumu">
                        <div class="row mt-3">
                            <div class="col-12 col-sm-6 col-md-4 mb-2">
                                <div class="form-group">
                                    <template v-if="aktifSiparis.onizlemeModu">
                                        <label for="tarih">Tarih</label>
                                        <h5 id="tarih">@{{ m(aktifSiparis.tarih).format('L') }}</h5>
                                    </template>
                                    <template v-else>
                                        <label for="tarih">Tarih *</label>
                                        <input
                                            v-model="aktifSiparis.tarih"
                                            type="date"
                                            class="form-control"
                                            placeholder="gg.aa.yyyy"
                                            data-date-container='#datepicker2'
                                            data-provide="datepicker"
                                            data-date-autoclose="true" id="tarih"
                                        />
                                    </template>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 mb-2">
                                <template v-if="aktifSiparis.onizlemeModu">
                                    <div class="form-group">
                                        <label for="siparisNo">Sipariş No</label>
                                        <h5 id="siparisNo">@{{ aktifSiparis.siparisNo }}</h5>
                                    </div>
                                </template>
                                <template v-else>
                                    <label class="form-label">Sipariş/Sıra No *</label>
                                    <input
                                        v-model="aktifSiparis.siparisNo"
                                        v-mask="'DRK#######'"
                                        class="form-control"
                                        placeholder="Sipariş numarası giriniz... (Örn: DRK0000001)"
                                        type="text"
                                    />
                                </template>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 mb-2">
                                <template v-if="aktifSiparis.onizlemeModu">
                                    <div class="form-group">
                                        <label for="siparisAdi">Sipariş Adı</label>
                                        <h5 id="siparisAdi">@{{ aktifSiparis.siparisAdi }}</h5>
                                    </div>
                                </template>
                                <template v-else>
                                    <label class="form-label">Sipariş Adı *</label>
                                    <input
                                        v-model="aktifSiparis.siparisAdi"
                                        class="form-control"
                                        placeholder="Sipariş adı giriniz..."
                                        type="text"
                                    />
                                    <small class="text-muted">Siparişe özel bir isim girebilirsiniz</small>
                                </template>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 mb-2">
                                <template v-if="aktifSiparis.onizlemeModu">
                                    <div class="form-group">
                                        <label for="irsaliyeNo">İrsaliye No</label>
                                        <h5 id="irsaliyeNo">@{{ aktifSiparis.irsaliyeNo }}</h5>
                                    </div>
                                </template>
                                <template v-else>
                                    <label class="form-label">İrsaliye No</label>
                                    <input
                                        v-model="aktifSiparis.irsaliyeNo"
                                        v-mask="'IR#######'"
                                        class="form-control"
                                        placeholder="İrsaliye numarası giriniz... (IR0000001)"
                                        type="text"
                                    />
                                </template>
                            </div>
                            <div class="col-6 col-sm-2 mb-2">
                                <template v-if="aktifSiparis.onizlemeModu">
                                    <div class="form-group">
                                        <label for="toplamTutar">Toplam Tutar</label>
                                        <h5 id="toplamTutar">@{{ aktifSiparis.tutar }} ₺</h5>
                                    </div>
                                </template>
                                <template v-else>
                                    <label class="form-label">Toplam Tutar</label>
                                    <input
                                        v-model="aktifSiparis.tutar"
                                        class="form-control"
                                        placeholder="Toplam tutarını giriniz..."
                                        type="number"
                                    />
                                </template>
                            </div>
                            <div class="col-6 col-sm-2 mb-2">
                                <template v-if="aktifSiparis.onizlemeModu">
                                    <div class="form-group">
                                        <label for="terminSuresi">Termin</label>
                                        <h5 id="terminSuresi">
                                            @{{ aktifSiparis.terminSuresi }}
                                            <span class="badge badge-pill" :class="`bg-${ aktifSiparis.gecenSureRenk }`">@{{ aktifSiparis.gecenSure }} Gün</span>
                                        </h5>
                                    </div>
                                </template>
                                <template v-else>
                                    <label class="form-label">Termin</label>
                                    <input
                                        v-model="aktifSiparis.terminSuresi"
                                        class="form-control"
                                        placeholder="Termin süresi giriniz..."
                                        type="number"
                                    />
                                </template>
                            </div>
                            <div class="mb-3 col-12 col-sm-6 col-md-4">
                                <template v-if="aktifSiparis.onizlemeModu">
                                    <div class="form-group">
                                        <label for="siparisDurumu">Sipariş Durumu</label>
                                        <h5 id="siparisDurumu">@{{ aktifSiparis.siparisDurumu.ad }}</h5>
                                    </div>
                                </template>
                                <template v-else>
                                    <label class="form-label">Sipariş Durumu</label>
                                    <select class="form-control select2" v-model="aktifSiparis.siparisDurumu">
                                        <optgroup label="Sipariş Durumu">
                                            <option
                                                v-for="(durum, index) in siparisDurumlari"
                                                :value="durum"
                                                :key="index"
                                            >
                                                @{{ durum.ad }}
                                            </option>
                                        </optgroup>
                                    </select>
                                </template>
                            </div>
                            <div class="mb-3 col-12 col-sm-6 col-md-4">
                                <template v-if="aktifSiparis.onizlemeModu">
                                    <div class="form-group">
                                        <label for="firma">Firmalar</label>
                                        <h5 id="firma">@{{ aktifSiparis.firma.firmaAdi }}</h5>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="row d-flex align-items-end">
                                        <div class="col">
                                            <label class="form-label">Firmalar *</label>
                                            <v-select
                                                v-model="aktifSiparis.firma"
                                                :options="firmalar"
                                                label="firmaAdi"
                                            >
                                                <template v-slot:option="firma">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            @{{ firma.firmaAdi }}
                                                        </div>
                                                        <div class="col-12">
                                                            (@{{ firma.sorumluKisi }})
                                                        </div>
                                                    </div>
                                                </template>
                                                <div slot="no-options">Firma bulunamadı!</div>
                                            </v-select>
                                        </div>
                                        @can("firma_kaydetme")
                                            <div class="col-auto p-0">
                                                <button
                                                    class="btn btn-primary"
                                                    @click="firmaEkleAc()"
                                                >
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        @endcan
                                    </div>
                                </template>
                            </div>
                            @canany(["siparis_kaydetme", "siparis_duzenleme"])
                                @can("siparis_ucreti_goruntuleme")
                                    <div class="mb-3 col-12 col-sm-6 col-md-4 d-flex align-items-center" v-if="!aktifSiparis.onizlemeModu">
                                        <div class="form-check form-switch h5">
                                            <input class="form-check-input" type="checkbox" value="" id="miktarFiyatCarp" v-model="aktifSiparis.miktarFiyatCarp">
                                            <label class="form-check-label" for="miktarFiyatCarp">
                                                Net x Tutar
                                            </label>
                                        </div>
                                    </div>
                                @endcan
                            @endcan
                            <div class="form-group col-12 mb-2">
                                <template v-if="aktifSiparis.onizlemeModu">
                                    <label for="aciklama">Açıklama</label>
                                    <h5 id="aciklama">@{{ aktifSiparis.aciklama }}</h5>
                                </template>
                                <template v-else>
                                    <label for="aciklama">Açıklama</label>
                                    <textarea
                                        v-model="aktifSiparis.aciklama"
                                        class="form-control"
                                        id="aciklama"
                                        rows="3"
                                    ></textarea>
                                </template>
                            </div>
                        </div>
                        <div class="mb-3 row overflow-auto">
                            <div class="col-12">
                                <table class="table table-striped table-bordered nowrap" id="urun-detay">
                                    <thead>
                                        <th>Sıra No</th>
                                        <th class="text-center">Resim</th>
                                        <th>Malzeme*</th>
                                        <th>Adet</th>
                                        <th class="text-center">Miktar (KG)</th>
                                        <th>Dara (KG)</th>
                                        <th class="text-center">Net (KG)</th>
                                        @can("siparis_ucreti_goruntuleme")
                                            <th>Tutar</th>
                                        @endcan
                                        <th>Kalite</th>
                                        <th>Yapılacak İşlem</th>
                                        <th>İstenilen Sertlik</th>
                                        <th>İşlem Durumu</th>
                                        <th v-if="!aktifSiparis.onizlemeModu">İşlemler</th>
                                    </thead>
                                    <tbody id="islem-satir-ekle">
                                        <template v-if="aktifSiparis.onizlemeModu">
                                            <tr v-for="(islem, index) in aktifSiparis.islemler">
                                                <td>@{{ index + 1 }}</td>
                                                <td class="text-center">
                                                    <img
                                                        :src="islem.resimYolu ? islem.resimYolu : varsayilanResimYolu"
                                                        class="kg-resim-sec"
                                                    />
                                                </td>
                                                <td>@{{ islem.malzeme.ad }}</td>
                                                <td>@{{ islem.adet ? islem.adet : "0" }}</td>
                                                <td>@{{ islem.miktar ? islem.miktar : "0" }}</td>
                                                <td>@{{ islem.dara ? islem.dara : "0" }}</td>
                                                @can("siparis_ucreti_goruntuleme")
                                                    <td>@{{ islem.birimFiyat }} ₺</td>
                                                @endcan
                                                <td>@{{ islem.kalite ? islem.kalite : "-" }}</td>
                                                <td>@{{ islem.yapilacakIslem ? islem.yapilacakIslem.ad : "-" }}</td>
                                                <td>@{{ islem.istenilenSertlik ? islem.istenilenSertlik : "-" }}</td>
                                                <td>@{{ islem.islemDurumu ? islem.islemDurumu.ad : "-" }}</td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr v-for="(islem, index) in aktifSiparis.islemler" :key="index" style="vertical-align: middle;">
                                                <td># @{{ index + 1 }}</td>
                                                <td class="text-center">
                                                    <img
                                                        v-if="islem.resim || islem.resimYolu"
                                                        :src="islem.resim ? islem.resim : islem.resimYolu"
                                                        class="kg-resim-sec"
                                                        @click="resimSecimAc(index)"
                                                    />
                                                    <button
                                                        v-else
                                                        @click="resimSecimAc(index)"
                                                        class="btn btn-primary"
                                                    >
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </td>
                                                <td class="en-uzun-uzunluk">
                                                    <div class="row d-flex">
                                                        <div class="col">
                                                            <v-select
                                                                v-model="islem.malzeme"
                                                                :options="malzemeler"
                                                                label="ad"
                                                            >
                                                                <template #selected-option="option">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            @{{ _.truncate(option.ad, { length: 15, omission: "..." }) }}
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                                <div slot="no-options">Malzeme bulunamadı!</div>
                                                            </v-select>
                                                        </div>
                                                        @can("malzeme_kaydetme")
                                                            <div class="col-auto ps-0" v-if="!aktifSiparis.onizlemeModu">
                                                                <button
                                                                    class="btn btn-primary"
                                                                    @click="malzemeEkleAc(index)"
                                                                >
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        @endcan
                                                    </div>
                                                </td>
                                                <td class="kisa-uzunluk">
                                                    <input class="form-control" type="number" placeholder="Adet" v-model="islem.adet">
                                                </td>
                                                <td class="orta-uzunluk text-center">
                                                    <input class="form-control" type="number" placeholder="Miktar (KG)" v-model="islem.miktar">
                                                </td>
                                                <td class="kisa-uzunluk">
                                                    <input class="form-control" type="number" placeholder="Dara (KG)" v-model="islem.dara">
                                                </td>
                                                <td class="kisa-uzunluk text-center">
                                                    <b><h5>@{{ islem.net ? islem.net : "0" }}</h5></b>
                                                </td>
                                                @can("siparis_ucreti_goruntuleme")
                                                    <td class="kisa-uzunluk">
                                                        <input class="form-control" type="number" placeholder="Birim Fiyat" v-model="islem.birimFiyat">
                                                    </td>
                                                @endcan
                                                <td class="kisa-uzunluk">
                                                    <input class="form-control" type="text" placeholder="Kalite" v-model="islem.kalite">
                                                </td>
                                                <td class="en-uzun-uzunluk">
                                                    <div class="row d-flex">
                                                        <div class="col">
                                                            <v-select
                                                                v-model="islem.yapilacakIslem"
                                                                :options="islemTurleri"
                                                                label="ad"
                                                            >
                                                                <template #selected-option="option">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            @{{ _.truncate(option.ad, { length: 15, omission: "..." }) }}
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                                <div slot="no-options">İşlem türü bulunamadı!</div>
                                                            </v-select>
                                                        </div>
                                                        @can("islem_turu_kaydetme")
                                                            <div class="col-auto ps-0" v-if="!aktifSiparis.onizlemeModu">
                                                                <button
                                                                    class="btn btn-primary"
                                                                    @click="islemTuruEklemeAc(index)"
                                                                >
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        @endcan
                                                    </div>
                                                </td>
                                                <td class="kisa-uzunluk">
                                                    <input class="form-control" type="text" placeholder="İstenilen Sertlik" v-model="islem.istenilenSertlik">
                                                </td>
                                                <td class="orta-uzunluk">
                                                    <select class="form-select" aria-label="İşlem Durumu" v-model="islem.islemDurumu">
                                                        <option
                                                            v-for="(islemDurumu, index) in islemDurumlari"
                                                            :value="islemDurumu"
                                                            :key="index"
                                                        >
                                                            @{{ islemDurumu.ad }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td class="kisa-uzunluk" v-if="!aktifSiparis.onizlemeModu">
                                                    <button class="btn btn-danger" @click="islemSil(index)">Sil</button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                    <tfoot v-if="!aktifSiparis.onizlemeModu">
                                        <tr>
                                            <td colspan="100%">
                                                <div class="d-grid">
                                                    <button class="btn btn-info btn-sm p-0" @click="islemEkle">
                                                        <i class="fa fa-plus"></i>
                                                        Ekle
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
    <!-- end col -->
</div>
@endsection

@section('script')

<script>
    let mixinApp = {
        data: function () {
            return {
                siparisler: {},
                aktifSiparis: null,
                yukleniyorObjesi: {
                    numaralar: false,
                    siparisDurumlari: false,
                    firmalar: false,
                    malzemeler: false,
                    islemTurleri: false,
                    islemDurumlari: false,
                },
                siparisDurumlari: [],
                islemDurumlari: [],
                firmalar: [],
                malzemeler: [],
                islemTurleri: [],
                firmaObjesi: {
                    ad: '',
                    firmaSorumlusu: '',
                    telefon: '',
                },
                filtrelemeObjesi: {
                    arama: "",
                    termin: 0,
                    firma: null,
                    baslangicTarihi: null,
                    bitisTarihi: null,
                    limit: 10,
                },
            }
        },
        mounted() {
            this.siparisleriGetir();
            this.firmalariGetir();
        },
        watch: {
            "aktifSiparis.islemler": {
                handler: function (newValue, oldValue) {
                    if (!this.aktifSiparis) return;

                    let toplam = 0;
                    for (let i in this.aktifSiparis.islemler) {
                        const islem = this.aktifSiparis.islemler[i];

                        islem.net = _.toNumber(islem.miktar) - _.toNumber(islem.dara);

                        const birimFiyat = (this.aktifSiparis.miktarFiyatCarp ? islem.net : 1) * _.toNumber(islem.birimFiyat);
                        toplam += _.toNumber(birimFiyat);
                    }

                    this.aktifSiparis.tutar = toplam;
                },
                deep: true
            },
            "aktifSiparis.miktarFiyatCarp": {
                handler: function (newValue, oldValue) {
                    if (!this.aktifSiparis) return;

                    this.aktifSiparis.islemler = _.cloneDeep(this.aktifSiparis.islemler);
                },
                deep: true
            },
        },
        computed: {
            araYukleniyor() {
                let yukleniyor = false;
                for (let i in this.yukleniyorObjesi) {
                    if (this.yukleniyorObjesi[i]) {
                        yukleniyor = true;
                        break;
                    }
                }
                return yukleniyor;
            },
        },
        methods: {
            siparisleriGetir(url = "/siparisler") {
                this.yukleniyorDurum(true);
                axios.get(url, {
                    params: {
                        filtreleme: this.filtrelemeObjesi,
                    }
                })
                .then(response => {
                    this.yukleniyorDurum(false);

                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    this.siparisler = response.data.siparisler;

                    this.siparisler = _.cloneDeep(this.siparisler);
                })
                .catch(error => {
                    this.yukleniyorDurum(false);
                    console.log(error);
                });
            },
            siparisEklemeAc() {
                this.aktifSiparis = {
                    tarih: this.m().format("YYYY-MM-DD"),
                    siparisNo: "",
                    siparisAdi: "",
                    terminSuresi: 5,
                    islemler: [],
                    firma: null,
                    onizlemeModu: false,
                    miktarFiyatCarp: true,
                };

                this.numaralariGetir();
                this.siparisDurumlariGetir();

                if (!_.size(this.firmalar)) {
                    this.firmalariGetir();
                }
            },
            geri() {
                this.aktifSiparis = null;
            },
            numaralariGetir() {
                this.yukleniyorObjesi.numaralar = true;
                axios.get("/numaralariGetir")
                .then(response => {
                    this.yukleniyorObjesi.numaralar = false;
                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    this.aktifSiparis.siparisNo = response.data.numaralar.siparisNo;
                    this.aktifSiparis.irsaliyeNo = response.data.numaralar.irsaliyeNo;
                    this.siparisAdiOlustur();

                    this.aktifSiparis = _.cloneDeep(this.aktifSiparis);
                })
                .catch(error => {
                    this.yukleniyorObjesi.numaralar = false;
                    console.log(error);
                });
            },
            siparisDurumlariGetir() {
                this.yukleniyorObjesi.siparisDurumlari = true;
                return axios.get("/siparisDurumlariGetir")
                .then(response => {
                    this.yukleniyorObjesi.siparisDurumlari = false;
                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    this.siparisDurumlari = response.data.siparisDurumlari;

                    if (this.aktifSiparis && !this.aktifSiparis.siparisId) {
                        const siparisAlindiDurum = _.find(this.siparisDurumlari, {
                            kod: "SIPARIS_ALINDI"
                        });

                        if (siparisAlindiDurum) {
                            this.aktifSiparis.siparisDurumu = siparisAlindiDurum;
                        }
                    }
                })
                .catch(error => {
                    this.yukleniyorObjesi.siparisDurumlari = false;
                    console.log(error);
                });
            },
            siparisAdiOlustur() {
                if (this.aktifSiparis.siparisAdi) {
                    return;
                }

                this.aktifSiparis.siparisAdi = this.aktifSiparis.siparisNo + " - Numaralı Sipariş";
            },
            firmalariGetir() {
                this.yukleniyorObjesi.firmalar = true;
                return axios.get("/firmalariGetir")
                .then(response => {
                    this.yukleniyorObjesi.firmalar = false;
                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    this.firmalar = response.data.firmalar;
                })
                .catch(error => {
                    this.yukleniyorObjesi.firmalar = false;
                    this.uyariAc({
                        baslik: 'Hata',
                        mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                        tur: "error"
                    });
                    console.log(error);
                });
            },
            malzemeleriGetir() {
                this.yukleniyorObjesi.malzemeler = true;
                return axios.get("/malzemeleriGetir")
                .then(response => {
                    this.yukleniyorObjesi.malzemeler = false;
                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    this.malzemeler = response.data.malzemeler;
                })
                .catch(error => {
                    this.yukleniyorObjesi.malzemeler = false;
                    this.uyariAc({
                        baslik: 'Hata',
                        mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                        tur: "error"
                    });
                    console.log(error);
                });
            },
            islemTurleriGetir() {
                this.yukleniyorObjesi.islemTurleri = true;
                return axios.get("/islemTurleriGetir")
                .then(response => {
                    this.yukleniyorObjesi.islemTurleri = false;
                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    this.islemTurleri = response.data.islemTurleri;
                })
                .catch(error => {
                    this.yukleniyorObjesi.islemTurleri = false;
                    this.uyariAc({
                        baslik: 'Hata',
                        mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                        tur: "error"
                    });
                    console.log(error);
                });
            },
            islemDurumlariGetir(islemEkleme = false) {
                this.yukleniyorObjesi.islemDurumlari = true;
                return axios.get("/islemDurumlariGetir")
                .then(response => {
                    this.yukleniyorObjesi.islemDurumlari = false;
                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    this.islemDurumlari = response.data.islemDurumlari;

                    if (islemEkleme) {
                        _.forEach(this.aktifSiparis.islemler, (islem) => {
                            if (!islem.islemDurumu) {
                                islem.islemDurumu = _.find(this.islemDurumlari, {
                                    kod: "BASLANMADI"
                                });
                            }
                        });
                    }
                })
                .catch(error => {
                    this.yukleniyorObjesi.islemDurumlari = false;
                    this.uyariAc({
                        baslik: 'Hata',
                        mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                        tur: "error"
                    });
                    console.log(error);
                });
            },
            islemEkle() {
                const veriler = {
                    malzeme: null,
                    adet: 1,
                    miktar: 1,
                    dara: 0,
                    birimFiyat: 0,
                    kalite: "",
                    yapilacakIslem: null,
                    istenilenSertlik: "",
                    islemDurumu: null,
                };

                if (!this.malzemeler.length && !this.yukleniyorObjesi.malzemeler) {
                    this.malzemeleriGetir();
                }

                if (!this.islemTurleri.length && !this.yukleniyorObjesi.islemTurleri) {
                    this.islemTurleriGetir();
                }

                if (!this.islemDurumlari.length && !this.yukleniyorObjesi.islemDurumlari) {
                    this.islemDurumlariGetir(true);
                }
                else {
                    veriler.islemDurumu = _.find(this.islemDurumlari, {
                        kod: "BASLANMADI"
                    });
                }

                this.aktifSiparis.islemler.push(veriler);
            },
            islemSil(index) {
                if (this.aktifSiparis.siparisId && this.aktifSiparis.islemler[index].id) {
                    if (!this.aktifSiparis.silinenIslemler) {
                        this.aktifSiparis.silinenIslemler = [];
                    }

                    this.aktifSiparis.silinenIslemler.push(this.aktifSiparis.islemler[index].id);
                }

                this.aktifSiparis.islemler.splice(index, 1);
            },
            siparisKaydet() {
                const islem = () => {
                    this.yukleniyorObjesi.kaydet = true;
                    axios.post("/siparisKaydet", {
                        siparis: this.aktifSiparis
                    })
                    .then(response => {
                        this.yukleniyorObjesi.kaydet = false;
                        if (!response.data.durum) {
                            return this.uyariAc({
                                baslik: 'Hata',
                                mesaj: response.data.mesaj,
                                tur: "error"
                            });
                        }

                        this.uyariAc({
                            baslik: 'Başarılı',
                            mesaj: response.data.mesaj,
                            tur: "success",
                            ozellikler: {
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 2000
                            }
                        });
                        this.siparisleriGetir();
                        this.geri();
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.kaydet = false;
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                        console.log(error);
                    });
                };

                if (_.size(this.aktifSiparis.silinenIslemler)) {
                    Swal.fire({
                        title: "Uyarı",
                        text: `Eğer devam ederseniz, ${_.size(this.aktifSiparis.silinenIslemler)} adet işlem silinecektir. Devam etmek istiyor musunuz?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Devam Et',
                        cancelButtonText: 'İptal',
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            islem();
                        } else if (result.isDenied) {}
                    });
                } else {
                    islem();
                }
            },
            async siparisDetayAc(siparis) {
                // yükleniyor başlat
                Swal.showLoading();
                this.siparisDuzenle(siparis).then(() => {
                    this.aktifSiparis.onizlemeModu = true;

                    this.aktifSiparis = _.cloneDeep(this.aktifSiparis);
                    Swal.hideLoading();
                    Swal.clickConfirm();
                });
            },
            siparisDuzenle(siparis) {
                const promises = [];

                if (!_.size(this.siparisDurumlari)) {
                    promises.push(this.siparisDurumlariGetir());
                }

                if (!_.size(this.firmalar)) {
                    promises.push(this.firmalariGetir());
                }

                if (!_.size(this.malzemeler)) {
                    promises.push(this.malzemeleriGetir());
                }

                if (!_.size(this.islemTurleri)) {
                    promises.push(this.islemTurleriGetir());
                }

                if (!_.size(this.islemDurumlari)) {
                    promises.push(this.islemDurumlariGetir());
                }

                return axios.post("/siparisDetay", {
                    siparisId: siparis.siparisId
                })
                .then(async response => {
                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    await Promise.all(promises);

                    const aktifSiparis = {
                        ...siparis,
                        islemler: response.data.veriler.islemler,
                        miktarFiyatCarp: true,
                    };

                    const firma = _.find(this.firmalar, {
                        id: aktifSiparis.firmaId
                    });

                    if (firma) {
                        aktifSiparis.firma = firma;
                    }

                    const siparisDurumu = _.find(this.siparisDurumlari, {
                        id: aktifSiparis.durumId
                    });

                    if (siparisDurumu) {
                        aktifSiparis.siparisDurumu = siparisDurumu;
                    }

                    if (_.size(aktifSiparis.islemler)) {
                        aktifSiparis.islemler.forEach(islem => {

                            if (islem.malzemeId) {
                                const malzeme = _.find(this.malzemeler, {
                                    id: islem.malzemeId
                                });

                                if (malzeme) {
                                    islem.malzeme = malzeme;
                                }
                            }

                            if (islem.islemTuruId) {
                                const islemTur = _.find(this.islemTurleri, {
                                    id: islem.islemTuruId
                                });

                                if (islemTur) {
                                    islem.yapilacakIslem = islemTur;
                                }
                            }

                            if (islem.durumId) {
                                const islemDurumu = _.find(this.islemDurumlari, {
                                    id: islem.durumId
                                });

                                if (islemDurumu) {
                                    islem.islemDurumu = islemDurumu;
                                }
                            }
                        });
                    }

                    this.aktifSiparis = aktifSiparis;
                })
                .catch(error => {
                    console.log(error);
                    this.uyariAc({
                        baslik: 'Hata',
                        mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                        tur: "error"
                    });
                });
            },
            siparisSil(siparis) {
                Swal.fire({
                    title: "Uyarı",
                    text: `Siparişi silerseniz siparişe ait ${siparis.islemSayisi} adet işlem kaydı da silinecektir. Siparişi silmek istediğinizden emin misiniz?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sil',
                    cancelButtonText: 'İptal',
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        this.yukleniyorObjesi.siparisSil = true;
                        axios.post("/siparisSil", {
                            siparisId: siparis.siparisId
                        })
                        .then(response => {
                            this.yukleniyorObjesi.siparisSil = false;
                            if (!response.data.durum) {
                                return this.uyariAc({
                                    baslik: 'Hata',
                                    mesaj: response.data.mesaj,
                                    tur: "error"
                                });
                            }

                            this.uyariAc({
                                baslik: 'Başarılı',
                                mesaj: response.data.mesaj,
                                tur: "success",
                                ozellikler: {
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 2000
                                }
                            });

                            this.siparisleriGetir();
                        })
                        .catch(error => {
                            this.yukleniyorObjesi.siparisSil = false;
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                                tur: "error"
                            });
                            console.log(error);
                        });
                    } else if (result.isDenied) {}
                });
            },
            malzemeSecildiginde(islemIndex, malzeme) {
                this.aktifSiparis.islemler[islemIndex].birimFiyat =
                    this.aktifSiparis.islemler[islemIndex].malzeme && this.aktifSiparis.islemler[islemIndex].malzeme.birimFiyat
                        ? this.aktifSiparis.islemler[islemIndex].malzeme.birimFiyat
                        : 0;
            },
            firmaEkleAc() {
                // Firma adı, firma sorumlusu, firma telefon
                Swal.fire({
                    title: "Firma Ekle",
                    html: `
                        <div class="container">
                            <div class="row g-3">
                                <div class="form-group col-12">
                                    <input type="text" class="form-control" id="firmaAdi" placeholder="Firma Adı *">
                                </div>
                                <div class="form-group col-12">
                                    <input type="text" class="form-control" id="firmaSorumlusu" placeholder="Firma Sorumlusu *">
                                </div>
                                <div class="form-group col-12">
                                    <input type="text" class="form-control" id="firmaTelefon" placeholder="Firma Telefon">
                                    <small class="text-muted">Başında sıfır olmadan 10 haneli olarak giriniz. (Örn: 5554443322)</small>
                                </div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Ekle',
                    cancelButtonText: 'İptal',
                })
                .then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        const firmaAdi = document.getElementById("firmaAdi").value;
                        const sorumluKisi = document.getElementById("firmaSorumlusu").value;
                        const telefon = document.getElementById("firmaTelefon").value;

                        this.yukleniyorObjesi.firmaEkle = true;
                        axios.post("/firmaEkle", {
                            firma: {
                                firmaAdi,
                                sorumluKisi,
                                telefon,
                            },
                        })
                        .then(response => {
                            this.yukleniyorObjesi.firmaEkle = false;
                            if (!response.data.durum) {
                                return this.uyariAc({
                                    baslik: 'Hata',
                                    mesaj: response.data.mesaj,
                                    tur: "error"
                                });
                            }

                            this.uyariAc({
                                baslik: 'Başarılı',
                                mesaj: response.data.mesaj,
                                tur: "success",
                                ozellikler: {
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 2000
                                }
                            });

                            this.firmalariGetir().then(() => {
                                if (!this.aktifSiparis.firma) {
                                    this.aktifSiparis.firma = response.data.firma;

                                    this.aktifSiparis = _.cloneDeep(this.aktifSiparis);
                                }
                            });
                        })
                        .catch(error => {
                            this.yukleniyorObjesi.firmaEkle = false;
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                                tur: "error"
                            });
                            console.log(error);
                        });
                    } else if (result.isDenied) {}
                });
            },
            malzemeEkleAc(islemIndex) {
                // Malzeme adı, malzeme fiyat
                Swal.fire({
                    title: "Malzeme Ekle",
                    html: `
                        <div class="container">
                            <div class="row g-3">
                                <div class="form-group col-12">
                                    <input type="text" class="form-control" id="malzemeAdi" placeholder="Malzeme Adı *">
                                </div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Ekle',
                    cancelButtonText: 'İptal',
                })
                .then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        const malzemeAdi = document.getElementById("malzemeAdi").value;

                        this.yukleniyorObjesi.malzemeEkle = true;
                        axios.post("/malzemeEkle", {
                            malzeme: {
                                malzemeAdi,
                            },
                        })
                        .then(response => {
                            this.yukleniyorObjesi.malzemeEkle = false;
                            if (!response.data.durum) {
                                return this.uyariAc({
                                    baslik: 'Hata',
                                    mesaj: response.data.mesaj,
                                    tur: "error"
                                });
                            }

                            this.uyariAc({
                                baslik: 'Başarılı',
                                mesaj: response.data.mesaj,
                                tur: "success",
                                ozellikler: {
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 2000
                                }
                            });

                            this.malzemeleriGetir().then(() => {
                                if (!this.aktifSiparis.islemler[islemIndex].malzeme) {
                                    this.aktifSiparis.islemler[islemIndex].malzeme = response.data.malzeme;

                                    this.malzemeSecildiginde(islemIndex);

                                    this.aktifSiparis = _.cloneDeep(this.aktifSiparis);
                                }
                            });
                        })
                        .catch(error => {
                            this.yukleniyorObjesi.malzemeEkle = false;
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                                tur: "error"
                            });
                            console.log(error);
                        });
                    } else if (result.isDenied) {}
                });
            },
            islemTuruEklemeAc(islemIndex) {
                // İşlem türü adı
                Swal.fire({
                    title: "İşlem Türü Ekle",
                    html: `
                        <div class="container">
                            <div class="row g-3">
                                <div class="form-group col-12">
                                    <input type="text" class="form-control" id="islemTuruAdi" placeholder="İşlem Türü Adı *">
                                </div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Ekle',
                    cancelButtonText: 'İptal',
                })
                .then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        const islemTuruAdi = document.getElementById("islemTuruAdi").value;

                        this.yukleniyorObjesi.islemTuruEkle = true;
                        axios.post("/islemTuruEkle", {
                            islemTuru: {
                                islemTuruAdi,
                            },
                        })
                        .then(response => {
                            this.yukleniyorObjesi.islemTuruEkle = false;
                            if (!response.data.durum) {
                                return this.uyariAc({
                                    baslik: 'Hata',
                                    mesaj: response.data.mesaj,
                                    tur: "error"
                                });
                            }

                            this.uyariAc({
                                baslik: 'Başarılı',
                                mesaj: response.data.mesaj,
                                tur: "success",
                                ozellikler: {
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 2000
                                }
                            });

                            this.islemTurleriGetir().then(() => {
                                if (!this.aktifSiparis.islemler[islemIndex].yapilacakIslem) {
                                    this.aktifSiparis.islemler[islemIndex].yapilacakIslem = response.data.islemTuru;

                                    this.aktifSiparis = _.cloneDeep(this.aktifSiparis);
                                }
                            });
                        })
                        .catch(error => {
                            this.yukleniyorObjesi.islemTuruEkle = false;
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                                tur: "error"
                            });
                            console.log(error);
                        });
                    } else if (result.isDenied) {}
                });
            },
            resimSecimAc(islemIndex) {
                const islem = this.aktifSiparis.islemler[islemIndex];
                let resimInputEl, onizlemeEl, resim;
                const swalObjesi = {
                    html: `
                        <div class="container">
                            <div class="row g-3">
                                <div class="form-group col-12">
                                    <input type="file" class="form-control" id="resimSecimi" placeholder="Resim Seçimi">
                                </div>
                                <!-- previewer -->
                                <div class="col-12">
                                    <div class="img-previewer">
                                        <img
                                            id="onizleme"
                                            class="img-fluid"
                                            src="${
                                                islem.resim
                                                    ? islem.resim
                                                    : islem.resimYolu
                                                        ? islem.resimYolu
                                                        : this.varsayilanResimYolu
                                            }"
                                            alt="İşlem resmi"
                                            width="400"
                                            height="400"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Ekle',
                    cancelButtonText: 'İptal',
                    didOpen: (val) => {
                        resimInputEl = document.getElementById("resimSecimi");
                        onizlemeEl = document.getElementById("onizleme");

                        resimInputEl.addEventListener("change", () => {
                            resim = resimInputEl.files[0];
                            const reader = new FileReader();

                            reader.onload = (e) => {
                                onizlemeEl.src = e.target.result;
                            };

                            reader.readAsDataURL(resim);
                        });
                    },
                    preConfirm: () => {
                        return {
                            resim: onizlemeEl.src,
                        };
                    },
                };

                // Resim seçme
                Swal.fire(swalObjesi)
                .then((result) => {
                    if (result.isConfirmed && result.value.resim) {
                        islem.resim = result.value.resim;
                        islem.yeniResimSecildi = true;
                        this.aktifSiparis = _.cloneDeep(this.aktifSiparis);
                    }
                });
            },
            ciktiAl() {
                const baslangicDurum = !!this.aktifSiparis.onizlemeModu;

                this.aktifSiparis.onizlemeModu = true;
                this.aktifSiparis = _.cloneDeep(this.aktifSiparis);
                this.$nextTick(() => {
                    html2canvas(document.getElementById("onizlemeGorunumu")).then(canvas => {
                        var a = document.createElement("a");
                        a.href = canvas.toDataURL("image/png");
                        a.download = this.aktifSiparis.siparisAdi + ".png";
                        a.click();
                        this.aktifSiparis.onizlemeModu = baslangicDurum;
                    });
                });
            },
            moduDegistir() {
                if (!this.aktifSiparis.firma || !this.aktifSiparis.firma.id) {
                    return this.uyariAc({
                        baslik: 'Hata',
                        mesaj: "Firma seçmeden siparişi önizleyemezsiniz.",
                        tur: "error"
                    });
                }

                this.aktifSiparis.onizlemeModu = !this.aktifSiparis.onizlemeModu;

                this.aktifSiparis = _.cloneDeep(this.aktifSiparis);
            },
            filtrele() {
                this.siparisleriGetir();
            },
            filtrelemeTarihTemizle() {
                this.filtrelemeObjesi.baslangicTarihi = null;
                this.filtrelemeObjesi.bitisTarihi = null;
            },
        }
    };
</script>
@endsection

@section('style')
    <style>
    </style>
@endsection
