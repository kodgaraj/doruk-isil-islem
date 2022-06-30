@extends('layout')
@section('content')
    <div class="row doruk-content">
        <h4 style="color:#999"><i class="fa fa-home"></i>FİRMALAR</h4>
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
                            <button v-if="aktifSayfa.kod === 'ANASAYFA'" class="btn btn-sm btn-primary"
                                @click="firmaEkleAc()">
                                <i class="fa fa-plus"></i> FİRMA EKLE
                            </button>
                            <!-- firma KAYDET BUTONU -->
                            <button v-if="aktifSayfa.kod === 'YENI_FIRMA' && !yukleniyorObjesi.firmaEkle"
                                class="btn btn-primary" @click="firmaEkle()">
                                <i class="fa fa-save"></i> FİRMA KAYDET
                            </button>
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
                                                <th>Firmalar</th>
                                                <th class="text-center">Firma Sorumlusu</th>
                                                <th class="text-center">Telefon</th>
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
                                                <button class="page-link" :disabled="!firmalar.prev_page_url" @click="firmalariGetir(firmalar.prev_page_url)">Önceki</button>
                                            </li>
                                            <li
                                                v-for="sayfa in firmalar.last_page"
                                                class="page-item"
                                                :class="[firmalar.current_page === sayfa ? 'active' : '']"
                                            >
                                                <button class="page-link" @click='firmalariGetir("{{ route("firmalariGetir") }}?page=" + sayfa)'>@{{ sayfa }}</button>
                                            </li>
                                            <li class="page-item">
                                                <button class="page-link" :disabled="!firmalar.next_page_url" @click="firmalariGetir(firmalar.next_page_url)">Sonraki</button>
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
                                <input v-model="yeniFirma.sorumluKisi" autocomplete="off" id="firma_sorumlusu" type="text" class="form-control"
                                    placeholder="Firma Sorumlusu" />
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
                            baslik: "Firma Düzenleme",
                            geriFonksiyon: () => this.geriAnasayfa(),
                        },
                    ],
                    yukleniyorObjesi: {
                        firmalariGetir: false,
                        firmaSil: false,
                        firmaEkle: false,
                    },
                    firmalar: {},
                    yeniFirma: {
                        firmaAdi: "",
                        sorumluKisi: "",
                        telefon: null,
                    },
                };
            },

            mounted() {
                this.firmalariGetir();
            },
            methods: {
                firmalariGetir(url = "{{ route('firmalariGetir') }}") {
                    this.yukleniyorObjesi.firmalariGetir = true;
                    axios.get(url, {
                            params: {
                                sayfalama: true,
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
                    this.aktifSayfa = _.find(this.sayfalar, {
                        kod: "YENI_FIRMA"
                    });
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
                turkceKarakterCevir(str) {
                    return str
                        .replace(/[Çç]/g, "C")
                        .replace(/[Ğğ]/g, "G")
                        .replace(/[İı]/g, "I")
                        .replace(/[Öö]/g, "O")
                        .replace(/[Şş]/g, "S")
                        .replace(/[Üü]/g, "U");
                },
            }
        };
    </script>
@endsection
