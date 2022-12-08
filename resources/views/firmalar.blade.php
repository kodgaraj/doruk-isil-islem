@extends('layout')
@section('content')
    <div class="row doruk-content">
        <h4 style="color:#999"><i class="fas fa-globe"></i> FİRMALAR</h4>
        <div class="col-12">
            <div class="card" key="ANASAYFA">
                <div class="card-body">
                    <div class="row d-flex align-items-center">
                        <div class="col">
                            <h4 class="card-title">
                                <button class="btn btn-warning" v-if="aktifSayfa.geriFonksiyon"
                                    @click="aktifSayfa.geriFonksiyon()">
                                    <i class="fa fa-arrow-left"></i> GERİ
                                </button>
                                @{{ aktifSayfa.baslik }}
                            </h4>
                        </div>
                        <div class="col-auto" v-if="aktifSayfa.kod === 'ANASAYFA'">
                            <div class="row d-flex align-items-center">
                                <div class="col">
                                    <div class="input-group">
                                        <input
                                            v-model="filtrelemeObjesi.arama" t
                                            ype="text"
                                            class="form-control"
                                            placeholder="Arama"
                                            aria-label="Arama"
                                            aria-describedby="arama"
                                            @keyup.enter="firmalariGetir()"
                                            @input="gecikmeliFonksiyon.varsayilan()"
                                        />
                                        <span @click="firmalariGetir()" class="input-group-text waves-effect"
                                            id="arama">
                                            <i class="mdi mdi-magnify"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <small class="text-muted">
                                            Firma adı/firma sorumlusu/telefon...
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center text-sm-end">
                                    <button v-if="aktifSayfa.kod === 'ANASAYFA'" class="btn btn-sm btn-outline-info"
                                        @click="teklifOlusturmaAc()">
                                        <i class="fas fa-file-signature"></i> TEKLİF OLUŞTUR
                                    </button>
                                    <button v-if="aktifSayfa.kod === 'ANASAYFA'" class="btn btn-sm btn-primary"
                                        @click="firmaEkleAc()">
                                        <i class="fa fa-plus"></i> FİRMA EKLE
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button v-if="aktifSayfa.kod === 'YENI_FIRMA' && yeniFirma.id && !yukleniyorObjesi.firmaEkle"
                                class="btn btn-warning" @click="firmaBirlestirmeAc()">
                                <i class="fa fa-sync"></i> FİRMA BİRLEŞTİR
                            </button>
                        </div>

                        <div class="col-auto">
                            <!-- firma KAYDET BUTONU -->
                            <button v-if="aktifSayfa.kod === 'YENI_FIRMA' && !yukleniyorObjesi.firmaEkle"
                                class="btn btn-primary" @click="firmaEkle()">
                                <i class="fa fa-save"></i> FİRMA KAYDET
                            </button>
                        </div>
                        <div class="col-auto">
                            <!-- TEKLİF OLUŞTUR BUTONU -->
                            <button
                                v-if="aktifSayfa.kod === 'TEKLIF_HAZIRLAMA' && !yukleniyorObjesi.firmaEkle"
                                class="btn btn-primary"
                                @click="teklifOlustur()"
                            >
                                <i class="fa fa-file-download"></i> TEKLİF OLUŞTUR
                            </button>

                            <div v-else-if="yukleniyorObjesi.firmaEkle" class="spinner-border text-primary" role="status">
                                <span class="sr-only">Yükleniyor...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="birlestirmeModal" tabindex="-1" aria-labelledby="birlestirmeModalTitle" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="birlestirmeModalTitle">Firma Birleştirme</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="col m-0">
                                    <div class="form-group">
                                        <label for="birlestirilecekFirma">Birleştirilecek Firma</label>
                                        <v-select
                                            v-model="birlestirmeObjesi.firma"
                                            :options="birlestirmeObjesi.firmalar"
                                            :loading="yukleniyorObjesi.birlestirilecekFirmalar"
                                            label="firmaAdi"
                                            id="birlestirilecekFirma"
                                        ></v-select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    :disabled="yukleniyorObjesi.birlestirilecekFirmalar"
                                    @click="firmalariBirlestir()"
                                >
                                    <template v-if="yukleniyorObjesi.birlestirilecekFirmalar">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        BİRLEŞTİRİLİYOR...
                                    </template>
                                    <template v-else>
                                        BİRLEŞTİR
                                    </template>
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">VAZGEÇ</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <template v-if="aktifSayfa.kod === 'ANASAYFA'">
                        <template v-if="yukleniyorObjesi.firmalariGetir">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Yükleniyor...</span>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="table-rep-plugin">
                                <div class="table-responsive mb-0" data-pattern="priority-columns">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th @click="siralamaYap('firmaAdi')">
                                                    Firmalar
                                                    <template v-if="filtrelemeObjesi.siralamaTuru">
                                                        <i :class="filtrelemeObjesi.siralamaTuru.firmaAdi === 'desc' ? 'fas fa-sort-alpha-down' : 'fas fa-sort-alpha-up'"></i>
                                                    </template>
                                                </th>
                                                <th @click="siralamaYap('sorumluKisi')" class="text-center">
                                                    Firma Sorumlusu
                                                    <template v-if="filtrelemeObjesi.siralamaTuru">
                                                        <i :class="filtrelemeObjesi.siralamaTuru.sorumluKisi === 'desc' ? 'fas fa-sort-alpha-down' : 'fas fa-sort-alpha-up'"></i>
                                                    </template>
                                                </th>
                                                <th @click="siralamaYap('telefon')" class="text-center">Telefon</th>
                                                <th class="text-center">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-if="_.size(firmalar.data)">
                                                <tr v-for="(firma, index) in firmalar.data" :key="index">
                                                    <td># @{{ firma.id }}</td>
                                                    <td class="uzun-uzunluk">
                                                        @{{ firma.firmaAdi }}
                                                    </td>
                                                    <td class="kisa-uzunluk text-center">
                                                        @{{ firma.sorumluKisi }}
                                                    </td>
                                                    <td class="kisa-uzunluk text-center">
                                                        @{{ firma.telefon }}
                                                    </td>
                                                    <td class="kisa-uzunluk text-center">
                                                        <button class="btn btn-sm btn-outline-info" @click="teklifOlusturmaAc(firma)">
                                                            <i class="fa fa-file-signature"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-primary" @click="firmaDuzenle(firma)">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" @click="firmaSil(firma)">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                            <template v-else>
                                                <tr>
                                                    <td colspan="100%" class="text-center py-4">
                                                        <h6>Kayıt bulunamadı</h6>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row d-flex align-items-center justify-content-between">
                                    <div class="col-auto"></div>
                                    <div class="col">
                                        <ul class="pagination pagination-rounded justify-content-center mb-0">
                                            <li class="page-item">
                                                <button class="page-link" :disabled="!firmalar.prev_page_url" @click="firmalariGetir(firmalar.prev_page_url)">
                                                    <i class="fas fa-angle-left"></i>
                                                </button>
                                            </li>
                                            <li
                                                v-for="sayfa in sayfalamaAyarla(firmalar.last_page, firmalar.current_page)"
                                                class="page-item"
                                                :class="[sayfa.aktif ? 'active' : '']"
                                            >
                                                <button class="page-link" @click="sayfa.tur === 'SAYFA' ? firmalariGetir(`{{ route("firmalariGetir") }}?page=` + sayfa.sayfa) : ()  => {}">@{{ sayfa.sayfa }}</button>
                                            </li>
                                            <li class="page-item">
                                                <button class="page-link" :disabled="!firmalar.next_page_url" @click="firmalariGetir(firmalar.next_page_url)">
                                                    <i class="fas fa-angle-right"></i>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-auto">
                                        <small class="text-muted">Toplam Kayıt: @{{ firmalar.total }}</small>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </template>
                    <template v-else-if="aktifSayfa.kod === 'YENI_FIRMA'">
                        <!-- firmaEkle -->
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="name">Firma adı *</label>
                                <input v-model="yeniFirma.firmaAdi" autocomplete="off" id="name" type="text"
                                    class="form-control" placeholder="Firma adı" />
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="firma_sorumlusu">Firma Sorumlusu</label>
                                <input v-model="yeniFirma.sorumluKisi" autocomplete="off" id="firma_sorumlusu"
                                    type="text" class="form-control" placeholder="Firma Sorumlusu" />
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="telefon">Telefon</label>
                                <input v-model="yeniFirma.telefon" autocomplete="off" id="telefon" type="text"
                                    class="form-control" placeholder="Telefon" />
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="telefon">E-posta</label>
                                <input v-model="yeniFirma.eposta" autocomplete="off" id="eposta" type="text"
                                    class="form-control" placeholder="E-posta" />
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="telefon">Adres</label>
                                <textarea v-model="yeniFirma.adres" autocomplete="off" id="adres" type="text"
                                    class="form-control" placeholder="Adres" rows="1" cols="50"></textarea>
                            </div>
                        </div>
                    </template>
                    <template v-else-if="aktifSayfa.kod === 'TEKLIF_HAZIRLAMA'">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="name">Firma adı *</label>
                                <input
                                    v-model="teklif.firma.firmaAdi"
                                    @input="gecikmeliFonksiyon.teklif()"
                                    autocomplete="off"
                                    id="name"
                                    type="text"
                                    class="form-control"
                                    placeholder="Firma adı"
                                />
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="firma_sorumlusu">Firma Sorumlusu</label>
                                <input
                                    v-model="teklif.firma.sorumluKisi"
                                    @input="gecikmeliFonksiyon.teklif()"
                                    autocomplete="off"
                                    id="firma_sorumlusu"
                                    type="text"
                                    class="form-control"
                                    placeholder="Firma Sorumlusu"
                                />
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="telefon">Telefon</label>
                                <input
                                    v-model="teklif.firma.telefon"
                                    @input="gecikmeliFonksiyon.teklif()"
                                    autocomplete="off"
                                    id="telefon"
                                    type="text"
                                    class="form-control"
                                    placeholder="Telefon"
                                />
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="telefon">E-posta</label>
                                <input
                                    v-model="teklif.firma.eposta"
                                    @input="gecikmeliFonksiyon.teklif()"
                                    autocomplete="off"
                                    id="eposta"
                                    type="text"
                                    class="form-control"
                                    placeholder="E-posta"
                                />
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="telefon">Adres</label>
                                <textarea
                                    v-model="teklif.firma.adres"
                                    @input="gecikmeliFonksiyon.teklif()"
                                    autocomplete="off"
                                    id="adres"
                                    type="text"
                                    class="form-control"
                                    placeholder="Adres"
                                    rows="1"
                                    cols="50"
                                ></textarea>
                            </div>

                            <hr class="mb-0" />

                            <div class="col-12 text-center">
                                <button
                                    class="btn btn-sm btn-primary"
                                    @click="teklifIcerikEkle({}, true)"
                                >
                                    <i class="fa fa-plus"></i>
                                    İŞLEM EKLE
                                </button>
                            </div>

                            <div class="col-12">
                                <div class="row d-flex align-items-center my-1" v-for="(icerik, index) in teklif.icerikler" :key="index">
                                    <div class="col-1 text-center">
                                        <button
                                            class="btn btn-sm btn-danger"
                                            @click="teklifIcerikSil(index)"
                                        >
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                    <div class="col-7 text-start">
                                        <input
                                            v-model="icerik.ad"
                                            @input="gecikmeliFonksiyon.teklif()"
                                            placeholder="İşlem"
                                            class="small form-control"
                                        />
                                    </div>
                                    <div class="col-2 text-start">
                                        <input
                                            v-model="icerik.fiyat"
                                            @change="paraBirimiFormatla(icerik)"
                                            placeholder="Fiyat"
                                            class="small form-control"
                                        />
                                    </div>
                                    <div class="col-1 text-start">
                                        <select
                                            v-model="icerik.paraBirimi"
                                            @change="gecikmeliFonksiyon.teklif()"
                                            name="paraBirimi"
                                            id="paraBirimi"
                                            class="small form-control"
                                        >
                                            <option value="" disabled>Para birimi seçiniz...</option>
                                            <option
                                                v-for="(paraBirimi, pIndex) in teklif.paraBirimleri"
                                                :value="paraBirimi.kod"
                                                :key="pIndex"
                                            >
                                                @{{ paraBirimi.ad }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-1 text-start">
                                        <select
                                            v-model="icerik.olcumTuru"
                                            @change="gecikmeliFonksiyon.teklif()"
                                            name="olcumTuru"
                                            id="olcumTuru"
                                            class="small form-control"
                                        >
                                            <option value="">-</option>
                                            <option
                                                v-for="(olcumTuru, oIndex) in teklif.olcumTurleri"
                                                :value="olcumTuru.kod"
                                                :key="oIndex"
                                            >
                                                @{{ olcumTuru.ad }} (@{{ olcumTuru.sembol }})
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="overflow-auto">
                                    <div
                                        v-html="teklif.html"
                                        :key="teklif.htmlKey"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div ref="teklif" style="display: none; background: white; color: black;">
        <!-- 1. sayfa -->
        <div class="printable-page" id="page-1">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-4">
                    <div>
                        <img style="width: 100%" src="/img/doruk-logo.png" />
                    </div>
                </div>
                <div class="col-4 text-center">
                    <b><h3>DORUK ISIL İŞLEM FİYAT TEKLİFİ</h3></b>
                </div>
                <div class="col-4">
                    <div>
                        <img style="width: 100%" src="/img/doruk-sertifika.png" />
                    </div>
                </div>
                <div class="col-12 text-end">
                    <div>
                        <h6><b>TARİH:</b> ${ data.tarih }</h6>
                    </div>
                </div>

                <!-- Firma -->
                {{-- <div class="col-2 text-start">
                    <h6><b>Firma:</b></h6>
                </div>
                <div class="col-10 text-start">
                    <h6>${ data.firma }</h6>
                </div> --}}
                <div class="col-12 text-start">
                    <h6><b>Firma:</b> ${ data.firma }</h6>
                </div>

                <!-- Yetkili -->
                <div class="col-6 text-start">
                    <h6><b>Yetkili:</b> ${ data.yetkili }</h6>
                </div>

                <!-- Telefon -->
                <div class="col-6 text-start">
                    <h6><b>Telefon:</b> ${ data.telefon }</h6>
                </div>

                <!-- Adres -->
                <div class="col-12 text-start">
                    <h6><b>Adres:</b> ${ data.adres }</h6>
                </div>

                <!-- E-posta -->
                <div class="col-12 text-start">
                    <h6><b>E-posta:</b> ${ data.eposta }</h6>
                </div>

                <hr class="m-0" />

                <div class="col-12" style="min-height: 600px; width: 100% !important;">
                    <div class="row d-flex align-items-center">
                        <% _.forEach(data.icerikler, function (icerik) { %>
                            <div class="col-12 my-1">
                                <div class="row d-flex align-items-center">
                                    <div class="col-8 text-start">
                                        <span><%- icerik.ad %></span>
                                    </div>
                                    <div class="col-2 text-end">
                                        <span><%- icerik.fiyat %></span>
                                    </div>
                                    <div class="col-1 text-end pe-0">
                                        <span><%- icerik.paraBirimi %></span>
                                    </div>
                                    <div class="col-1 text-start ps-1">
                                        <% if (icerik.olcumTuru) { %>
                                            /
                                            <span><%- icerik.olcumTuru %></span>
                                        <% } %>
                                    </div>
                                </div>
                            </div>
                            <hr class="m-0" />
                        <% }); %>
                    </div>
                </div>

                <hr class="m-0" />

                <div class="col-12">
                    <b>* Fiyat teklifimizi onaylamanız durumunda kaşe/imza yapmanızı önemle rica ederiz.</b>
                </div>

                <div class="col-12 mt-2">
                    <div class="row text-center">
                        <div class="col-4">
                            Müşteri Onay Kaşe/İmza
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <div class="col-12">
                                    <b>Hazırlayan</b>
                                </div>
                                <div class="col-12">
                                    Aziz KALEM
                                </div>
                                <div class="col-12 text-muted small">
                                    Metalurji ve Malzeme Mühendisi
                                </div>
                                <div class="col-12">
                                    <img style="width: 100%" src="/img/doruk-aziz-imza.png" />
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <div class="col-12">
                                    <b>Onaylayan</b>
                                </div>
                                <div class="col-12">
                                    Gökhan ÇELİK
                                </div>
                                <div class="col-12 text-muted small">
                                    İnşaat Mühendisi
                                    <br />
                                    Genel Müdür
                                </div>
                                <div class="col-12">
                                    <img style="width: 100%" src="/img/doruk-gokhan-imza.png" />
                                </div>
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

        <!-- 2. sayfa -->
        <div class="printable-page" id="page-2">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-4">
                    <div>
                        <img style="width: 100%" src="/img/doruk-logo.png" />
                    </div>
                </div>
                <div class="col-4 text-center">
                    <b><h3>DORUK ISIL İŞLEM FİYAT TEKLİFİ</h3></b>
                </div>
                <div class="col-4">
                    <div>
                        <img style="width: 100%" src="/img/doruk-sertifika.png" />
                    </div>
                </div>
                <div class="col-12 text-end">
                    <div>
                        <h6><b>TARİH:</b> ${ data.tarih }</h6>
                    </div>
                </div>

                <!-- Firma -->
                {{-- <div class="col-2 text-start">
                    <h6><b>Firma:</b></h6>
                </div>
                <div class="col-10 text-start">
                    <h6>${ data.firma }</h6>
                </div> --}}
                <div class="col-12 text-start">
                    <h6><b>Firma:</b> ${ data.firma }</h6>
                </div>

                <!-- Yetkili -->
                <div class="col-6 text-start">
                    <h6><b>Yetkili:</b> ${ data.yetkili }</h6>
                </div>

                <!-- Telefon -->
                <div class="col-6 text-start">
                    <h6><b>Telefon:</b> ${ data.telefon }</h6>
                </div>

                <!-- Adres -->
                <div class="col-12 text-start">
                    <h6><b>Adres:</b> ${ data.adres }</h6>
                </div>

                <!-- E-posta -->
                <div class="col-12 text-start">
                    <h6><b>E-posta:</b> ${ data.eposta }</h6>
                </div>

                <hr class="m-0" />

                <div class="col-12">
                    <div class="row">
                        <div class="col-12">
                            1 - Fiyatlarımıza KDV dahil değildir.
                        </div>
                        <div class="col-12">
                            2 - Tek seferde gelen ürün/işlem fatura tutarının <strong class="text-danger"><u>600,00 $</u></strong> altında kalması halinde <strong class="text-danger"><u>şarj bedeli</u></strong> uygulanır.
                        </div>
                        <div class="col-12">
                            3 - Gelen malzemelerde kalite veya istenen sertlik değerleri farklı olması halinde her ürün grubu için minimum iş bedeli uygulanır.
                        </div>
                        <div class="col-12">
                            4 - Özel ürünlerde ve / veya özel proses gerektiren ürünlerde fiyatlar ayrıca görüşülecektir.
                        </div>
                        <div class="col-12">
                            5 - Ödeme: Ürün tesliminde <strong class="text-danger"><u>nakit, maksimum 30 günlük çek</u></strong> ile ya da <strong class="text-danger"><u>30 gün</u></strong> içinde banka havalesi şeklinde yapılacaktır.
                        </div>
                        <div class="col-12">
                            6 - Teklifin geçerlilik süresi <strong class="text-danger"><u>15 gün</u></strong>dür. <strong class="text-danger"><u>15 gün</u></strong> içinde teklifin onaylanması durumunda; yukarıdaki fiyatlar <strong class="text-danger"><u>${ data.gecerlilikTarihi }</u></strong> tarihine kadar geçerli olacaktır. <strong class="text-danger"><u>15 gün</u></strong> içinde teklifin onaylanmaması durumunda ise tekrar fiyat teklifi istemenizi rica ederiz.
                        </div>
                        <div class="col-12">
                            7 - Ürün teslim (termin) süresi, ürünün fimamıza tesliminden itibaren <strong class="text-danger"><u>5+1</u></strong> iş günüdür
                        </div>
                        <div class="col-12">
                            8 - Firmamızın hazırlamış olduğu fiyat teklifi tarafınızdan imza edilmemiş / onaylanmamış olsa dahi ısıl işleme tabi tutulacak ürünün / ürünlerin tesisimize gönderilmesi durumunda, fiyat teklifi ile sözleşme şartları kabul edilmiş sayılır.
                        </div>
                        <div class="col-12">
                            9 - Isıl İşleme tabi tutulan ürünlerinizi teslim etmeyi öngördüğümüz sürelere, enerji kesintisi, arıza ve benzeri sorunlar dışında, uyacağımızı taahüt ederiz. Teknik sorunlar da mücbir sebep olarak gecikmeye neden olabilecektir. Bu durumlarda tarafınıza bilgilendirme yapılacaktır.
                        </div>
                        <div class="col-12">
                            10 - Ürünlere uygulanması istenilen ısıl işlem operasyonu ve özellikleri ile ürün / ürünlere ait teknik resmi, var ise ısıl işlem şartnamesini, ürünün teslimi ile birlikte tarafımıza iletilmelidir. Bu bilgi ve belgelerin teslim edilmemesinden meydana gelecek her türlü zarardan firmamız sorumlu tutulamayacaktır.
                        </div>
                        <div class="col-12">
                            11 - Malzeme bilgilerinin sertifikasının ürünle beraber gönderilmediği durumda spesifikasyon limitleri dışında elde edilen sertlik ve sertlik derinliğinden ve ölçülerden firmamız sorumlu değildir.
                        </div>
                        <div class="col-12">
                            12 - İletilen bilgiler hatalı, yanlış ve eksik olması nedenleriyle ısıl işlem başarısız olsa dahi, ısıl işlem ücretinin tamamı ödenecektir.
                        </div>
                        <div class="col-12">
                            13 - Firmamız bünyesinde Isıl işleme tabi tutulan ürünlerin sertlik değerleri, tarafınızdan iletilen bilgilerde belirttiğiniz sertlik değer aralığı dışında kaldığında hiçbir bedel gözetmeksizin ısıl işlemin tekrarını gerçekleştirerek belirtilen sertlik değer aralığına getireleceğini kabul ve taahhüt ederiz.
                        </div>
                        <div class="col-12">
                            14 - Boyutsal kararlılık veya yüzey durumuna ilişkin talepler irsaliyelerde belirtilmelidir veya irsaliye ile ulaştırılmalıdır. Özellikle kaynaklanmış veya lehimlenmiş ve içinde boşluklar olan materyallere ilişkin bilgi vermelidir. Teslim edilen ürünleri ebat, ağırlık ve miktar yönünden kontrole tabi tutabiliriz. Ancak, teslim edilen ürünlerin kalitesi açısından kontrol görevimiz bulunmamaktadır. Bu kapsamda teslim edilen ürünlerin doğru ve elverişli olduğu kabul edilir. Bu kapsamda yükümlülüklerinizi yerine getirmemeniz veya eksik bırakmanızdan meydana gelecek zararlarda sorumluk size aittir. Açık talebiniz olması halinde ve masrafını karşılamanız şartıyla kontrol işlemini sizin adınıza yaptırabiliriz. Teslim edilen ürünlerdeki gizli kusurlardan kaynaklanan zararlardan firmamız sorumlu değildir.
                        </div>
                    </div>
                </div>

                <hr class="m-0" />

                <div class="col-12">
                    <b>* Fiyat teklifimizi onaylamanız durumunda kaşe/imza yapmanızı önemle rica ederiz.</b>
                </div>

                <div class="col-12 mt-2">
                    <div class="row text-center">
                        <div class="col-4">
                            Müşteri Onay Kaşe/İmza
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <div class="col-12">
                                    <b>Hazırlayan</b>
                                </div>
                                <div class="col-12">
                                    Aziz KALEM
                                </div>
                                <div class="col-12 text-muted small">
                                    Metalurji ve Malzeme Mühendisi
                                </div>
                                <div class="col-12">
                                    <img style="width: 100%" src="/img/doruk-aziz-imza.png" />
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <div class="col-12">
                                    <b>Onaylayan</b>
                                </div>
                                <div class="col-12">
                                    Gökhan ÇELİK
                                </div>
                                <div class="col-12 text-muted small">
                                    İnşaat Mühendisi
                                    <br />
                                    Genel Müdür
                                </div>
                                <div class="col-12">
                                    <img style="width: 100%" src="/img/doruk-gokhan-imza.png" />
                                </div>
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

        <!-- 3. sayfa -->
        <div class="printable-page" id="page-2">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-4">
                    <div>
                        <img style="width: 100%" src="/img/doruk-logo.png" />
                    </div>
                </div>
                <div class="col-4 text-center">
                    <b><h3>DORUK ISIL İŞLEM FİYAT TEKLİFİ</h3></b>
                </div>
                <div class="col-4">
                    <div>
                        <img style="width: 100%" src="/img/doruk-sertifika.png" />
                    </div>
                </div>
                <div class="col-12 text-end">
                    <div>
                        <h6><b>TARİH:</b> ${ data.tarih }</h6>
                    </div>
                </div>

                <!-- Firma -->
                {{-- <div class="col-2 text-start">
                    <h6><b>Firma:</b></h6>
                </div>
                <div class="col-10 text-start">
                    <h6>${ data.firma }</h6>
                </div> --}}
                <div class="col-12 text-start">
                    <h6><b>Firma:</b> ${ data.firma }</h6>
                </div>

                <!-- Yetkili -->
                <div class="col-6 text-start">
                    <h6><b>Yetkili:</b> ${ data.yetkili }</h6>
                </div>

                <!-- Telefon -->
                <div class="col-6 text-start">
                    <h6><b>Telefon:</b> ${ data.telefon }</h6>
                </div>

                <!-- Adres -->
                <div class="col-12 text-start">
                    <h6><b>Adres:</b> ${ data.adres }</h6>
                </div>

                <!-- E-posta -->
                <div class="col-12 text-start">
                    <h6><b>E-posta:</b> ${ data.eposta }</h6>
                </div>

                <hr class="m-0" />

                <div class="col-12">
                    <div class="row">
                        <div class="col-12">
                            15 - Ürün üzerinde bulunan köşeler, kesit farklılıkları, kenara yakın ve/veya kör delikler, kama kanalları, gerekli şekilde verilmemiş radyuslar, damgalar ve benzeri noktalardan kaynaklanan çatlamalar ve yine hammaddede bulunan hatalardan kaynaklanan çatlama veya deformasyonlardan firmamız sorumlu tutulmayacaktır.
                        </div>
                        <div class="col-12">
                            16 - Isıl işlem uygulanan ürünlerde asgari dahi olsa distorsiyon - boyutsal ölçü değişikliği olabilir. Bu yüzden ısıl işleme tabi tutulacak ürünler nihai ölçüye getirilmeden, taşlama öncesi ölçüleriyle teslim edilmelidir. Ürünlerin çapı ve boyuna göre taşlama payı değişebilir. Bu nedenle ürünler teslim edilirken taşlama payı belirtilmeli veya taşlama payı ne kadar bırakalım diye sorulmalıdır. <strong>AKSİ DURUMDA SORUMLULUK FİRMAMIZDA OLMAYACAKTIR.</strong>
                        </div>
                        <div class="col-12">
                            17 - Firmamız bünyesinde ısıl işleme tabi tutulan ürünlerin takribi %10 oranında sertlik kontrolü yapılacağını belirtiriz. Ancak çok küçük ve yüksek adetli ürünlerde ise her bir kilogram ürün için 1 adet numuneye sertlik kontrolü yapılacaktır.
                        </div>
                        <div class="col-12">
                            18 - Isıl işlem sonrasında ürünlere uygulatılacak ek işlemler (taşlama, kaynak, tornalama, ısıl işlem kaplama vb.) sonrası parçada ortaya çıkabilecek hasarlardan firmamız sorumlu tutulmayacaktır.
                        </div>
                        <div class="col-12">
                            19 - Isıl işleme tabi tutulan üründen dolayı, üçüncü kişilerin herhangi bir şekilde zarar görmesi halinde bu zararlardan firmamız sorumlu tutulamaz. Tarafınızdan üçüncü kişilere yapılan ödemeler, firmamıza rücu edilemez.
                        </div>
                        <div class="col-12">
                            20 - Isıl işlemi tamamlanan ürünler <strong class="text-danger"><u>7 (yedi) gün</u></strong> içerisinde teslim alınmalıdır.
                        </div>
                        <div class="col-12">
                            21 - Ürünlerinize ısıl işlemi uygulayabilmemiz için özel olarak imal edilmesi ve / veya satın alınması gereken aparatların olması durumunda; talep ettiğiniz takdirde, bu özel aparatların bedeli tarafınıza ait olacaktır.
                        </div>
                        <div class="col-12">
                            22 - Teslime hazır ürünlerin yazılı ya da e-posta ile bildiriminden sonra <strong class="text-danger"><u>15 gün</u></strong> içerisinde teslim alınmamasından dolayı oluşabilecek zararlardan firmamız sorumlu tutulamaz. <strong class="text-danger"><u>30 gün</u></strong>den daha uzun teslim alınmayan ürünlere depolama ücreti uygulanacaktır.
                        </div>
                        <div class="col-12">
                            23 - Ödemeler, faturaları alınır alınmaz herhangi bir (EK) indirime tabi olmaksızın (anlaşılan şekilde / vadede) ödenecektir. Ödemelerinizde gecikme olur ise, yıllık olarak Türkiye Merkez Bankası reeskont faizi uygulanır.
                        </div>
                        <div class="col-12">
                            24 - Ödemelerinizi, ürün tesliminde nakit olarak veya banka havalesi ile yapabilirsiniz. Vadeli anlaşmalarda vade tarihi en son ödeme günüdür.
                        </div>
                        <div class="col-12">
                            25 - Anlaşılan şekilde / vadede ödeme yapılmaması durumunda, herhangi bir görüşmeye gerek olmadan, yine teklifinde belirttiği faiz oranları üzerinden vade farkı faturası kesilecektir.
                        </div>
                        <div class="col-12">
                            26 - Müşteri tarafından verilen çek, vadeli anlaşmalarda vade tarihini geçemez. Vade tarihinden ileri tarihli çek verilmesi durumunda müşteri, çek miktarının <strong class="text-danger"><u>%8</u></strong> kadarının (çek işletme maliyeti olarak) eksik tahsil edilmiş olduğunu kabul ve taahhüt eder.
                        </div>
                        <div class="col-12">
                            27 - Tesisimize gönderilen ürünlere ait tarafımıza iletilen tüm bilgiler ve dokümantasyon eksiksiz ve doğru olmasına rağmen tesisimiz bünyesindeki ekipmanlardan ve / veya firmamız çalışanlarından kaynaklı hatalardan ve ısıl işlemin yanlış yapılmasından dolayı ürünlerde oluşabilecek zararlarda ürün hammadde bedelini karşılayacağımızı kabul ve taahhüt ederiz. Ürün işleme ve işçilik ücretlerinin sorumluluğu ve taahhüdü tarafınıza aittir.
                        </div>
                    </div>
                </div>

                <hr class="m-0" />

                <div class="col-12">
                    <b>* Fiyat teklifimizi onaylamanız durumunda kaşe/imza yapmanızı önemle rica ederiz.</b>
                </div>

                <div class="col-12 mt-2">
                    <div class="row text-center">
                        <div class="col-4">
                            Müşteri Onay Kaşe/İmza
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <div class="col-12">
                                    <b>Hazırlayan</b>
                                </div>
                                <div class="col-12">
                                    Aziz KALEM
                                </div>
                                <div class="col-12 text-muted small">
                                    Metalurji ve Malzeme Mühendisi
                                </div>
                                <div class="col-12">
                                    <img style="width: 100%" src="/img/doruk-aziz-imza.png" />
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <div class="col-12">
                                    <b>Onaylayan</b>
                                </div>
                                <div class="col-12">
                                    Gökhan ÇELİK
                                </div>
                                <div class="col-12 text-muted small">
                                    İnşaat Mühendisi
                                    <br />
                                    Genel Müdür
                                </div>
                                <div class="col-12">
                                    <img style="width: 100%" src="/img/doruk-gokhan-imza.png" />
                                </div>
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
    </div>
@endsection

@section('script')
    <script>
        function olcumTuruDegisti(event) {
            console.log(event);
        }

        let mixinApp = {
            data() {
                return {
                    aktifSayfa: {
                        kod: "ANASAYFA",
                        baslik: "Firma Yönetimi",
                    },
                    sayfalar: [
                        {
                            kod: "ANASAYFA",
                            baslik: "Firma Yönetimi",
                        },
                        {
                            kod: "YENI_FIRMA",
                            baslik: "Firma Kaydet",
                            geriFonksiyon: () => this.geriAnasayfa(),
                        },
                        {
                            kod: "TEKLIF_HAZIRLAMA",
                            baslik: "Teklif Hazırlama",
                            geriFonksiyon: () => {
                                this.teklif = _.cloneDeep(this.teklif._teklif);

                                this.geriAnasayfa();
                            },
                        },
                    ],
                    yukleniyorObjesi: {
                        firmalariGetir: false,
                        firmaSil: false,
                        firmaEkle: false,
                        birlestirilecekFirmalar: false,
                    },
                    firmalar: {},
                    yeniFirma: {
                        firmaAdi: "",
                        sorumluKisi: "",
                        telefon: "",
                        eposta: "",
                        adres: "",
                    },
                    filtrelemeObjesi: {
                        arama: "",
                        siralamaTuru: null,
                    },
                    birlestirmeObjesi: {
                        firmalar: [],
                        firma: null,
                        modal: null,
                    },
                    teklif: {
                        firma: {},
                        html: "",
                        htmlKey: 123,
                        icerikler: [],
                        // paraBirimleri: [
                        //     { ad: "Türk Lirası", kod: "TL", sembol: "₺" },
                        //     { ad: "Dolar", kod: "USD", sembol: "$" },
                        //     { ad: "Euro", kod: "EURO", sembol: "€" },
                        // ],
                        olcumTurleri: [
                            { ad: "Kilogram", kod: "KG", sembol: "KG" }
                        ],
                        paraBirimleri: @json($paraBirimleri),
                        teklifBilgileri: {},
                    },
                };
            },

            mounted() {
                this.gecikmeliFonksiyonCalistir(this.firmalariGetir);
                this.gecikmeliFonksiyonCalistir(this.teklifAlanlariDoldur, {
                    fonksiyonKey: "teklif"
                });

                this.firmalariGetir();
            },
            methods: {
                firmalariGetir(url = "{{ route('firmalariGetir') }}") {
                    this.yukleniyorObjesi.firmalariGetir = true;
                    axios.get(url, {
                            params: {
                                sayfalama: true,
                                filtreleme: this.filtrelemeObjesi,
                            }
                        })
                        .then(response => {
                            this.yukleniyorObjesi.firmalariGetir = false;

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
                            this.yukleniyorObjesi.firmalariGetir = false;
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data
                                    .hataKodu,
                                tur: "error"
                            });
                            console.log(error);
                        });
                },
                geriAnasayfa() {
                    this.aktifSayfa = _.find(this.sayfalar, {
                        kod: "ANASAYFA"
                    });

                    this.yeniFirma = {
                        firmaAdi: "",
                        sorumluKisi: "",
                        telefon: "",
                        eposta: "",
                        adres: "",
                    };

                    this.aktifSayfa = _.cloneDeep(this.aktifSayfa);
                },
                firmaDuzenle(firma) {
                    this.yeniFirma = _.cloneDeep(firma);

                    // this.yeniFirma.renk = _.find(this.renkler, ["kod", firma.json.renk]);
                    this.firmaEkleAc();
                    this.aktifSayfa.baslik = "Firma Düzenleme";
                },
                firmaSil(firma) {
                    const islem = (cikisDurum) => {
                        this.yukleniyorObjesi.firmaSil = true;

                        axios.post("{{ route('firmaSil') }}", {
                                id: firma.id,
                            })
                            .then(response => {
                                this.yukleniyorObjesi.firmaSil = false;

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

                                this.firmalariGetir();
                                this.geriAnasayfa();
                            })
                            .catch(error => {
                                this.yukleniyorObjesi.firmaSil = false;
                                this.uyariAc({
                                    baslik: 'Hata',
                                    mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response
                                        .data.hataKodu,
                                    tur: "error"
                                });
                                console.log(error);
                            });
                    };

                    Swal.fire({
                        title: "Uyarı",
                        text: `"${ firma.firmaAdi }" adlı firmayı silmek istediğinize emin misiniz?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sil',
                        cancelButtonText: 'İptal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            islem();
                        }
                    });
                },
                firmaEkleAc() {
                    this.aktifSayfa = _.cloneDeep(_.find(this.sayfalar, {
                        kod: "YENI_FIRMA"
                    }));
                },
                firmaEkle() {
                    if (!this.yeniFirma.firmaAdi) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: 'Fırın adı boş olamaz!',
                            tur: "error"
                        });
                    }

                    // if (!this.yeniFirma.sorumluKisi) {
                    //     return this.uyariAc({
                    //         baslik: 'Hata',
                    //         mesaj: 'Firma sorumlusu boş olamaz!',
                    //         tur: "error"
                    //     });
                    // }

                    // if (!this.yeniFirma.telefon) {
                    //     return this.uyariAc({
                    //         baslik: 'Hata',
                    //         mesaj: 'Telefon boş olamaz!',
                    //         tur: "error"
                    //     });
                    // }

                    this.yukleniyorObjesi.firmaEkle = true;

                    // this.yeniFirma.json = {
                    //     renk: this.yeniFirma.renk.kod
                    // };

                    axios.post('/firmaEkle', {
                        firma: this.yeniFirma,
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

                        const firma = response.data.firma;

                        this.uyariAc({
                            toast: {
                                status: true,
                                message: response.data.mesaj,
                            },
                        });

                        this.firmalariGetir();
                        this.geriAnasayfa();
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.firmaEkle = false;
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data
                                .hataKodu,
                            tur: "error"
                        });
                        console.log(error);
                    });
                },
                siralamaYap(alan){
                    if(this.filtrelemeObjesi.siralamaTuru && this.filtrelemeObjesi.siralamaTuru[alan]){
                        if(this.filtrelemeObjesi.siralamaTuru[alan]==='desc'){
                            this.filtrelemeObjesi.siralamaTuru[alan] = 'asc'
                        }
                        else {
                            this.filtrelemeObjesi.siralamaTuru[alan] = 'desc'
                        }
                    }
                    else {
                        this.filtrelemeObjesi.siralamaTuru = {
                            [alan]: "desc"
                        };
                    }
                    this.firmalariGetir();
                    // console.log(this.filtrelemeObjesi.siralamaTuru);
                },
                turkceKarakterCevir(str) {
                    return str
                        .replace(/[Çç]/g, "C")
                        .replace(/[Ğğ]/g, "G")
                        .replace(/[İı]/g, "I")
                        .replace(/[Öö]/g, "O")
                        .replace(/[Şş]/g, "S")
                        .replace(/[Üü]/g, "U");
                },
                firmaBirlestirmeAc() {
                    this.birlestirmeObjesi.modal = new bootstrap.Modal(document.getElementById("birlestirmeModal"));
                    this.birlestirmeObjesi.firma = null;

                    this.yukleniyorObjesi.birlestirilecekFirmalar = true;

                    return axios.get("/firmalariGetir")
                    .then(response => {
                        this.yukleniyorObjesi.birlestirilecekFirmalar = false;
                        if (!response.data.durum) {
                            return this.uyariAc({
                                baslik: 'Hata',
                                mesaj: response.data.mesaj,
                                tur: "error"
                            });
                        }

                        this.birlestirmeObjesi.firmalar = response.data.firmalar;

                        this.birlestirmeObjesi.modal.show();
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.birlestirilecekFirmalar = false;
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                        console.log(error);
                    });
                },
                firmalariBirlestir() {
                    const fun = () => {
                        this.yukleniyorObjesi.birlestirilecekFirmalar = true;

                        axios.post("/firmalariBirlestir", {
                            anaFirma: this.yeniFirma,
                            birlestirilecekFirma: this.birlestirmeObjesi.firma,
                        })
                        .then(response => {
                            this.yukleniyorObjesi.birlestirilecekFirmalar = false;
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

                            this.birlestirmeObjesi.modal.hide();
                            this.birlestirmeObjesi.firma = null;
                            this.firmalariGetir();
                        })
                        .catch(error => {
                            this.yukleniyorObjesi.birlestirilecekFirmalar = false;
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                                tur: "error"
                            });
                            console.log(error);
                        });
                    }

                    Swal.fire({
                        title: "Uyarı",
                        text: `Eğer devam ederseniz "${this.birlestirmeObjesi.firma.firmaAdi}" firmasındaki tüm işlemler (siparişler vs.),
                            "${this.yeniFirma.firmaAdi}" firmasına aktarılıp "${this.birlestirmeObjesi.firma.firmaAdi}" firması silinecektir.
                            Devam etmek istiyor musunuz?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Devam Et',
                        cancelButtonText: 'İptal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fun();
                        }
                    });
                },
                teklifOlusturmaAc(firma = {}) {
                    this.teklif._teklif = _.cloneDeep(this.teklif);

                    this.teklif.firma = {
                        firmaAdi: "",
                        sorumluKisi: "",
                        telefon: "",
                        adres: "",
                        eposta: "",
                        ...firma,
                    };

                    this.teklifIcerikEkle();

                    this.teklifAlanlariDoldur();

                    this.aktifSayfa = _.cloneDeep(_.find(this.sayfalar, {
                        kod: "TEKLIF_HAZIRLAMA"
                    }));
                },
                teklifAlanlariDoldur(teklifBilgileri = {}) {
                    this.teklif.teklifBilgileri = {
                        tarih: this.m().format("L"),
                        firma: this.teklif.firma.firmaAdi,
                        yetkili: this.teklif.firma.sorumluKisi ? this.teklif.firma.sorumluKisi : "---",
                        telefon: this.teklif.firma.telefon ? this.teklif.firma.telefon : "---",
                        adres: this.teklif.firma.adres ? this.teklif.firma.adres : "---",
                        eposta: this.teklif.firma.eposta ? this.teklif.firma.eposta : "---",
                        icerikler: this.teklif.icerikler,
                        gecerlilikTarihi: this.m().add(15, 'd').format("LL"),
                        ...teklifBilgileri,
                    };

                    const cloneRaporArea = this.$refs.teklif.cloneNode(true);
                    cloneRaporArea.style.display = "block";
                    cloneRaporArea.style.background = "white";

                    const compiled = _.template(
                        this.decodeHTMLEntities(
                            cloneRaporArea.outerHTML
                        )
                    );
                    this.teklif.html = compiled({
                        data: this.teklif.teklifBilgileri,
                    });

                    this.teklif.htmlKey = this.m().valueOf();
                },
                teklifIcerikEkle(icerik = {}, yenidenDoldur = false) {
                    this.teklif.icerikler.push({
                        ad: "",
                        fiyat: "",
                        paraBirimi: "TL",
                        olcumTuru: null,
                        ...icerik,
                    });

                    if (yenidenDoldur) {
                        this.teklifAlanlariDoldur();
                    }
                },
                teklifIcerikSil(index) {
                    this.teklif.icerikler.splice(index, 1);

                    if (!_.size(this.teklif.icerikler)) {
                        this.teklifIcerikEkle();
                    }

                    this.teklifAlanlariDoldur();
                },
                teklifOlustur() {
                    console.log(this.teklif.teklifBilgileri);
                    let url = encodeURIComponent(JSON.stringify(this.teklif.teklifBilgileri))
                    console.log(url);

                    this.yukleniyorObjesi.firmaEkle = true;

                    // this.yeniFirma.json = {
                    //     renk: this.yeniFirma.renk.kod
                    // };

                    const dosyaAdi = this.teklif.teklifBilgileri.firma ? this.teklif.teklifBilgileri.firma : "Teklif";

                    axios.post('/createPDF', {
                        data: url,
                        dosyaAdi,
                    }, {
                        responseType: 'blob',
                    })
                    .then(response => {
                        console.log(response);
                        this.yukleniyorObjesi.firmaEkle = false;

                        if (response.data && response.data.durum === false) {
                            return this.uyariAc({
                                baslik: 'Hata',
                                mesaj: response.data.mesaj,
                                tur: "error"
                            });
                        }

                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', 'teklif.pdf');
                        link.click();

                        this.uyariAc({
                            toast: {
                                status: true,
                                message: response.data.mesaj,
                            },
                        });

                        this.aktifSayfa.geriFonksiyon();
                    })
                    .catch(async error => {
                        console.log(error);
                        this.yukleniyorObjesi.firmaEkle = false;
                        error = JSON.parse(await error.response.data.text());
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.mesaj + " - Hata Kodu: " + error.hataKodu,
                            tur: "error"
                        });
                    });
                },
                decodeHTMLEntities(text) {
                    let textArea = document.createElement('textarea');
                    textArea.innerHTML = text;
                    return textArea.value;
                },
                paraBirimiFormatla(icerik) {
                    let value = icerik.fiyat.trim();

                    if (value == "") return;

                    let sadeceSayiArray = value.match(/\d/g);

                    if (!sadeceSayiArray) return;

                    let sadeceSayi = sadeceSayiArray.join("");

                    if (value != sadeceSayi) {
                        value = sadeceSayi;
                    }

                    value = value.replace(/,/g, '.');

                    icerik.fiyat = parseFloat(value).toLocaleString('tr-TR', {
                        style: 'decimal',
                        maximumFractionDigits: 2,
                        minimumFractionDigits: 2
                    });

                    this.teklifAlanlariDoldur();
                },
            }
        };
    </script>
@endsection

@section('style')
    <link rel="stylesheet" href="/css/print.css">
@endsection