@extends('layout')
@section('style')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link href="https://unpkg.com/quill-table-ui@1.0.5/dist/index.css" rel="stylesheet">
<style>
    .a4 {
        width: 21cm;
        min-height: 29.7cm;
        padding: 2cm;
        margin: 1cm auto;
        border: 1px #D3D3D3 solid;
        border-radius: 5px;
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection
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
                                        @click="teklifModalAc()">
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
                <div class="modal fade" id="sablonModal" tabindex="-1" aria-labelledby="sablonModalTitle" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="sablonModalTitle">Şablonlar</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="col m-0">
                                    <div class="form-group">
                                        <label for="sablonlar">Şablonlar</label>
                                        <v-select
                                            v-model="sablonObjesi.sablon"
                                            :options="sablonObjesi.sablonlar"
                                            label="sablonAdi"
                                            id="id"
                                        ></v-select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    @click="teklifOlusturmaAc()"
                                >
                                Teklif Oluştur
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
                                                        <button class="btn btn-sm btn-outline-info" @click="teklifModalAc(firma)">
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
                            <div class="col-12 col-sm-6 col-md-4" v-if="sablonObjesi.sablon.kullanilabilirOgeler.includes('[firmaAdi]')">
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
                            <div class="col-12 col-sm-6 col-md-4" v-if="sablonObjesi.sablon.kullanilabilirOgeler.includes('[sorumluKisi]')">
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
                            <div class="col-12 col-sm-6 col-md-4" v-if="sablonObjesi.sablon.kullanilabilirOgeler.includes('[telefon]')">
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
                            <div class="col-12 col-sm-6 col-md-4" v-if="sablonObjesi.sablon.kullanilabilirOgeler.includes('[eposta]')">
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
                            <div class="col-12 col-sm-6 col-md-4" v-if="sablonObjesi.sablon.kullanilabilirOgeler.includes('[adres]')">
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
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="telefon">Şablon</label>
                                <textarea
                                    v-model="sablonObjesi.sablon.sablonAdi"
                                    autocomplete="off"
                                    disabled
                                    id="sablon"
                                    type="text"
                                    class="form-control"
                                    placeholder="Şablon"
                                    rows="1"
                                    cols="50"
                                ></textarea>
                            </div>
                            <div class="col-12">
                                <span class="badge badge-pill bg-primary" style="font-size: 90%; margin: 3px;" v-for="ogeler in sablonObjesi.sablon.kullanilabilirOgeler">@{{ogeler}}</span>
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
                                        <select
                                            v-model="icerik.ad"
                                            @change="gecikmeliFonksiyon.teklif()"
                                            name="islem"
                                            id="ad"
                                            class="small form-control"
                                        >
                                            <option value="" disabled>İşlem türünü seçiniz...</option>
                                            <option
                                                v-for="(islem, pIndex) in teklif.islemTurleri"
                                                :value="islem.ad"
                                                :key="pIndex"
                                            >
                                                @{{ islem.ad }}
                                            </option>
                                        </select>
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
                            <div class="col-12 col-sm-12 col-md-12">
                                <div class="card" v-for="(sayi, index) in teklif.teklifBilgileri.icerik_html.length">
                                    <div class="card-body ql-editor a4 p-0">
                                        <div v-html="teklif.teklifBilgileri.icerik_html[index]"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </template>
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
                    sablonObjesi:{
                        sablonlar:[],
                        sablon: {},
                        firma: {},
                        modal:null,
                    },
                    teklif: {
                        firma: {},
                        baslik: "Doruk Isıl İşlem",
                        html: "",
                        htmlKey: 123,
                        icerikler: [],
                        islemTurleri:[],
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

                    this.yukleniyorObjesi.firmaEkle = true;

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
                teklifModalAc(firma = {}) {

                    this.sablonObjesi.modal = new bootstrap.Modal(document.getElementById("sablonModal"));
                    this.sablonObjesi.sablon = null;
                    this.sablonObjesi.firma = firma;
                    return axios.get("/sablonlariGetir",{
                        params: {
                            tur: "TEKLIF",
                        },
                    })
                    .then(response => {
                        if (!response.data.durum) {
                            return this.uyariAc({
                                baslik: 'Hata',
                                mesaj: response.data.mesaj,
                                tur: "error"
                            });
                        }
                        this.sablonObjesi.sablonlar = response.data.sablonlar;

                        this.sablonObjesi.modal.show();
                    })
                    .catch(error => {
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                        console.log(error);
                    });
                },
                teklifOlusturmaAc() {
                    this.sablonObjesi.modal.hide();
                    this.islemTurleriGetir();
                    this.teklif._teklif = _.cloneDeep(this.teklif);

                    this.teklif.firma = {
                        firmaAdi: "",
                        sorumluKisi: "",
                        telefon: "",
                        adres: "",
                        eposta: "",
                        ...this.sablonObjesi.firma,
                    };

                    this.teklifIcerikEkle();

                    this.teklifAlanlariDoldur();

                    this.aktifSayfa = _.cloneDeep(_.find(this.sayfalar, {
                        kod: "TEKLIF_HAZIRLAMA"
                    }));
                },
                islemTurleriGetir(){
                    axios.get("{{ route('islemTurleriGetir') }}")
                        .then(response => {
                            if (!response.data.durum) {
                                return this.uyariAc({
                                    baslik: 'Hata',
                                    mesaj: response.data.mesaj,
                                    tur: "error"
                                });
                            }

                            this.teklif.islemTurleri = response.data.islemTurleri;
                        })
                        .catch(error => {
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data
                                    .hataKodu,
                                tur: "error"
                            });
                            console.log(error);
                        });
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
                        tur: this.sablonObjesi.sablon.tur,
                        klasor: this.teklif.firma.id,
                        gecerlilikTarihi: this.m().add(15, 'd').format("LL"),
                        ...teklifBilgileri,
                    };

                    this.teklif.teklifBilgileri.icerik_html = _.assignIn([], this.sablonObjesi.sablon.icerik_html);
                    for (let index = 0; index < this.teklif.teklifBilgileri.icerik_html.length; index++) {
                        this.teklif.teklifBilgileri.icerik_html[index] = this.teklif.teklifBilgileri.icerik_html[index].replaceAll('[baslik]', this.teklif.baslik + " " + this.sablonObjesi.sablon.tur + " Formu" );
                        this.teklif.teklifBilgileri.icerik_html[index] = this.teklif.teklifBilgileri.icerik_html[index].replaceAll('[firmaAdi]', this.teklif.firma.firmaAdi);
                        this.teklif.teklifBilgileri.icerik_html[index] = this.teklif.teklifBilgileri.icerik_html[index].replaceAll('[sorumluKisi]', this.teklif.firma.sorumluKisi);
                        this.teklif.teklifBilgileri.icerik_html[index] = this.teklif.teklifBilgileri.icerik_html[index].replaceAll('[telefon]', this.teklif.firma.telefon);
                        this.teklif.teklifBilgileri.icerik_html[index] = this.teklif.teklifBilgileri.icerik_html[index].replaceAll('[adres]', this.teklif.firma.adres);
                        this.teklif.teklifBilgileri.icerik_html[index] = this.teklif.teklifBilgileri.icerik_html[index].replaceAll('[eposta]', this.teklif.firma.eposta);
                        let iceriklerHtml = "<div class='table-rep-plugin'><div class='table-responsive mb-0'><table class='table table-striped'><thead><tr><th>İşlem</th><th>Fiyat</th><th>Ölçü</th><th>Birim</th></tr></thead><tbody>";
                        this.teklif.teklifBilgileri.icerikler.forEach(icerik => {
                            iceriklerHtml += `<tr><td>${icerik.ad}</td><td>${icerik.fiyat}</td><td>${icerik.olcumTuru ? icerik.olcumTuru : ""}</td><td>${icerik.paraBirimi}</td></tr>`;
                        });
                        iceriklerHtml += "</tbody></table></div></div>";
                        this.teklif.teklifBilgileri.icerik_html[index] = this.teklif.teklifBilgileri.icerik_html[index].replaceAll('[teklifIcerikleri]', iceriklerHtml);
                        this.teklif.teklifBilgileri.icerik_html[index] = this.teklif.teklifBilgileri.icerik_html[index].replaceAll('[tarih]', this.m().format("L"));
                        this.teklif.teklifBilgileri.icerik_html[index] = this.teklif.teklifBilgileri.icerik_html[index].replaceAll('[gecerlilikTarihi]', this.m().add(15, 'd').format("LL"));
                    }
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

                    this.yukleniyorObjesi.firmaEkle = true;
                    // this.teklif.teklifBilgileri.icerik_html = JSON.stringify(this.teklif.teklifBilgileri.icerik_html);
                    // let url = encodeURIComponent(JSON.stringify(this.teklif.teklifBilgileri));
                    const dosyaAdi = this.turkceKarakterCevir(this.teklif.teklifBilgileri.firma ? this.teklif.teklifBilgileri.firma : "TEKLIF");
                    // this.teklif.teklifBilgileri.icerik_html = JSON.stringify(this.teklif.teklifBilgileri.icerik_html);
                    // window.location.href = "{{ route('pdfExports', ['tur' => 'TEKLIF', 'yazdir' => 1]) }}" + "&q=" + data;
                    axios.post('/teklifEkle', {
                            firmaId: this.teklif.firma.id,
                            tur: this.sablonObjesi.sablon.tur,
                            html: JSON.stringify(this.teklif.teklifBilgileri.icerik_html),
                            teklifBilgileri: this.teklif.teklifBilgileri,
                            dosyaAdi,
                        }).then(response => {
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
                            this.aktifSayfa.geriFonksiyon();
                        })
                        .catch(error => {
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data
                                    .hataKodu,
                                tur: "error"
                            });
                            console.log(error);
                            // window.location.href = "{{ route('pdfExports2', ['tur' => 'TEKLIF', 'id' => '42']) }}";
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
