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
                                <div class="col-auto">
                                    <button v-if="aktifSayfa.kod === 'ANASAYFA'" class="btn btn-sm btn-primary"
                                        @click="firmaEkleAc()">
                                        <i class="fa fa-plus"></i> FİRMA EKLE
                                    </button>
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
                                <label for="firma_sorumlusu">Firma Sorumlusu *</label>
                                <input v-model="yeniFirma.sorumluKisi" autocomplete="off" id="firma_sorumlusu"
                                    type="text" class="form-control" placeholder="Firma Sorumlusu" />
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="telefon">Telefon</label>
                                <input v-model="yeniFirma.telefon" autocomplete="off" id="telefon" type="text"
                                    class="form-control" placeholder="Telefon" />
                            </div>
                            {{-- <div class="col-12 col-sm-6 col-md-4">
                                <label for="renk">Renk *</label>
                                <v-select v-model="yeniFirma.renk" :options="renkler" label="ad"
                                    id="renk">
                                    <template slot="option" slot-scope="renk">
                                        <span :class="'bg-' + renk.kod" class="p-2 me-1"></span>
                                        @{{ renk.ad }}
                                    </template>
                                    <div slot="no-options">Renk bulunamadı!</div>
                                </v-select>
                            </div> --}}
                        </div>
                    </template>
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
                        baslik: "Firma Yönetimi",
                    },
                    sayfalar: [{
                            kod: "ANASAYFA",
                            baslik: "Firma Yönetimi",
                        },
                        {
                            kod: "YENI_FIRMA",
                            baslik: "Firma Kaydet",
                            geriFonksiyon: () => this.geriAnasayfa(),
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
                        telefon: null,
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
                };
            },

            mounted() {
                this.gecikmeliFonksiyonCalistir(this.firmalariGetir);

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
                        telefon: null,
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

                    if (!this.yeniFirma.sorumluKisi) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: 'Firma sorumlusu boş olamaz!',
                            tur: "error"
                        });
                    }

                    if (!this.yeniFirma.telefon) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: 'Telefon boş olamaz!',
                            tur: "error"
                        });
                    }

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
            }
        };
    </script>
@endsection
