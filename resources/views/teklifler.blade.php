@extends('layout')

@section('content')
    <div class="row doruk-content">
        <h4 style="color:#999"><i class="fa fa-home"></i> TEKLİFLER</h4>
        <div class="col-12">
            <div class="card" key="ANASAYFA">
                <div class="card-header">
                    <div class="row d-flex align-items-center">
                        <div class="col">
                            <h4>
                                <button class="btn btn-warning" v-if="aktifSayfa.geriFonksiyon"
                                    @click="aktifSayfa.geriFonksiyon()">
                                    <i class="fa fa-arrow-left"></i> GERİ
                                </button>
                                @{{ aktifSayfa.baslik }}
                            </h4>
                        </div>
                        <div class="col-auto">
                            <div class="row d-flex align-items-center">
                                <div class="col-auto">
                                    <button :class="{'btn btn-sm btn-warning' : filtrelemeObjesi.topluTeklifleriGetir, 'btn btn-sm btn-primary' : !filtrelemeObjesi.topluTeklifleriGetir}"
                                        @click="topluTeklifleriGetir()">
                                        <i class="fas fa-object-group"></i> @{{filtrelemeObjesi.topluTeklifleriGetir ? 'TEKLİFLERİ GETİR' : 'TOPLU TEKLİFLERİ GETİR'}}
                                    </button>

                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <input
                                            v-model="filtrelemeObjesi.arama"
                                            type="text"
                                            class="form-control"
                                            placeholder="Arama"
                                            aria-label="Arama"
                                            aria-describedby="arama"
                                            @keyup.enter="teklifleriGetir()"
                                            @input="gecikmeliFonksiyon.varsayilan()"
                                        />
                                        <span @click="teklifleriGetir()" class="input-group-text waves-effect" id="arama">
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
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <small class="text-muted">
                                        Teklif adı, Firma Adı, Teklif Türü, Oluşturma Tarihi...
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
                                                        <label for="islemDurumuFiltre">Teklif Adı</label>
                                                        <input
                                                            v-model="filtrelemeObjesi.teklifAdi"
                                                            label="teklifAdi"
                                                            class="form-control"
                                                            id="teklifAdiFiltre"
                                                        >
                                                    </div>
                                                </div>
                                                {{-- <div class="col-12 m-0">
                                                    <div class="form-group">
                                                        <label for="islemDurumuFiltre">Teklif Türü</label>
                                                        <v-select
                                                            v-model="filtrelemeObjesi.tur"
                                                            :options="sablonlar"
                                                            label="tur"
                                                            multiple
                                                            id="teklifTuruFiltre"
                                                        ></v-select>
                                                    </div>
                                                </div> --}}
                                                <div class="col-12 m-0">
                                                    <div class="input-group">
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
                                                        <span @click="tarihleriTemizle()" class="input-group-text waves-effect" id="tarihTemizle">
                                                            <i class="fa fa-eraser"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">VAZGEÇ</button>
                                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="teklifleriGetir()">ARA</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <template v-if="aktifSayfa.kod === 'ANASAYFA'">
                        <template v-if="yukleniyorObjesi.teklifleriGetir">
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
                                                <th @click="siralamaYap('id')">ID</th>
                                                <th @click="siralamaYap('topluKey')">Toplu Teklif ID'si</th>
                                                <th @click="siralamaYap('teklifAdi')">Teklif Adı</th>
                                                <th @click="siralamaYap('tur')" class="text-center">Teklif Türü</th>
                                                <th @click="siralamaYap('created_at')" class="text-center">Oluşturma Tarihi </th>
                                                <th class="text-center">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-if="_.size(teklifler.data) && filtrelemeObjesi.topluTeklifleriGetir">
                                                <tr v-for="(grup, index) in grupluTeklifler" :key="index">
                                                    <td>#---</td>
                                                    <td><span class="badge badge-pill bg-danger"># @{{ grup[0].topluKey }} </span></td>
                                                    <td class="uzun-uzunluk">
                                                        <div class="col-12">
                                                            @{{ grup[0].tur }} FORMU
                                                        </div>

                                                    </td>
                                                    <td class="kisa-uzunluk text-center">
                                                        @{{ grup[0].tur }}
                                                    </td>
                                                    <td class="kisa-uzunluk text-center">
                                                        @{{ m(grup[0].created_at).format("L LTS") }}
                                                    </td>
                                                    <td class="kisa-uzunluk text-center">
                                                            <button class="btn btn-sm btn-warning"
                                                            @click="sablonModalAc({'topluKey' : grup[0].topluKey})">
                                                            <i class="fa fa-envelope"></i> TOPLU MAİL GÖNDER
                                                        </button>
                                                    </td>
                                                </tr>

                                            </template>
                                            <template v-else-if="_.size(teklifler.data)">
                                                <tr v-for="(teklif, index) in teklifler.data" :key="index">
                                                    <td># @{{ teklif.id }}</td>
                                                    <td><span :class="{'badge badge-pill bg-danger' : teklif.topluKey }"># @{{ teklif.topluKey ?? '---' }} </span></td>
                                                    <td class="uzun-uzunluk">
                                                        <div class="col-12">
                                                            @{{ teklif.teklifAdi }}
                                                        </div>

                                                    </td>
                                                    <td class="kisa-uzunluk text-center">
                                                        @{{ teklif.tur }}
                                                    </td>
                                                    <td class="kisa-uzunluk text-center">
                                                        @{{ m(teklif.created_at).format("L LTS") }}
                                                    </td>
                                                    <td class="kisa-uzunluk text-center">
                                                        <button class="btn btn-sm btn-success" @click="teklifGoruntule(teklif)">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" @click="teklifSil(teklif)">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-primary"
                                                            @click="sablonModalAc(teklif)">
                                                            <i class="fa fa-envelope"></i> MAİL GÖNDER
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
                                        <tfoot>
                                    </table>
                                </div>
                            </div>

                        </template>
                    </template>
                    <template v-if="aktifSayfa.kod === 'TEKLIF_GORUNTULE'">
                        <div class="row">
                            <iframe :src='url' width="100%" height="600px"></iframe>
                        </div>
                    </template>
                    <div class="card-footer">
                        <div class="row d-flex align-items-center justify-content-between">
                            <div class="col-auto"></div>
                            <div class="col">
                                <ul class="pagination pagination-rounded justify-content-center mb-0">
                                    <li class="page-item">
                                        <button class="page-link" :disabled="!teklifler.prev_page_url" @click="teklifleriGetir(teklifler.prev_page_url)">
                                            <i class="fas fa-angle-left"></i>
                                        </button>
                                    </li>
                                    <li
                                        v-for="sayfa in sayfalamaAyarla(teklifler.last_page, teklifler.current_page)"
                                        class="page-item"
                                        :class="[sayfa.aktif ? 'active' : '']"
                                    >
                                        <button class="page-link" @click="sayfa.tur === 'SAYFA' ? teklifleriGetir(`{{ route("teklifleriGetir") }}?page=` + sayfa.sayfa) : ()  => {}">@{{ sayfa.sayfa }}</button>
                                    </li>
                                    <li class="page-item">
                                        <button class="page-link" :disabled="!teklifler.next_page_url" @click="teklifleriGetir(teklifler.next_page_url)">
                                            <i class="fas fa-angle-right"></i>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-auto">
                                <small class="text-muted">Toplam Kayıt: @{{ teklifler.total }}</small>
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
                                <div class="col m-0" v-if="">
                                    <div class="col m-0" v-if="filtrelemeObjesi.topluTeklifleriGetir">
                                        <div class="form-group">
                                            <label for="">Toplu Teklif ID'si</label>
                                            <input class="form-control" type="text" disabled v-model="sablonObjesi.teklif.topluKey">
                                        </div>
                                    </div>
                                    <div class="form-group" v-else>
                                        <label for="">Kime</label>
                                        <input class="form-control" type="text" v-model="sablonObjesi.teklif.eposta">
                                    </div>
                                </div>
                                <div class="col m-0">
                                    <div class="form-group">
                                        <label for="">CC</label>
                                        <input class="form-control" type="text" v-model="sablonObjesi.teklif.cc">
                                    </div>
                                </div>
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
                                    @click="mailGonder()"
                                >
                                Mail Gönder
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">VAZGEÇ</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script>

        let mixinApp = {

            data() {
                return {
                    aktifSayfa: {
                        kod: "ANASAYFA",
                        baslik: "Teklif Yönetimi",
                    },
                    sayfalar: [{
                            kod: "ANASAYFA",
                            baslik: "Teklif Yönetimi",
                        },
                        {
                            kod: "TEKLIF_GORUNTULE",
                            baslik: "Teklif Görüntüle",
                            geriFonksiyon: () => this.geriAnasayfa(),
                        },
                    ],
                    yukleniyorObjesi: {
                        teklifleriGetir: false,
                        teklifSil: false,
                        teklifGoruntule: false,
                        mailGonder: false,
                    },
                    filtrelemeObjesi: {
                        arama: "",
                        baslangicTarihi: "",
                        bitisTarihi: "",
                        teklifAdi: "",
                        tur: "",
                        topluTeklifleriGetir: false,
                        firmaId: @json($firmaId ? $firmaId : ""),
                        siralamaTuru:[]
                    },
                    sayfalamaSayilari: [10, 25, 50, 100],
                    sayfalamaSayisi: 10,
                    sablonObjesi:{
                        sablonlar:[],
                        sablon: {},
                        teklif: {},
                        modal:null,
                    },
                    sablonlar: @json($sablonlar),
                    teklifler: [],
                    url: "",
                };
            },
            mounted() {
                this.gecikmeliFonksiyonCalistir(this.teklifleriGetir);
                this.teklifleriGetir();
            },
            methods: {
                teklifleriGetir(url = "{{ route('teklifleriGetir') }}") {
                    this.yukleniyorObjesi.teklifleriGetir = true;
                    axios.get(url, {
                        params: {
                            filtreleme: this.filtrelemeObjesi,
                            sayfalama: true,
                            sayfalamaSayisi: this.sayfalamaSayisi,
                        },
                    }).then(response => {
                            this.yukleniyorObjesi.teklifleriGetir = false;

                            if (!response.data.durum) {
                                return this.uyariAc({
                                    baslik: 'Hata',
                                    mesaj: response.data.mesaj,
                                    tur: "error"
                                });
                            }

                            this.teklifler = response.data.teklifler;

                        })
                        .catch(error => {
                            this.yukleniyorObjesi.teklifleriGetir = false;
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data
                                    .hataKodu,
                                tur: "error"
                            });
                            console.log(error);
                        });
                },
                topluTeklifleriGetir(){
                    this.filtrelemeObjesi.topluTeklifleriGetir = this.filtrelemeObjesi.topluTeklifleriGetir ? false : true;
                    this.sablonObjesi = {
                        sablonlar:[],
                        sablon: {},
                        teklif: {},
                        modal:null,
                    },
                    this.sayfalamaSayisi = this.filtrelemeObjesi.topluTeklifleriGetir ? 100 : 10;
                    this.teklifleriGetir();
                },
                geriAnasayfa() {
                    this.aktifSayfa = _.find(this.sayfalar, {
                        kod: "ANASAYFA"
                    });

                    this.aktifSayfa = _.cloneDeep(this.aktifSayfa);
                },
                teklifSil(teklif) {
                    const islem = (cikisDurum) => {
                        this.yukleniyorObjesi.teklifSil = true;

                        axios.post("{{ route('teklifSil') }}", {
                                id: teklif.id,
                            })
                            .then(response => {
                                this.yukleniyorObjesi.teklifSil = false;

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

                                this.teklifleriGetir();
                                this.geriAnasayfa();
                            })
                            .catch(error => {
                                this.yukleniyorObjesi.teklifSil = false;
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
                        text: `"${ teklif.teklifAdi }" adlı Teklifi silmek istediğinize emin misiniz?`,
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
                teklifGoruntule(teklif) {

                    this.aktifSayfa = _.find(this.sayfalar, {
                        kod: "TEKLIF_GORUNTULE"
                    });
                    this.url = "/" + teklif.url
                },
                sablonModalAc(teklif = {}) {
                    this.sablonObjesi.modal = new bootstrap.Modal(document.getElementById("sablonModal"));
                    this.sablonObjesi.sablon = null;
                    this.sablonObjesi.teklif = teklif;
                    return axios.get("/sablonlariGetir",{
                        params: {
                            tur: "MAIL",
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
                teklifAlanlariDoldur(teklif) {

                    this.sablonObjesi.teklif = {
                        tarih: this.m().format("L"),
                        ...teklif,
                    };

                    this.sablonObjesi.teklif.icerik_html = _.assignIn([], this.sablonObjesi.sablon.icerik_html);
                    for (let index = 0; index < this.sablonObjesi.teklif.icerik_html.length; index++) {
                        this.sablonObjesi.teklif.icerik_html[index] = this.sablonObjesi.teklif.icerik_html[index].replaceAll('[firmaAdi]', this.sablonObjesi.teklif.firmaAdi);
                        this.sablonObjesi.teklif.icerik_html[index] = this.sablonObjesi.teklif.icerik_html[index].replaceAll('[eposta]', this.sablonObjesi.teklif.eposta);
                        this.sablonObjesi.teklif.icerik_html[index] = this.sablonObjesi.teklif.icerik_html[index].replaceAll('[tur]', this.sablonObjesi.teklif.tur);
                        this.sablonObjesi.teklif.icerik_html[index] = this.sablonObjesi.teklif.icerik_html[index].replaceAll('[tarih]', this.m().format("L"));
                     }
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
                    this.teklifleriGetir();
                },

                tarihleriTemizle() {
                    this.filtrelemeObjesi.baslangicTarihi = "";
                    this.filtrelemeObjesi.bitisTarihi = "";
                },
                mailGonder() {
                    this.yukleniyorObjesi.mailGonder = true;
                    this.sablonObjesi._teklif = _.cloneDeep(this.sablonObjesi.teklif);

                    const fun = async (index) => {
                        return new Promise((resolve) => {
                            if (index !== null && this.sablonObjesi.teklif.topluKey && index >= this.grupluTeklifler[this.sablonObjesi.teklif.topluKey].length) {
                                this.yukleniyorObjesi.mailGonder = false;
                                this.sablonObjesi.modal.hide();
                                setTimeout(() => {
                                    this.uyariAc({
                                    toast: {
                                            status: true,
                                            message: this.sablonObjesi.teklif.topluKey + " ID'li Toplu Teklif Maili Tüm Alıcılara Gönderildi",
                                        },
                                    });
                                }, 2000);
                                resolve();
                                return;
                            }

                            let teklif;

                            if (index === null) {
                                this.teklifAlanlariDoldur(this.sablonObjesi.teklif);
                            } else {
                                this.teklifAlanlariDoldur(this.grupluTeklifler[this.sablonObjesi._teklif.topluKey][index]);
                            }
                            console.log(index, this.sablonObjesi.teklif);

                            axios.post('/mailGonder', {
                                teklif: this.sablonObjesi.teklif,
                            })
                            .then(response => {
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
                                if (index === null) {
                                    this.yukleniyorObjesi.mailGonder = false;
                                    this.sablonObjesi.modal.hide();
                                    console.log("indexe girmedi");
                                    resolve();
                                    return;
                                }else{
                                    console.log("index artı 1 e girdi");
                                    resolve(fun(index + 1));
                                }

                            })
                            .catch(error => {
                                this.yukleniyorObjesi.teklifEkle = false;
                                this.uyariAc({
                                    baslik: 'Hata',
                                    mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data
                                        .hataKodu,
                                    tur: "error"
                                });
                                console.log(error);
                                resolve(fun(index + 1));
                            })
                        });
                    }

                    if(this.filtrelemeObjesi.topluTeklifleriGetir){
                        fun(0);
                    }else {
                        if(this.sablonObjesi.teklif.eposta != null && this.sablonObjesi.teklif.eposta != "") {
                            fun(null);
                        } else {  this.uyariAc({toast: {status: false,message: this.sablonObjesi.teklif.firmaAdi + " Firma Bilgilerinde Mail Mevcut Değil."}});}
                    }
                }
            },
            computed: {
                grupluTeklifler() {
                    const grupluTeklifler = {};
                    this.teklifler.data.forEach((teklif) => {
                        if (teklif.topluKey !== null) {
                            if (!grupluTeklifler[teklif.topluKey]) {
                            grupluTeklifler[teklif.topluKey] = [];
                            }
                            grupluTeklifler[teklif.topluKey].push(teklif);
                        }
                    });
                    return grupluTeklifler;
                }
            },
        };
    </script>
@endsection
