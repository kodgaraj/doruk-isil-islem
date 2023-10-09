@extends('layout')
@section('content')
<div class="row doruk-content">
    <div class="d-inline-flex">
        <h4 style="color:#999">
            <i class="fab fa-wpforms"> </i>
            SİPARİŞ FORMU
        </h4>
        <div class="ms-1">
            <button @click="sorguParametreleriTemizle" v-if="sorguParametreleri.siparisId" class="btn btn-danger btn-sm">
                <b>Sipariş ID: @{{ sorguParametreleri.siparisId }}</b>
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <template v-if="aktifSayfa.kod === 'ANASAYFA'">
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
                                            @input="gecikmeliFonksiyon.varsayilan()"
                                        />
                                        <span @click="filtrele()" class="input-group-text waves-effect" id="arama">
                                            <i class="mdi mdi-magnify"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-auto ps-0">
                                    <!-- Filtreleme butonu -->
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#filtrelemeModal">
                                        <i class="fa fa-filter" data-bs-toggle="tooltip" data-bs-placement="top" title="Filtreleme Aç"></i>
                                    </button>
                                </div>

                                <div class="col-auto">
                                    @can("siparis_kaydetme")
                                        <button @click="siparisEklemeAc" class="btn btn-primary btn-sm"><i class="fas fa-plus" data-bs-toggle="tooltip" data-bs-placement="top" title="Sipariş Ekle"></i> SİPARİŞ EKLE</button>
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
                                                <div class="col m-0">
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
                                                <div class="col-auto m-0">
                                                    <div class="form-group">
                                                        <label for="sayfalamaSayisi">Sayfalama</label>
                                                        <v-select
                                                            v-model="sayfalamaSayisi"
                                                            :options="sayfalamaSayilari"
                                                            id="sayfalamaSayisi"
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
                                                <div class="col m-0">
                                                    <div class="form-group">
                                                        <div class="row d-flex align-items-center justify-space-between">
                                                            <div class="col">
                                                                <label for="faturaTarihFiltre">Fatura Tarihi</label>
                                                            </div>
                                                            <div class="col-auto">
                                                                <button
                                                                    v-if="filtrelemeObjesi.faturaBaslangicTarihi || filtrelemeObjesi.faturaBitisTarihi"
                                                                    @click="filtrelemeFaturaTarihTemizle()"
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
                                                                v-model="filtrelemeObjesi.faturaBaslangicTarihi"
                                                                type="date"
                                                                class="form-control"
                                                                placeholder="Başlangıç"
                                                                data-date-container='#datepicker2'
                                                                data-provide="datepicker"
                                                                data-date-autoclose="true"
                                                                id="faturaTarih"
                                                                aria-label="Başlangıç"
                                                            />
                                                            <span class="input-group-text">Bitiş</span>
                                                            <input
                                                                v-model="filtrelemeObjesi.faturaBitisTarihi"
                                                                type="date"
                                                                class="form-control"
                                                                placeholder="Bitiş"
                                                                data-date-container='#datepicker2'
                                                                data-provide="datepicker"
                                                                data-date-autoclose="true"
                                                                id="faturaTarih"
                                                                aria-label="Bitiş"
                                                            />
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col m-0">
                                                        <div class="form-group">
                                                            <div class="row d-flex align-items-center justify-space-between">
                                                                <div class="col">
                                                                    <label for="siparisDurumu">Sipariş Durumu</label>
                                                                </div>
                                                            </div>
                                                            <div class="input-group mb-3">
                                                                <select class="form-control select2" v-model="filtrelemeObjesi.siparisDurumu">
                                                                    <optgroup label="Sipariş Durumu">
                                                                        <option value="">
                                                                            Tümü
                                                                        </option>
                                                                        <option
                                                                            v-for="(durum, index) in siparisDurumlari"
                                                                            :value="durum.id"
                                                                            :key="index"
                                                                        >
                                                                            @{{ durum.ad }}
                                                                        </option>
                                                                    </optgroup>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto m-0">
                                                        <div class="form-group">
                                                        <div class="row d-flex align-items-center justify-space-between">
                                                            <div class="col">
                                                                <label for="siparisDurumu">Fatura Durumu</label>
                                                            </div>
                                                        </div>
                                                        <div class="input-group mb-3">
                                                            <select class="form-control select2" v-model="filtrelemeObjesi.faturaDurumu">
                                                                <optgroup label="Fatura Durumu">
                                                                    <option value="">
                                                                        Tümü
                                                                    </option>
                                                                    <option value="1">
                                                                        Kesildi
                                                                    </option>
                                                                    <option value="0">
                                                                        Kesilmedi
                                                                    </option>
                                                                </optgroup>
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="row m-0">
                                                    <div class="form-group">
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox" id="tutar" class="form-check-input" v-model="filtrelemeObjesi.tutar">
                                                            <label for="tutar" class="form-check-label" >
                                                                <span class="text-primary">Toplam Tutar "0₺" Olanlar</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-success" data-bs-dismiss="modal" @click="excelCikti()">
                                                <i class="fas fa-file-excel" data-bs-toggle="tooltip" data-bs-placement="top" title="Excell İndir"></i>
                                                EXCEL
                                            </button>
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
                                                        <th>ID/Termin</th>
                                                        <th data-priority="2">Firma</th>
                                                        <th data-priority="3" class="text-center">İşlem Sayısı</th>
                                                        <th data-priority="2">Miktar (Net)</th>
                                                        @can("siparis_ucreti_goruntuleme")
                                                            <th data-priority="4">Tutar</th>
                                                        @endcan
                                                        <th data-priority="5">Sipariş Tarihi</th>
                                                        <th data-priority="5">Fatura Tarihi</th>
                                                        <th data-priority="6" class="text-center">İşlemler</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template v-for="(siparis, index) in siparisler.data">
                                                        <tr
                                                            :key="index + 'siparis'"
                                                            @click="hizliDetayAc(index)"
                                                            style="cursor: pointer"
                                                            :class="!siparis.faturaKesildi && siparis.bitisTarihi ? 'table-warning' : ''"
                                                        >
                                                            <td class="kisa-uzunluk">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        # @{{ siparis.siparisId }}
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <span class="badge badge-pill" style="color: black" :class="`bg-${ siparis.gecenSureRenk }`">@{{ siparis.gecenSure }} Gün</span>
                                                                        <div class="d-inline-flex" v-if="siparis.islemYukleniyor">
                                                                            <div class="spinner-grow text-primary m-1 spinner-grow-sm" role="status">
                                                                                <span class="sr-only">Yükleniyor...</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <span class="badge badge-pill bg-primary">@{{ siparis.siparisNo }}</span>
                                                                    </div>
                                                                </div>
                                                            </td>
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
                                                            <td class="kisa-uzunluk text-center">@{{ siparis.islemSayisi }}</td>
                                                            <td class="kisa-uzunluk">@{{ siparis.netYazi }}</td>
                                                            @can("siparis_ucreti_goruntuleme")
                                                                <td class="kisa-uzunluk">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            @{{ siparis.tutarTLYazi ? siparis.tutarTLYazi : "-" }}
                                                                        </div>
                                                                        <div class="col-12" v-if="siparis.tutarUSD">
                                                                            @{{ siparis.tutarUSDYazi }}
                                                                        </div>
                                                                        <div class="col-12" v-if="siparis.tutarEURO">
                                                                            @{{ siparis.tutarEUROYazi }}
                                                                        </div>

                                                                    </div>
                                                                </td>
                                                            @endcan
                                                            <td class="kisa-uzunluk">@{{ m(siparis.tarih).format("L") }}</td>
                                                            <td class="kisa-uzunluk">
                                                                @can("fatura_kesildi_listeleme")
                                                                    <div class="col-12">
                                                                        <span :class="siparis.faturaKesildi ? 'text-success' : 'text-danger'">
                                                                            Fatura: <i class="fas" :class="siparis.faturaKesildi ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                                                        </span>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        @{{ siparis.faturaTarihi ? m(siparis.faturaTarihi).format("L") : "-" }}
                                                                    </div>
                                                                @endcan
                                                            </td>
                                                            <td class="uzun-uzunluk text-center">
                                                                <div class="btn-group row d-inline-flex g-1">
                                                                    <div class="col">
                                                                        <button @click.stop="siparisDetayAc(siparis)" class="btn btn-primary btn-sm"><i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="Sipariş Detay"></i></button>
                                                                    </div>

                                                                    @can("siparis_duzenleme")
                                                                        <div class="col">
                                                                            <button @click.stop="siparisDuzenle(siparis)" class="btn btn-warning btn-sm"><i class="fas fa-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Sipariş Düzenle"></i></button>
                                                                        </div>
                                                                    @endcan

                                                                    @can("siparis_silme")
                                                                        <div class="col">
                                                                            <button @click.stop="siparisSil(siparis)" class="btn btn-danger btn-sm"><i class="fas fa-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Sipariş Sil"></i></button>
                                                                        </div>
                                                                    @endcan
                                                                    <div class="col-12">
                                                                        <span class="badge badge-pill bg-success">Son Düzenleyen: @{{ siparis.duzenleyen }}</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr
                                                            v-if="_.size(siparis.islemler) && siparis.islemlerAcik"
                                                            class="py-0"
                                                            :key="index + 'formIslemleri'"
                                                        >
                                                            <td
                                                                colspan="100%"
                                                                class="text-center p-0"
                                                                style="border: 1px solid blue;"
                                                            >
                                                                <div
                                                                    class="table-responsive"
                                                                    :key="index + 'islemler'"
                                                                    style="max-height: 400px"
                                                                >
                                                                    <table class="table table-bordered nowrap" id="urun-detay">
                                                                        <thead>
                                                                            <th>Sıra No</th>
                                                                            <th class="text-center">Resim</th>
                                                                            <th>Malzeme</th>
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
                                                                        </thead>
                                                                        <tbody id="islem-satir-ekle">
                                                                            <tr v-for="(islem, iIndex) in siparis.bolunmusToplamliIslemler">
                                                                                <td class="kisa-uzunluk">
                                                                                    <div class="col-12">
                                                                                        @{{ iIndex + 1 }}
                                                                                    </div>
                                                                                    <div class="col-12" v-if="islem.islemTermini">
                                                                                        <span class="badge badge-pill" style="color: black" :class="`bg-${ siparis.gecenSureRenk }`">@{{ islem.islemTermini }} Gün</span>

                                                                                    </div>
                                                                                    <div class="col-12">
                                                                                        <span class="badge badge-pill bg-primary"><a href="/isil-islemler" style="color:white">@{{ islem.formId }}</a></span>
                                                                                    </div>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <img
                                                                                        :src="islem.resimYolu ? islem.resimYolu : varsayilanResimYolu"
                                                                                        class="kg-resim-sec"
                                                                                        @click.stop="resimOnizlemeAc(islem.resimYolu)"
                                                                                    />
                                                                                </td>
                                                                                <td class="orta-uzunluk">@{{ islem.malzeme ? islem.malzeme.ad : "-" }}</td>
                                                                                <td>@{{ islem.adet ? islem.adet : "0" }}</td>
                                                                                <td>@{{ islem.miktarYazi ? islem.miktarYazi : "0" }}</td>
                                                                                <td class="orta-uzunluk" :style="islem.daraSonraGirilecek ? 'background-color: #EB1D3666; color: white !important' : ''">
                                                                                    <div class="col-12">
                                                                                        @{{ islem.daraYazi ? islem.daraYazi : "0" }}
                                                                                    </div>
                                                                                    <div class="col-12" v-if="islem.daraSonraGirilecek">
                                                                                        <small class="text-white">
                                                                                            Dara bilgisi sonra girilecek
                                                                                        </small>
                                                                                    </div>
                                                                                </td>
                                                                                <td class="kisa-uzunluk text-center">
                                                                                    <b><h5>@{{ islem.netYazi ? islem.netYazi : "0" }}</h5></b>
                                                                                </td>
                                                                                @can("siparis_ucreti_goruntuleme")
                                                                                    <td>@{{ islem.birimFiyatYazi }}</td>
                                                                                @endcan
                                                                                <td>@{{ islem.kalite ? islem.kalite : "-" }}</td>
                                                                                <td>@{{ islem.yapilacakIslem ? islem.yapilacakIslem.ad : "-" }}</td>
                                                                                <td>@{{ islem.istenilenSertlik ? islem.istenilenSertlik : "-" }}</td>
                                                                                <td>@{{ islem.islemDurumu ? islem.islemDurumu.ad : "-" }}</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer">
                                            <div class="row d-flex align-items-center justify-content-between">
                                                <div class="col-auto"></div>
                                                <div class="col">
                                                    <ul class="pagination pagination-rounded justify-content-center mb-0">
                                                        <li class="page-item">
                                                            <button class="page-link" :disabled="!siparisler.prev_page_url" @click="siparisleriGetir(siparisler.prev_page_url)">
                                                                <i class="fas fa-angle-left"></i>
                                                            </button>
                                                        </li>
                                                        <li
                                                            v-for="sayfa in sayfalamaAyarla(siparisler.last_page, siparisler.current_page)"
                                                            class="page-item"
                                                            :class="[sayfa.aktif ? 'active' : '']"
                                                        >
                                                            <button class="page-link" @click="sayfa.tur === 'SAYFA' ? siparisleriGetir('/siparisler?page=' + sayfa.sayfa) : ()  => {}">@{{ sayfa.sayfa }}</button>
                                                        </li>
                                                        <li class="page-item">
                                                            <button class="page-link" :disabled="!siparisler.next_page_url" @click="siparisleriGetir(siparisler.next_page_url)">
                                                                <i class="fas fa-angle-right"></i>
                                                            </button>
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
                <template v-else-if="aktifSayfa.kod === 'YENI_SIPARIS'">
                    <div class="row gap-2 gap-sm-0">
                        <div class="col-12 col-md-8">
                            <div class="d-flex flex-row align-items-center">
                                <button @click="geri" class="btn btn-warning"><i class="fas fa-arrow-left"></i> GERİ</button>
                                <h4 class="card-title m-0 ms-2">
                                    <template v-if="aktifSiparis.siparisId">
                                        @{{ aktifSiparis.siparisAdi }}
                                        <div class="col-12" v-if="aktifSiparis.duzenleyen">
                                            <span class="badge badge-pill bg-success">Son Düzenleyen: @{{ aktifSiparis.duzenleyen }}</span>
                                        </div>
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
                        <div class="col-12 col-md-4 text-end">
                            @can("siparis_raporu_olusturma")
                                <button @click="raporOlusturAc" class="btn btn-outline-primary" v-if="aktifSiparis.siparisId">
                                    <i class="fas fa-chart-line" data-bs-toggle="tooltip" data-bs-placement="top" title="Rapor Oluştur"></i>
                                </button>

                                <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="raporlamaModal" tabindex="-1" aria-labelledby="raporlamaModal" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-lg-down modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">İşlem Raporlama</h5>
                                                <button type="button" class="btn-close" @click="raporOlusturKapat" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row gap-3">
                                                    <div class="col-12 m-0">
                                                        <ul class="list-group text-start">
                                                            <li
                                                                v-for="(islem, index) in siparisRaporlama.islemler"
                                                                :class="{
                                                                    'list-group-item-primary': siparisRaporlama.islem && siparisRaporlama.islem.id == islem.id,
                                                                    'list-group-item-danger': (!siparisRaporlama.islem || siparisRaporlama.islem.id != islem.id) && islem.islemDurumu.kod !== 'TAMAMLANDI',
                                                                }"
                                                                :aria-current="siparisRaporlama.islem && siparisRaporlama.islem.id == islem.id"
                                                                :key="index"
                                                                class="list-group-item list-group-item-action"
                                                                style="cursor: pointer"
                                                                @click="raporIslemSec(islem)"
                                                            >
                                                                <div class="d-flex">
                                                                    <div class="col-2">
                                                                        <img
                                                                            :src="islem.resimYolu ? islem.resimYolu : varsayilanResimYolu"
                                                                            class="kg-resim-sec"
                                                                            @click.stop="resimOnizlemeAc(islem.resimYolu)"
                                                                        />
                                                                    </div>
                                                                    <div class="col-10">
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                @{{ islem.malzeme.ad }} - @{{ islem.netYazi }}
                                                                            </div>
                                                                            <div class="col-12 text-muted">
                                                                                @{{ islem.islemDurumu.ad }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>

                                                        <hr />

                                                        <template v-if="siparisRaporlama.islem">
                                                            <div class="row">

                                                            <div class="col-3 m-0 mb-2 text-start">
                                                                <label class="form-label" for="gelisTarihi">Geliş Tarihi</label>
                                                                <input
                                                                    v-model="siparisRaporlama.islem.gelisTarihi"
                                                                    type="date"
                                                                    class="form-control"
                                                                    data-date-container='#datepicker2'
                                                                    data-provide="datepicker"
                                                                    data-date-autoclose="true" id="gelisTarihi"
                                                                    @input="gecikmeliFonksiyon.siparisRaporlama('GELIS')"
                                                                />
                                                            </div>
                                                            <div class="col-3 m-0 mb-2 text-start">
                                                                <label class="form-label">Ürün Kalitesi</label>
                                                                <input
                                                                    v-model="siparisRaporlama.islem.kalite"
                                                                    class="form-control"
                                                                    @input="gecikmeliFonksiyon.siparisRaporlama('KALITE')"
                                                                />
                                                            </div>
                                                            <div class="col-3 m-0 mb-2 text-start">
                                                                <label class="form-label">İstenen Sertlik</label>
                                                                <input
                                                                    v-model="siparisRaporlama.islem.istenilenSertlik"
                                                                    class="form-control"
                                                                    @input="gecikmeliFonksiyon.siparisRaporlama('SERTLIK')"
                                                                />
                                                            </div>
                                                            <div class="col-3 m-0 mb-2 text-start">
                                                                <label class="form-label">Son Sertlik</label>
                                                                <input
                                                                    v-model="siparisRaporlama.islem.sonSertlik"
                                                                    class="form-control"
                                                                    @input="gecikmeliFonksiyon.siparisRaporlama('OLCUM')"
                                                                />
                                                            </div>
                                                            <div class="col-12 m-0 mb-2 text-start">
                                                                <label class="form-label">Yapılacak İşlem</label>
                                                                <v-select
                                                                    v-model="siparisRaporlama.islem.yapilacakIslem"
                                                                    :options="islemTurleri"
                                                                    label="ad"
                                                                    @input="gecikmeliFonksiyon.siparisRaporlama('NOT')"
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

                                                            <div class="col-4 m-0 text-start">
                                                                <label class="form-label">Ölçümler</label>
                                                                <input
                                                                    v-model="siparisRaporlama.islem.olcumler"
                                                                    class="form-control"
                                                                    placeholder="Örn: 65, 72, 70"
                                                                    @input="gecikmeliFonksiyon.siparisRaporlama('OLCUM')"
                                                                />
                                                                <small class="text-muted">
                                                                    Virgülle ayrılmış şekilde yazınız... (Örn: 65, 72, 70)
                                                                </small>
                                                            </div>
                                                            <div class="col-4 m-0 text-start">
                                                                <label class="form-label">Ölçüm Adı "X" </label>
                                                                <input
                                                                    v-model="siparisRaporlama.grafikOptions.xaxis.title.text"
                                                                    class="form-control"
                                                                    placeholder="Ölçüm No"
                                                                    @input="gecikmeliFonksiyon.siparisRaporlama('XAXIS')"
                                                                />

                                                            </div>
                                                            <div class="col-4 m-0 text-start">
                                                                <label class="form-label">Ölçüm Adı "Y"</label>
                                                                <input
                                                                    v-model="siparisRaporlama.grafikOptions.yaxis.title.text"
                                                                    class="form-control"
                                                                    placeholder="Sertlik"
                                                                    @input="gecikmeliFonksiyon.siparisRaporlama('YAXIS')"
                                                                />

                                                            </div>
                                                            <div class="col-12 m-0 mb-2 text-start">
                                                                <label class="form-label">Not</label>
                                                                <textarea
                                                                    v-model="siparisRaporlama.islem.not"
                                                                    class="form-control"
                                                                    @input="gecikmeliFonksiyon.siparisRaporlama('NOT')"
                                                                ></textarea>
                                                            </div>


                                                            </div>
                                                        </template>

                                                        <div class="overflow-auto">
                                                            <div
                                                                v-show="siparisRaporlama.islem"
                                                                v-html="siparisRaporlama.html"
                                                                :key="siparisRaporlama.islem ? siparisRaporlama.islem.id : 'yok'"
                                                            ></div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer justify-content-between">
                                                <button type="button" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="Yukarıdaki alanlara girilen değerler rapora yansımadıysa bu butona basarak verileri senkronize edebilirsiniz." @click="raporAlanlariDoldur">SENKRONİZE ET</button>
                                                <button type="button" class="btn btn-danger" @click="raporOlusturKapat">VAZGEÇ</button>
                                                <button type="button" class="btn btn-primary" @click="raporOlustur" :disabled="!siparisRaporlama.islem || !siparisRaporlama.islem.id">RAPOR OLUŞTUR</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                            @can("siparis_duzenleme")
                                <button @click="moduDegistir" class="btn btn-outline-info">
                                    <i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="Önizleme Moduna Geç" v-if="!aktifSiparis.onizlemeModu"></i>
                                    <i class="fas fa-eye-slash" data-bs-toggle="tooltip" data-bs-placement="top" title="Önizleme Modundan Çık" v-else></i>
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
                                    <i class="fas fa-save" data-bs-toggle="tooltip" data-bs-placement="top" title="Siparişi Kaydet"></i> KAYDET
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
                            {{-- <div class="col-12 col-sm-6 col-md-4 mb-2">
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
                            </div> --}}
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
                            @can("siparis_ucreti_goruntuleme")
                                <div class="col-4 mb-2">
                                    <template v-if="aktifSiparis.onizlemeModu">
                                        <div class="form-group">
                                            <label for="toplamTutar">Toplam TL (₺)</label>
                                            <h5 id="toplamTutar">@{{ aktifSiparis.tutarTLYazi }} ₺</h5>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <label class="form-label">Toplam TL (₺)</label>
                                        <input
                                            v-model.lazy="aktifSiparis.tutarTLYazi"
                                            v-money="maskeler.tl"
                                            class="form-control"
                                            placeholder="Toplam tutarını giriniz..."
                                            disabled
                                        />
                                    </template>
                                </div>
                                <div class="col-4 mb-2">
                                    <template v-if="aktifSiparis.onizlemeModu">
                                        <div class="form-group">
                                            <label for="toplamTutar">Toplam USD ($)</label>
                                            <h5 id="toplamTutar">@{{ aktifSiparis.tutarUSDYazi }} $</h5>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <label class="form-label">Toplam USD ($)</label>
                                        <input
                                            v-model.lazy="aktifSiparis.tutarUSDYazi"
                                            v-money="maskeler.usd"
                                            class="form-control"
                                            placeholder="Toplam tutarını giriniz..."
                                            disabled
                                        />
                                    </template>
                                </div>
                                <div class="col-4 mb-2">
                                    <template v-if="aktifSiparis.onizlemeModu">
                                        <div class="form-group">
                                            <label for="toplamTutar">Toplam EURO (€)</label>
                                            <h5 id="toplamTutar">@{{ aktifSiparis.tutarEUROYazi }} $</h5>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <label class="form-label">Toplam EURO (€)</label>
                                        <input
                                            v-model.lazy="aktifSiparis.tutarEUROYazi"
                                            v-money="maskeler.euro"
                                            class="form-control"
                                            placeholder="Toplam tutarını giriniz..."
                                            disabled
                                        />
                                    </template>
                                </div>
                            @endcan
                            <div class="col-6 col-sm-6 col-md-4 mb-2">
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
                            @can("fatura_kesildi_listeleme")
                                <div class="mb-3 col-12 col-sm-6 col-md-4 align-self-center">
                                    <template v-if="aktifSiparis.onizlemeModu">
                                        <span v-if="aktifSiparis.faturaKesildi" class="text-success">
                                            <i class="fas fa-check-circle"></i>
                                            Fatura kesildi
                                        </span>
                                        <span v-else class="text-danger">
                                            <i class="fas fa-times-circle"></i>
                                            Fatura kesilmedi
                                        </span>
                                    </template>
                                    <template v-else>
                                        <div class="row d-flex align-items-end">
                                            <div class="form-group">
                                                <div class="form-check form-switch">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        id="faturaKesildi"
                                                        v-model="aktifSiparis.faturaKesildi"
                                                    />
                                                    <label class="form-check-label" for="faturaKesildi">
                                                        <span v-if="aktifSiparis.faturaKesildi" class="text-success">Fatura kesildi</span>
                                                        <span v-else class="text-danger">Fatura kesilmedi</span>
                                                    </label>
                                                    <span v-if="aktifSiparis.faturaTarihi">@{{aktifSiparis.faturaTarihi}}</span>
                                                    {{-- <input v-if="aktifSiparis.faturaKesildi" type="datetime-local" class="form-control" v-model="aktifSiparis.faturaTarihi" /> --}}
                                                </div>
                                                <div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            @endcan
                            <div class="col-12 col-sm-6 col-md-4" v-if="aktifSiparis.siparisId">
                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="islemDuzenle"
                                            v-model="aktifSiparis.islemDuzenle"
                                            @click="siparisDurumDegisti()"
                                        />
                                        <label class="form-check-label" for="islemDuzenle">
                                            <span  class="text-primary">İşlem Düzenle</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
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
                        <div id="onizlemeScroll" class="mb-3 row overflow-auto">
                            <div class="col-12">
                              <label for="aciklama">İşlem Detayları</label>
                                <table class="table table-bordered nowrap" id="urun-detay">
                                    <thead>
                                        <th>Sıra No</th>
                                        <th class="text-center">Resim</th>
                                        <th>Malzeme*</th>
                                        <th class="text-center">Adet</th>
                                        <th class="text-center">Miktar (KG)</th>
                                        <th class="text-center">Dara (KG)</th>
                                        <th class="text-center">Net (KG)</th>
                                        @can("siparis_ucreti_goruntuleme")
                                            <th class="text-center">Tutar</th>
                                            <th v-if="('islemDuzenle' in aktifSiparis) && aktifSiparis.islemDuzenle">Para Birimi</th>
                                        @endcan
                                        <th>Kalite</th>
                                        <th>Yapılacak İşlem</th>
                                        <th>İstenilen Sertlik</th>
                                        <th>İşlem Durumu</th>
                                        <th v-if="('islemDuzenle' in aktifSiparis) && aktifSiparis.islemDuzenle">İşlemler</th>
                                    </thead>
                                    <tbody id="islem-satir-ekle">
                                        <template v-if="!('islemDuzenle' in aktifSiparis) || !aktifSiparis.islemDuzenle ">
                                            <tr v-for="(islem, index) in aktifSiparis.bolunmusToplamliIslemler">
                                                <td>@{{ index + 1 }}</td>
                                                <td class="text-center">
                                                    <img
                                                        :src="islem.resimYolu ? islem.resimYolu : varsayilanResimYolu"
                                                        class="kg-resim-sec"
                                                        @click.stop="resimOnizlemeAc(islem.resimYolu)"
                                                    />
                                                </td>
                                                <td class="en-uzun-uzunluk">@{{ islem.malzeme ? islem.malzeme.ad : "-" }}</td>
                                                <td class="kisa-uzunluk text-center">@{{ islem.adet ? islem.adet : "0" }}</td>
                                                <td class="orta-uzunluk text-center">@{{ islem.miktarYazi ? islem.miktarYazi : "0" }}</td>
                                                <td class="orta-uzunluk text-center" :style="islem.daraSonraGirilecek ? 'background-color: #EB1D3666; color: white !important' : ''">
                                                    <div class="col-12">
                                                        @{{ islem.daraYazi ? islem.daraYazi : "0" }}
                                                    </div>
                                                    <div class="col-12" v-if="islem.daraSonraGirilecek">
                                                        <small class="text-white">
                                                            Dara bilgisi sonra girilecek
                                                        </small>
                                                    </div>
                                                </td>
                                                <td class="kisa-uzunluk text-center">
                                                    <b><h5>@{{ islem.netYazi ? islem.netYazi : "0" }}</h5></b>
                                                </td>
                                                @can("siparis_ucreti_goruntuleme")
                                                    <td class="orta-uzunluk text-center">@{{ islem.birimFiyatYazi }}</td>
                                                @endcan
                                                <td class="kisa-uzunluk">@{{ islem.kalite ? islem.kalite : "-" }}</td>
                                                <td class="en-uzun-uzunluk">@{{ islem.yapilacakIslem ? islem.yapilacakIslem.ad : "-" }}</td>
                                                <td class="kisa-uzunluk">@{{ islem.istenilenSertlik ? islem.istenilenSertlik : "-" }}</td>
                                                <td class="orta-uzunluk">@{{ islem.islemDurumu ? islem.islemDurumu.ad : "-" }}</td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr v-for="(islem, index) in aktifSiparis.islemler" :key="index" style="vertical-align: middle;">
                                                <td>
                                                    <div class="row d-flex">
                                                        <div class="col-12">
                                                            # @{{ index + 1 }}
                                                        </div>
                                                        <div class="col-12" v-if="islem.id">
                                                            <span class="badge badge-pill bg-primary">ID: @{{ islem.id }}</span>
                                                        </div>
                                                    </div>
                                                </td>
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
                                                            <div class="col-auto ps-0" v-if="!aktifSiparis.onizlemeModu && !islem.malzeme">
                                                                <button
                                                                    class="btn btn-primary"
                                                                    @click="malzemeEkleAc(index)"
                                                                >
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        @endcan
                                                        @can("malzeme_duzenleme")
                                                            <div class="col-auto ps-0" v-else-if="!aktifSiparis.onizlemeModu && islem.malzeme">
                                                                <button
                                                                    class="btn btn-warning"
                                                                    @click="malzemeEkleAc(index, true)"
                                                                >
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            </div>
                                                        @endcan
                                                    </div>
                                                </td>
                                                <td class="kisa-uzunluk">
                                                    <input class="form-control" type="number" placeholder="1" v-model="islem.adet">
                                                </td>
                                                <td class="orta-uzunluk text-center">
                                                    <div class="row d-flex">
                                                        <div class="col-12">
                                                            <input
                                                                class="form-control"
                                                                v-money="maskeler.kg"
                                                                placeholder="Miktar (KG)"
                                                                v-model="islem.miktarYazi"
                                                            />
                                                        </div>
                                                        <div class="col-12" v-if="islem.bolunmusId">
                                                            <span class="badge badge-pill bg-primary">Bölünmüş ID: @{{ islem.bolunmusId }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="orta-uzunluk text-center" :style="islem.daraSonraGirilecek ? 'background-color: #EB1D3666; color: white !important' : ''">
                                                    <input
                                                        class="form-control"
                                                        placeholder="Dara (KG)"
                                                        v-model="islem.daraYazi"
                                                        v-money="maskeler.kg"
                                                        :disabled="islem.daraSonraGirilecek"
                                                    />
                                                    <div class="form-check form-switch">
                                                        <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            value=""
                                                            id="daraSonraGirilecek"
                                                            v-model="islem.daraSonraGirilecek"
                                                            @change="daraSonraGirilecekAyarla(islem)"
                                                        />
                                                        <label class="form-check-label" for="daraSonraGirilecek">
                                                            <small>Sonra girilecek</small>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="kisa-uzunluk text-center">
                                                    <b><h5>@{{ islem.netYazi ? islem.netYazi : "0" }}</h5></b>
                                                </td>
                                                @can("siparis_ucreti_goruntuleme")
                                                    <td class="orta-uzunluk pe-0">
                                                        <input
                                                            v-model="islem.birimFiyatYazi"
                                                            v-money="maskeler[islem.paraBirimi.maske]"
                                                            class="form-control"
                                                            placeholder="Birim Fiyat"
                                                        />
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" value="" id="miktarFiyatCarp" v-model="islem.miktarFiyatCarp">
                                                            <label class="form-check-label" for="miktarFiyatCarp">
                                                                <small>Net x Tutar</small>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="orta-uzunluk ps-0 pe-2">
                                                        <v-select
                                                            v-model="islem.paraBirimi"
                                                            :options="paraBirimleri"
                                                            label="ad"
                                                            class="mb-4"
                                                        ></v-select>
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
                                    <tfoot v-if="('islemDuzenle' in aktifSiparis) && aktifSiparis.islemDuzenle">
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

<div ref="siparisRaporlama" style="background: white; display: none; color: black;">
    <div class="printable-page" id="page-1">
        <div class="col-12">
            <div>
                <img style="width: 100%" src="/img/doruk-belge-baslik.png" />
            </div>
        </div>
        <div class="col-12 text-center">
            <div>
                <h3>KALİTE KONTROL RAPORU</h3>
            </div>
        </div>
        <div class="col-12 text-end">
            <div>
                <h6><b>TARİH:</b> ${ rapor.tarih }</h6>
            </div>
        </div>
        <div class="col-12 text-end">
            <div>
                <h6><b>RAPOR NO:</b> ${ rapor.no }</h6>
            </div>
        </div>
        <div class="px-5">
            <div class="col-12" style="border-bottom: 1px solid #dddddd;">
                <div class="row d-flex align-items-center">
                    <div class="col-4 text-end">
                        <b>GELİŞ TARİHİ:</b>
                    </div>
                    <div class="col-8 text-start">
                        <span>${ rapor.gelisTarihi }</span>
                    </div>
                </div>
            </div>
            <div class="col-12" style="border-bottom: 1px solid #dddddd;">
                <div class="row d-flex align-items-center">
                    <div class="col-4 text-end">
                        <b>FİRMA:</b>
                    </div>
                    <div class="col-8 text-start">
                        <span>${ rapor.firma }</span>
                    </div>
                </div>
            </div>
            <div class="col-12" style="border-bottom: 1px solid #dddddd;">
                <div class="row d-flex align-items-center">
                    <div class="col-4 text-end">
                        <b>ÜRÜN KALİTESİ:</b>
                    </div>
                    <div class="col-8 text-start">
                        <span>${ rapor.urunKalitesi }</span>
                    </div>
                </div>
            </div>
            <div class="col-12" style="border-bottom: 1px solid #dddddd;">
                <div class="row d-flex align-items-center">
                    <div class="col-4 text-end">
                        <b>MALZEME TANIMI:</b>
                    </div>
                    <div class="col-8 text-start">
                        <span>${ rapor.malzeme }</span>
                    </div>
                </div>
            </div>
            <div class="col-12" style="border-bottom: 1px solid #dddddd;">
                <div class="row d-flex align-items-center">
                    <div class="col-4 text-end">
                        <b>ADET:</b>
                    </div>
                    <div class="col-8 text-start">
                        <span>${ rapor.urunAdedi }</span>
                    </div>
                </div>
            </div>
            <div class="col-12" style="border-bottom: 1px solid #dddddd;">
                <div class="row d-flex align-items-center">
                    <div class="col-4 text-end">
                        <b>YAPILAN İŞLEM:</b>
                    </div>
                    <div class="col-8 text-start">
                        <span>${ rapor.yapilanIslem }</span>
                    </div>
                </div>
            </div>
            <div class="col-12" style="border-bottom: 1px solid #dddddd;">
                <div class="row d-flex align-items-center">
                    <div class="col-4 text-end">
                        <b>İSTENEN SERTLİK:</b>
                    </div>
                    <div class="col-8 text-start">
                        <span>${ rapor.istenenSertlik }</span>
                    </div>
                </div>
            </div>
            <div class="col-12" style="border-bottom: 1px solid #dddddd;">
                <div class="row d-flex align-items-center">
                    <div class="col-4 text-end">
                        <b>SON SERTLİK:</b>
                    </div>
                    <div class="col-8 text-start">
                        <span>${ rapor.sonSertlik }</span>
                    </div>
                </div>
            </div>
            <div class="col-12" style="border-bottom: 1px solid #dddddd;">
                <div class="row d-flex align-items-center">
                    <div class="col-4 text-end">
                        <b>NOT:</b>
                    </div>
                    <div class="col-8 text-start">
                        <span>${ rapor.not }</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 text-center mt-4">
            <b>SİPARİŞ FOTOĞRAFI</b>
        </div>
        <div class="col-12 text-center mb-2">
            <img height="400" style="object-fit: contain; width: 190mm;" src="${ rapor.urunFotografi }" />
        </div>
    </div>
    <div class="printable-page" id="page-2">
        <div class="col-12 text-center mt-4">
            <b>ISIL İŞLEM SONRASI ÖLÇÜLEN SERTLİK DEĞERLERİ</b>
        </div>
        <div class="col-12 d-flex justify-content-center">
            <div class="siparis-raporlama-chart"></div>
            {{-- <apexchart
                ref="siparisRaporlamaChart"
                type="line"
                height="400"
                width="400"
                :options="siparisRaporlama.grafikOptions"
                :series="siparisRaporlama.grafikSeries"
            ></apexchart> --}}
        </div>
        <hr />
        <div class="px-5 my-3">
            <div class="col-12">
                <div class="row d-flex align-items-center">
                    <div class="col-6 text-center">
                        <b>Ünal SANDAL</b>
                        <br />
                        <b>Metalurji ve Malzeme Mühendisi</b>
                    </div>
                    <div class="col-6 text-center">
                        <img height="250" width="250" style="object-fit: contain;" src="/img/doruk-unal-imza.jpg" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div>
                <img style="width: 100%" src="/img/doruk-belge-alt-bilgi.jpg" />
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- apexcharts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.3/apexcharts.min.js"></script>
<!-- vue apexcharts -->
<script src="https://unpkg.com/vue-apexcharts"></script>

<script>
    let mixinApp = {
        data: function () {
            return {
                aktifSayfa: {
                    kod: "ANASAYFA",
                    baslik: "Siparişler",
                },
                sayfalar: [
                    {
                        kod: "ANASAYFA",
                        baslik: "Siparişler",
                    },
                    {
                        kod: "YENI_SIPARIS",
                        baslik: "Form Oluştur",
                    },
                ],
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
                    faturaBaslangicTarihi: null,
                    faturaBitisTarihi: null,
                    limit: 10,
                    siparisDurumu: "",
                    faturaDurumu: "",
                    tutar: false,
                },
                sayfalamaSayilari: [10, 25, 50, 100],
                sayfalamaSayisi: 10,
                maskeler: {
                    tl: {
                        prefix: "",
                        thousands: ".",
                        decimal: ",",
                        suffix: " ₺",
                        precision: 2,
                    },
                    usd: {
                        prefix: "",
                        thousands: ".",
                        decimal: ",",
                        suffix: " $",
                        precision: 2,
                    },
                    euro: {
                        prefix: "",
                        thousands: ".",
                        decimal: ",",
                        suffix: " €",
                        precision: 2,
                    },
                    kg: {
                        prefix: "",
                        thousands: ".",
                        decimal: ",",
                        suffix: " kg",
                        precision: 2,
                    },
                },
                paraBirimleri: @json($paraBirimleri),
                sorguParametreleri: {
                    siparisId: null,
                },
                siparisRaporlama: {
                    html: "",
                    modal: null,
                    islem: null,
                    islemler: null,
                    grafikOptions: {
                        chart: {
                            height: 350,
                            type: 'line',
                            dropShadow: {
                                enabled: true,
                                color: '#000',
                                top: 18,
                                left: 7,
                                blur: 10,
                                opacity: 0.2
                            },
                            toolbar: {
                                show: false
                            },
                            zoom: {
                                enabled: false,
                            },
                        },
                        colors: ['#77B6EA', '#545454'],
                        dataLabels: {
                            enabled: true,
                        },
                        stroke: {
                            curve: 'smooth'
                        },
                        grid: {
                            borderColor: '#e7e7e7',
                            row: {
                                colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                                opacity: 0.5
                            },
                        },
                        markers: {
                            size: 1
                        },
                        xaxis: {
                            categories: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
                            title: {
                                text: 'Ölçüm No'
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Sertlik'
                            },
                            min: 0,
                            forceNiceScale: true,
                        },
                        legend: {
                            show: false,
                            // position: 'top',
                            // horizontalAlign: 'right',
                            // floating: true,
                            // offsetY: -25,
                            // offsetX: -5
                        },
                        tooltip: {
                            enabled: false,
                        },
                    },
                    grafikSeries: [
                        {
                            name: "Ölçüm",
                            data: []
                        },
                    ],
                },
            }
        },
        created() {
            Vue.use(VueApexCharts);
            Vue.component('apexchart', VueApexCharts);
        },
        mounted() {
            this.onyukleme();
            this.siparisDurumlariGetir();
        },
        watch: {
            "aktifSiparis.islemler": {
                handler: function (newValue, oldValue) {
                    if (!this.aktifSiparis) return;

                    let tutarTL = 0, tutarUSD = 0, tutarEURO = 0;
                    for (let i in this.aktifSiparis.islemler) {
                        const islem = this.aktifSiparis.islemler[i];

                        islem.birimFiyat = this.floatDonustur(islem.birimFiyatYazi, { paraBirimi: islem.paraBirimi });
                        islem.miktar = this.floatDonustur(islem.miktarYazi, { kg: true });
                        islem.dara = this.floatDonustur(islem.daraYazi, { kg: true });

                        islem.net = _.round(islem.miktar - islem.dara, 2);
                        islem.netYazi = this.yaziyaDonustur(islem.net, { kg: true });

                        const birimFiyat = _.round((islem.miktarFiyatCarp ? islem.net : 1) * islem.birimFiyat, 2);

                        if (islem.paraBirimi.kod == "USD") {
                            tutarUSD += birimFiyat;
                        }
                        else if (islem.paraBirimi.kod == "EURO") {
                            tutarEURO += birimFiyat;
                        }
                        else {
                            tutarTL += birimFiyat;
                        }
                    }

                    tutarTL = _.round(tutarTL, 2);
                    tutarUSD = _.round(tutarUSD, 2);
                    tutarEURO = _.round(tutarEURO, 2);

                    this.aktifSiparis.tutarUSD = tutarUSD;
                    this.aktifSiparis.tutarUSDYazi = this.yaziyaDonustur(tutarUSD);
                    this.aktifSiparis.tutarEURO = tutarEURO;
                    this.aktifSiparis.tutarEUROYazi = this.yaziyaDonustur(tutarEURO);
                    this.aktifSiparis.tutarTL = tutarTL;
                    this.aktifSiparis.tutarTLYazi = this.yaziyaDonustur(tutarTL);
                },
                deep: true
            },
            "aktifSiparis.bolunmusToplamliIslemler": {
                handler: function (newValue, oldValue) {
                    if (!this.aktifSiparis) return;

                    let tutarTL = 0, tutarUSD = 0, tutarEURO = 0;
                    for (let i in this.aktifSiparis.bolunmusToplamliIslemler) {
                        const islem = this.aktifSiparis.bolunmusToplamliIslemler[i];

                        islem.birimFiyat = this.floatDonustur(islem.birimFiyatYazi, { paraBirimi: islem.paraBirimi });
                        islem.miktar = this.floatDonustur(islem.miktarYazi, { kg: true });
                        islem.dara = this.floatDonustur(islem.daraYazi, { kg: true });

                        islem.net = _.round(islem.miktar - islem.dara, 2);
                        islem.netYazi = this.yaziyaDonustur(islem.net, { kg: true });

                        const birimFiyat = _.round((islem.miktarFiyatCarp ? islem.net : 1) * islem.birimFiyat, 2);

                        if (islem.paraBirimi.kod == "USD") {
                            tutarUSD += birimFiyat;
                        }
                        else if (islem.paraBirimi.kod == "EURO") {
                            tutarEURO += birimFiyat;
                        }
                        else {
                            tutarTL += birimFiyat;
                        }
                    }

                    tutarTL = _.round(tutarTL, 2);
                    tutarUSD = _.round(tutarUSD, 2);
                    tutarEURO = _.round(tutarEURO, 2);

                    this.aktifSiparis.tutarUSD = tutarUSD;
                    this.aktifSiparis.tutarUSDYazi = this.yaziyaDonustur(tutarUSD);
                    this.aktifSiparis.tutarEURO = tutarEURO;
                    this.aktifSiparis.tutarEUROYazi = this.yaziyaDonustur(tutarEURO);
                    this.aktifSiparis.tutarTL = tutarTL;
                    this.aktifSiparis.tutarTLYazi = this.yaziyaDonustur(tutarTL);
                },
                deep: true
            }
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
            onyukleme() {
                let url = new URL(window.location.href);
                this.sorguParametreleri.siparisId = _.toNumber(url.searchParams.get("siparisId"));

                if (this.sorguParametreleri.siparisId) {
                    this.filtrelemeObjesi.siparisId = this.sorguParametreleri.siparisId;
                }

                this.gecikmeliFonksiyonCalistir(this.filtrele);
                this.gecikmeliFonksiyonCalistir(this.raporAlanlariGuncelle, {
                    fonksiyonKey: "siparisRaporlama"
                });

                this.siparisleriGetir();
                this.firmalariGetir();
            },
            aktifSayfaDegistir(kod) {
                this.aktifSayfa = _.find(this.sayfalar, { kod });
            },
            siparisleriGetir(url = "/siparisler", cikti = false) {
                this.yukleniyorDurum(true);
                axios.get(url, {
                    params: {
                        cikti,
                        filtreleme: this.filtrelemeObjesi,
                        sayfalamaSayisi: this.sayfalamaSayisi,
                    },
                    responseType: cikti ? 'blob' : 'json',
                })
                .then(async response => {
                    this.yukleniyorDurum(false);

                    if (cikti) {
                        const dosyaAdi = 'Sipariş Listesi ' + moment().format('L LTS');
                        const uzanti = "xlsx";
                        // convert blob
                        const blob = new Blob([response.data]);
                        if (this.isNativeApp) {
                            const base64 = await this.blobToBase64(blob);
                            window.ReactNativeWebView.postMessage(JSON.stringify({
                                kod: "INDIR",
                                dosya: base64,
                                dosyaAdi: dosyaAdi,
                                dosyaUzantisi: uzanti,
                            }));
                            return;
                        }

                        const url = window.URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = dosyaAdi + '.' + uzanti;
                        link.click();

                        window.URL.revokeObjectURL(url);

                        return;
                    }

                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    this.siparisler = response.data.siparisler;

                    this.siparisler = _.cloneDeep(this.siparisler);

                    if (this.sorguParametreleri.siparisId) {
                        this.siparisDetayAc(this.siparisler.data[0]);
                    }
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
                    faturaKesildi: false,
                    islemDuzenle: true,
                };

                this.numaralariGetir();
                this.siparisDurumlariGetir();

                if (!_.size(this.firmalar)) {
                    this.firmalariGetir();
                }

                this.aktifSayfaDegistir("YENI_SIPARIS");
            },
            geri() {
                this.aktifSayfaDegistir("ANASAYFA");
                this.aktifSiparis = null;
            },
            numaralariGetir() {
                console.log("numaralariGetir");
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
                    adet: null,
                    miktar: 0,
                    dara: 0,
                    birimFiyat: 0,
                    kalite: "",
                    yapilacakIslem: null,
                    istenilenSertlik: "",
                    islemDurumu: null,
                    miktarFiyatCarp: true,
                    paraBirimi: _.find(this.paraBirimleri, {
                        kod: "TL"
                    }),
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
            siparisDurumDegisti() {
                if (this.aktifSiparis.islemDuzenle) {
                    this.siparisKaydet("geriGitme");
                }
            },
            siparisKaydet(veri = null) {

                if (!this.aktifSiparis.firma) {
                    return this.uyariAc({
                        baslik: 'Uyarı',
                        mesaj: "Lütfen firma seçiniz!",
                        tur: "warning"
                    });
                }

                if (_.size(this.aktifSiparis.islemler)) {
                    for (let index in this.aktifSiparis.islemler) {
                        const islem = this.aktifSiparis.islemler[index];
                        if (!islem.malzeme) {
                            return this.uyariAc({
                                baslik: 'Uyarı',
                                mesaj: `Lütfen ${_.toInteger(index) + 1} sıra numaralı işlem için malzeme seçiniz!`,
                                tur: "warning"
                            });
                        }
                    };
                }

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
                        console.log(veri);

                        if(veri !== "geriGitme"){
                            this.siparisleriGetir();
                            this.geri();
                        }else{
                            this.siparisDuzenle(this.aktifSiparis);
                        }
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
            siparisDuzenle(siparis, hizliDetay = false) {
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
                    siparisId: siparis.siparisId,
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
                        bolunmusToplamliIslemler: response.data.veriler.bolunmusToplamliIslemler
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

                    if (_.size(aktifSiparis.bolunmusToplamliIslemler)) {
                        aktifSiparis.bolunmusToplamliIslemler.forEach(islem => {

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
                    if (!hizliDetay) {
                        this.aktifSayfaDegistir("YENI_SIPARIS");
                    }
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
            malzemeEkleAc(islemIndex, duzenleme = false) {
                const valueAttr = duzenleme ? this.aktifSiparis.islemler[islemIndex].malzeme.ad : "";
                const buttonText = duzenleme ? "Güncelle" : "Ekle";
                const malzemeId = duzenleme ? this.aktifSiparis.islemler[islemIndex].malzeme.id : undefined;
                // Malzeme adı, malzeme fiyat
                Swal.fire({
                    title: "Malzeme " + buttonText,
                    html: `
                        <div class="container">
                            <div class="row g-3">
                                <div class="form-group col-12">
                                    <input type="text" class="form-control" id="malzemeAdi" placeholder="Malzeme Adı *" value="${valueAttr}">
                                </div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: buttonText,
                    cancelButtonText: 'İptal',
                })
                .then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        const malzemeAdi = document.getElementById("malzemeAdi").value;

                        this.yukleniyorObjesi.malzemeEkle = true;
                        axios.post("/malzemeEkle", {
                            malzeme: {
                                malzemeId,
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
                                toast: {
                                    status: true,
                                    message: response.data.mesaj,
                                },
                            });

                            this.malzemeleriGetir().then(() => {
                                if (!this.aktifSiparis.islemler[islemIndex].malzeme) {
                                    this.aktifSiparis.islemler[islemIndex].malzeme = response.data.malzeme;

                                    this.malzemeSecildiginde(islemIndex);

                                    this.aktifSiparis = _.cloneDeep(this.aktifSiparis);
                                }
                                else if (duzenleme) {
                                    const malzeme = response.data.malzeme;
                                    _.forEach(this.aktifSiparis.islemler, (islem) => {
                                        if (malzeme.id == islem.malzeme.id) {
                                            islem.malzeme = malzeme;
                                        }
                                    });

                                    const malzemeIndex = _.findIndex(this.malzemeler, { id: malzeme.id })
                                    if (malzemeIndex > -1) {
                                        this.malzemeler[malzemeIndex] = malzeme;

                                        this.malzemeler = _.cloneDeep(this.malzemeler);
                                    }

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
                                    <input type="file" accept="image/*" class="form-control" id="resimSecimi" placeholder="Resim Seçimi">
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

                            const image = new Image();
                            image.onload = () => {
                                onizlemeEl.src = this.resmiOlceklendir(image, 0.6, 0.6);
                            };
                            image.src = URL.createObjectURL(resim);
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
            resmiOlceklendir(imgToCompress, resizingFactor, quality) {
                // showing the compressed image
                const canvas = document.createElement("canvas");
                const context = canvas.getContext("2d");

                const originalWidth = imgToCompress.width;
                const originalHeight = imgToCompress.height;

                const canvasWidth = originalWidth * resizingFactor;
                const canvasHeight = originalHeight * resizingFactor;

                canvas.width = canvasWidth;
                canvas.height = canvasHeight;

                context.drawImage(
                    imgToCompress,
                    0,
                    0,
                    originalWidth * resizingFactor,
                    originalHeight * resizingFactor
                );

                // reducing the quality of the image
                return canvas.toDataURL("image/jpeg", quality);
            },
            ciktiAl() {
                const baslangicDurum = !!this.aktifSiparis.onizlemeModu;

                this.aktifSiparis.onizlemeModu = true;
                this.aktifSiparis = _.cloneDeep(this.aktifSiparis);
                this.$nextTick(() => {
                    var element = document.getElementById('onizlemeGorunumu');
                    var onizlemeScrollElement = element.querySelector('#onizlemeScroll');
                        onizlemeScrollElement.classList.remove('overflow-auto');
                    var scrollHeight = element.scrollHeight;
                        element.style.height = scrollHeight + 'px';
                    var scrollWidth = element.scrollWidth;
                        element.style.width = scrollWidth + 'px';
                    var options = {
                        width: scrollWidth,
                    };
                    html2canvas(element, options).then(canvas => {
                        const uzanti = "png";
                        const base64 = canvas.toDataURL("image/png");
                        var a = document.createElement("a");
                        a.href = base64;
                        a.download = this.aktifSiparis.siparisAdi + "." + uzanti;

                        if (this.isNativeApp) {
                            window.ReactNativeWebView.postMessage(JSON.stringify({
                                kod: "INDIR",
                                dosya: base64,
                                dosyaAdi: this.aktifSiparis.siparisAdi,
                                dosyaUzantisi: uzanti,
                            }));
                        }
                        a.click();
                        onizlemeScrollElement.classList.add('overflow-auto');
                        element.style.width = '';
                        element.style.height = '';
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
            filtrelemeFaturaTarihTemizle() {
                this.filtrelemeObjesi.faturaBaslangicTarihi = null;
                this.filtrelemeObjesi.faturaBitisTarihi = null;
            },
            floatDonustur(deger, obj = {}) {
                const arr = _.split(deger, ".");
                const binliksizPara = _.join(arr, "");
                let sayi = _.replace(binliksizPara, ",", ".");

                if (obj.paraBirimi) {
                    sayi = _.replace(sayi, obj.paraBirimi.sembol, "");
                }
                else if (obj.kg) {
                    sayi = _.replace(sayi, "kg", "");
                }

                return _.round(sayi, 2);
            },
            yaziyaDonustur(deger, obj = {}) {
                const yaziDeger = _.toString(deger);
                const arr = _.split(yaziDeger, ".");
                let yazi = arr[0];
                if (arr[1]) {
                    yazi += "," + _.padEnd(arr[1], 2, "0");
                }
                else {
                    yazi += ",00";
                }

                if (obj.kg) {
                    yazi += " kg";
                }
                else if (obj.paraBirimi) {
                    yazi += " " + obj.paraBirimi.sembol;
                }

                return yazi;
            },
            async hizliDetayAc(index) {
                if (this.siparisler.data[index].islemlerAcik) {
                    this.siparisler.data[index].islemlerAcik = false;
                    this.siparisler.data[index].islemYukleniyor = false;
                    return;
                }
                this.siparisler.data[index].islemYukleniyor = true;
                this.siparisler = _.cloneDeep(this.siparisler);

                await this.siparisDuzenle(this.siparisler.data[index], true);

                this.siparisler.data[index].islemler = this.aktifSiparis.islemler;
                this.siparisler.data[index].bolunmusToplamliIslemler = this.aktifSiparis.bolunmusToplamliIslemler;
                this.siparisler.data[index].islemlerAcik = true;
                this.siparisler.data[index].islemYukleniyor = false;
                this.siparisler = _.cloneDeep(this.siparisler);
            },
            excelCikti() {
                this.siparisleriGetir(undefined, true);
            },
            sorguParametreleriTemizle() {
                this.sorguParametreleri = {
                    siparisId: null,
                };

                if (this.aktifSiparis && this.aktifSiparis.siparisId === this.filtrelemeObjesi.siparisId) {
                    this.geri();
                }

                delete this.filtrelemeObjesi.siparisId;

                window.history.replaceState({}, document.title, (new URL(window.location.href)).pathname)

                this.siparisleriGetir();
            },
            daraSonraGirilecekAyarla(islem) {
                if (islem.daraSonraGirilecek) {
                    islem.dara = 0;
                    islem.daraYazi = this.yaziyaDonustur(islem.dara, { kg: true });

                    this.aktifSiparis = _.cloneDeep(this.aktifSiparis);
                }
            },
            raporOlusturAc() {
                this.siparisRaporlama = {
                    html: "",
                    modal: new bootstrap.Modal(document.getElementById("raporlamaModal")),
                    islem: null,
                    islemler: _.cloneDeep(this.aktifSiparis.bolunmusToplamliIslemler),
                    grafikOptions: _.cloneDeep(this.siparisRaporlama.grafikOptions),
                    grafikSeries: _.cloneDeep(this.siparisRaporlama.grafikSeries),
                    _defaultGrafikOptions: _.cloneDeep(this.siparisRaporlama.grafikOptions),
                    _defaultGrafikSeries: _.cloneDeep(this.siparisRaporlama.grafikSeries),
                };

                this.siparisRaporlama.modal.show();
            },
            raporOlusturKapat() {
                this.siparisRaporlama.modal.hide();

                this.siparisRaporlama = {
                    ...this.siparisRaporlama,
                    modal: null,
                    islem: null,
                    islemler: null,
                    grafikOptions: _.cloneDeep(this.siparisRaporlama._defaultGrafikOptions),
                    grafikSeries: _.cloneDeep(this.siparisRaporlama._defaultGrafikSeries),
                };
            },
            raporIslemSec(islem) {
                if (this.siparisRaporlama.islem && this.siparisRaporlama.islem.id == islem.id) {
                    return this.siparisRaporlama.islem = null;
                }

                this.siparisRaporlama.islem = {
                    ...islem,
                    onizlemeChart: null,
                    siparisNo: this.aktifSiparis.siparisNo,
                    firma: _.cloneDeep(this.aktifSiparis.firma),
                };

                this.raporAlanlariDoldur();
            },
            raporOlustur() {
                this.siparisRaporlama.modal.hide();

                this.globalYazdir(this.siparisRaporlama.html, {
                    beforePrint: (printAreaEl) => {
                        this.$nextTick(() => {
                            const chart = new ApexCharts(printAreaEl.querySelector(".siparis-raporlama-chart"), {
                                series: this.siparisRaporlama.grafikSeries,
                                ...this.siparisRaporlama.grafikOptions,
                            });
                            chart.render();
                        });
                    },
                    afterPrint: () => {
                        this.siparisRaporlama.modal.show();
                    }
                });
            },
            raporAlanlariGuncelle(tur) {
                console.log(tur);
                if (!this.siparisRaporlama.islem) {
                    return;
                }

                switch (tur) {
                    case "OLCUM": {
                        if (this.siparisRaporlama.islem.sonSertlik != "") {
                            const olcumlerDizisi = _.map(_.split(this.siparisRaporlama.islem.sonSertlik, ","), _.trim);

                            this.siparisRaporlama.grafikSeries[0].data = [];
                            _.forEach(olcumlerDizisi, olcum => {
                                if (olcum != "") {
                                    const _olcum = _.toNumber(olcum);
                                    if (!_.isNaN(_olcum)) {
                                        this.siparisRaporlama.grafikSeries[0].data.push(_olcum);
                                    }
                                }
                            });
                        }
                        else {
                            this.siparisRaporlama.grafikSeries[0].data = [];
                        }

                        this.siparisRaporlama.islem.onizlemeChart.updateSeries(this.siparisRaporlama.grafikSeries);

                        break;
                    }
                    case "GELIS":
                    case "NOT": {
                        this.raporAlanlariDoldur();
                        break;
                    }
                }
            },
            raporAlanlariDoldur() {
                const rapor = {
                    tarih: this.m().format("L"),
                    no: this.siparisRaporlama.islem.siparisNo + "-" + this.siparisRaporlama.islem.id,
                    gelisTarihi: this.siparisRaporlama.islem.gelisTarihi ? this.siparisRaporlama.islem.gelisTarihi : "---",
                    firma: this.siparisRaporlama.islem.firma.firmaAdi,
                    urunKalitesi: this.siparisRaporlama.islem.kalite ? this.siparisRaporlama.islem.kalite : "---",
                    malzeme: this.siparisRaporlama.islem.malzeme.ad,
                    urunAdedi: this.siparisRaporlama.islem.adet,
                    yapilanIslem: this.siparisRaporlama.islem.yapilacakIslem ? this.siparisRaporlama.islem.yapilacakIslem.ad : "---",
                    istenenSertlik: this.siparisRaporlama.islem.istenilenSertlik ? this.siparisRaporlama.islem.istenilenSertlik : "---",
                    sonSertlik: this.siparisRaporlama.islem.sonSertlik ? this.siparisRaporlama.islem.sonSertlik : "---",
                    not: this.siparisRaporlama.islem.not ? this.siparisRaporlama.islem.not : "---",
                    urunFotografi: this.siparisRaporlama.islem.resimYolu ? this.siparisRaporlama.islem.resimYolu : "/no-image.jpg",
                }

                const cloneRaporArea = this.$refs.siparisRaporlama.cloneNode(true);
                cloneRaporArea.style.display = "block";
                cloneRaporArea.style.background = "white";
                // cloneRaporArea.style.overflow = "hidden";

                const compiled = _.template(cloneRaporArea.outerHTML);
                this.siparisRaporlama.html = compiled({ rapor });

                this.$nextTick(() => {
                    this.siparisRaporlama.islem.onizlemeChart = new ApexCharts(document.querySelector(".siparis-raporlama-chart"), {
                        series: _.cloneDeep(this.siparisRaporlama.grafikSeries),
                        ..._.cloneDeep(this.siparisRaporlama.grafikOptions),
                    });
                    this.siparisRaporlama.islem.onizlemeChart.render();
                });
            }
        }
    };
</script>
@endsection

@section('style')
    <link rel="stylesheet" href="/css/print.css">
@endsection
