@extends('layout')
@section('content')
    <div class="row doruk-content">
        <h4 style="color:#999"><i class="fa fa-home"></i> FIRINLAR</h4>
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
                                @click="firinEkleAc()">
                                <i class="fa fa-plus"></i> FIRIN EKLE
                            </button>
                            <!-- firin KAYDET BUTONU -->
                            <button v-if="aktifSayfa.kod === 'YENI_FIRIN' && !yukleniyorObjesi.firinKaydet" class="btn btn-primary" @click="firinKaydet()">
                                <i class="fa fa-save"></i> FIRIN KAYDET
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <template v-if="aktifSayfa.kod === 'ANASAYFA'">
                        <template v-if="yukleniyorObjesi.firinlariGetir">
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
                                                <th>Fırınlar</th>
                                                <th class="text-center">Fırın işlem Sayısı</th>
                                                <th class="text-center">İşlemler</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(firin, index) in firinlar" :key="index">
                                                <td># @{{ firin.id }}</td>
                                                <td class="uzun-uzunluk">
                                                    <div class="col-12">
                                                        @{{ firin.ad }}
                                                    </div>
                                                    <div class="col-12">
                                                        <span class="badge" :class="'bg-' + firin.json.renk">
                                                            @{{ firin.kod }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="kisa-uzunluk text-center">
                                                    @{{ firin.islemSayisi }}
                                                </td>
                                                <td class="kisa-uzunluk text-center">
                                                    <button class="btn btn-sm btn-primary" @click="firinDuzenle(firin)">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" @click="firinSil(firin)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                    </table>
                                </div>
                            </div>

                        </template>
                    </template>
                    <template v-else-if="aktifSayfa.kod === 'YENI_FIRIN'">
                        <!-- fırınEkle -->
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="name">Fırın adı *</label>
                                <input v-model="yeniFirin.ad" autocomplete="off" id="name" type="text"
                                    class="form-control" placeholder="Fırın adı" />
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="kod">Fırın kodu *</label>
                                <input readonly :value="firinKoduOlustur" autocomplete="off" id="kod" type="text"
                                    class="form-control" placeholder="Fırın kodu" />
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="renk">Renk *</label>
                                <v-select v-model="yeniFirin.renk" :options="renkler" label="ad"
                                    id="renk">
                                    <template slot="option" slot-scope="renk">
                                        <span :class="'bg-' + renk.kod" class="p-2 me-1"></span>
                                        @{{renk.ad}}
                                    </template>
                                    <div slot="no-options">Renk bulunamadı!</div>
                                </v-select>
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
        let mixinApp = {
            data() {
                return {
                    renkler: @json($renkler),
                    aktifSayfa: {
                        kod: "ANASAYFA",
                        baslik: "Fırın Yönetimi",
                    },
                    sayfalar: [{
                            kod: "ANASAYFA",
                            baslik: "Fırın Yönetimi",
                        },
                        {
                            kod: "YENI_FIRIN",
                            baslik: "Fırın Düzenleme",
                            geriFonksiyon: () => this.geriAnasayfa(),
                        },
                    ],
                    yukleniyorObjesi: {
                        firinlariGetir: false,
                        firinSil: false,
                        firinKaydet: false,
                    },
                    firinlar: [],
                    yeniFirin: {
                        ad: "",
                        kod: "",
                        renk: null,
                    },
                };
            },
            computed: {
                firinKoduOlustur() {
                    const slug = _.toUpper(this.turkceKarakterCevir(this.yeniFirin.ad));

                    this.yeniFirin.kod = _.replace(slug, /[^a-zA-Z]+/g, "_");

                    return this.yeniFirin.kod;
                },
            },
            mounted() {
                this.firinlariGetir();
            },
            methods: {
                firinlariGetir(url = "{{ route('firinlariGetir') }}") {
                    this.yukleniyorObjesi.firinlariGetir = true;
                    axios.get(url)
                        .then(response => {
                            this.yukleniyorObjesi.firinlariGetir = false;

                            if (!response.data.durum) {
                                return this.uyariAc({
                                    baslik: 'Hata',
                                    mesaj: response.data.mesaj,
                                    tur: "error"
                                });
                            }

                            this.firinlar = response.data.firinlar;
                        })
                        .catch(error => {
                            this.yukleniyorObjesi.firinlariGetir = false;
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

                    this.yeniFirin = {
                        ad: "",
                        kod: "",
                        renk: null,
                    };

                    this.aktifSayfa = _.cloneDeep(this.aktifSayfa);
                },
                firinDuzenle(firin) {
                    this.yeniFirin = _.cloneDeep(firin);

                    this.yeniFirin.renk = _.find(this.renkler, ["kod", firin.json.renk]);
                    this.firinEkleAc();
                },
                firinSil(firin) {
                    const islem = (cikisDurum) => {
                        this.yukleniyorObjesi.firinSil = true;

                        axios.post("{{ route('firinSil') }}", {
                                id: firin.id,
                            })
                            .then(response => {
                                this.yukleniyorObjesi.firinSil = false;

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

                                this.firinlariGetir();
                                this.geriAnasayfa();
                            })
                            .catch(error => {
                                this.yukleniyorObjesi.firinSil = false;
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
                        text: `"${ firin.ad }" adlı fırını silmek istediğinize emin misiniz?`,
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
                firinEkleAc() {
                    this.aktifSayfa = _.find(this.sayfalar, {
                        kod: "YENI_FIRIN"
                    });
                },
                firinKaydet() {
                    if (!this.yeniFirin.ad) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: 'Fırın adı boş olamaz!',
                            tur: "error"
                        });
                    }

                    if (!this.yeniFirin.kod) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: 'Fırın kısa adı boş olamaz!',
                            tur: "error"
                        });
                    }

                    if (!this.yeniFirin.renk) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: 'Fırın rengi boş olamaz!',
                            tur: "error"
                        });
                    }

                    this.yukleniyorObjesi.firinKaydet = true;
                    
                    this.yeniFirin.json={
                        renk:this.yeniFirin.renk.kod
                    };

                    axios.post('/firinKaydet', {
                            firin: this.yeniFirin,
                        })
                        .then(response => {
                            this.yukleniyorObjesi.firinKaydet = false;

                            if (!response.data.durum) {
                                return this.uyariAc({
                                    baslik: 'Hata',
                                    mesaj: response.data.mesaj,
                                    tur: "error"
                                });
                            }

                            const firin = response.data.firin;

                            this.uyariAc({
                                toast: {
                                    status: true,
                                    message: response.data.mesaj,
                                },
                            });

                            this.firinlariGetir();
                            this.geriAnasayfa();
                        })
                        .catch(error => {
                            this.yukleniyorObjesi.firinKaydet = false;
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
