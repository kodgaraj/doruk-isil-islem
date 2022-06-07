@extends('layout') 
@section('content')
<div class="row doruk-content">
    <h4 style="color:#999"><i class="fab fa-wpforms"> </i> SİPARİŞ FORMU</h4>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <template v-if="aktifSiparis === null">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">SİPARİŞLER</h4>
                        </div>
                        <div class="col-4 text-end">
                            <button @click="siparisEklemeAc" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> SİPARİŞ EKLE</button>
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
                                                        <th data-priority="3">İşlem Sayısı</th>
                                                        <th data-priority="1">İrsaliye No</th>
                                                        <th data-priority="4">Tutar</th>
                                                        <th data-priority="5">Sipariş Tarihi</th>
                                                        <th data-priority="6">İşlemler</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(siparis, index) in siparisler.data" :key="index">
                                                        <td>
                                                            <span class="badge badge-pill" :class="`bg-${ siparis.gecenSureRenk }`">@{{ siparis.gecenSure }} Gün</span>
                                                        </td>
                                                        <td>@{{ siparis.siparisNo }}</td>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    @{{ siparis.firmaAdi }}
                                                                </div>
                                                                <div class="col-12">
                                                                    <h6>@{{ siparis.sorumluKisi }}</h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>@{{ siparis.siparisAdi }}</td>
                                                        <td>@{{ siparis.islemSayisi }}</td>
                                                        <td>@{{ siparis.irsaliyeNo }}</td>
                                                        <td>@{{ siparis.tutar ? siparis.tutar + " ₺" : "-" }}</td>
                                                        <td>@{{ m(siparis.tarih).format("L") }}</td>
                                                        <td>
                                                            <button @click="siparisDuzenle(siparis)" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>
                                                            <button @click="siparisSil(siparis)" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="100%">
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
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
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
                            <button
                                @click="siparisKaydet"
                                class="btn btn-success"
                                :disabled="aktifSiparis.islemler.length === 0"
                            >
                                <i class="fas fa-save"></i> KAYDET
                            </button>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 col-sm-6 col-md-4 mb-2">
                            <div class="form-group">
                                <label for="tarih">Tarih</label>
                                <input
                                    v-model="aktifSiparis.tarih"
                                    type="date"
                                    class="form-control"
                                    placeholder="gg.aa.yyyy"
                                    data-date-container='#datepicker2'
                                    data-provide="datepicker"
                                    data-date-autoclose="true" id="tarih"
                                />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 mb-2">
                            <label class="form-label">Sipariş/Sıra No</label>
                            <input
                                v-model="aktifSiparis.siparisNo"
                                v-mask="'SPR#######'"
                                class="form-control"
                                placeholder="Sipariş numarası giriniz... (Örn: SPR0000001)"
                                type="text"
                            />
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 mb-2">
                            <label class="form-label">Sipariş Adı</label>
                            <input
                                v-model="aktifSiparis.siparisAdi"
                                class="form-control"
                                placeholder="Sipariş adı giriniz..."
                                type="text"
                            />
                            <small class="text-muted">Siparişe özel bir isim girebilirsiniz</small>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 mb-2">
                            <label class="form-label">İrsaliye No</label>
                            <input
                                v-model="aktifSiparis.irsaliyeNo"
                                v-mask="'IR#######'"
                                class="form-control"
                                placeholder="İrsaliye numarası giriniz... (IR0000001)"
                                type="text"
                            />
                        </div>
                        <div class="col-6 col-sm-2 mb-2">
                            <label class="form-label">Tutar</label>
                            <input
                                v-model="aktifSiparis.tutar"
                                class="form-control"
                                placeholder="Toplam tutarını giriniz..."
                                type="text"
                            />
                        </div>
                        <div class="col-6 col-sm-2 mb-2">
                            <label class="form-label">Termin</label>
                            <input
                                v-model="aktifSiparis.terminSuresi"
                                class="form-control"
                                placeholder="Termin süresi giriniz..."
                                type="number"
                            />
                        </div>
                        <div class="mb-3 col-12 col-sm-6 col-md-4">
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
                        </div>
                        <div class="mb-3 col-12 col-sm-6 col-md-4">
                            <label class="form-label">Firmalar</label>
                            <select class="form-control select2" v-model="aktifSiparis.firma">
                                <optgroup label="Firmalar">
                                    <option
                                        v-for="(firma, index) in firmalar"
                                        :value="firma"
                                        :key="index"
                                    >
                                        <div class="row">
                                            <div class="col-8">
                                                @{{ firma.firmaAdi }}
                                            </div>
                                            <div class="col-4">
                                                (@{{ firma.sorumluKisi }})
                                            </div>
                                        </div>
                                    </option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="form-group col-12 mb-2">
                            <label for="aciklama">Açıklama</label>
                            <textarea
                                v-model="aktifSiparis.aciklama"
                                class="form-control"
                                id="aciklama"
                                rows="3"
                            ></textarea>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <table class="table table-striped table-bordered nowrap" id="urun-detay">
                            <thead>
                                <th>Sıra No</th>
                                <th>Malzeme</th>
                                <th>Adet</th>
                                <th>Miktar (KG)</th>
                                <th>Dara (KG)</th>
                                <th>Birim Fiyat</th>
                                <th>Kalite</th>
                                <th>Yapılacak İşlem</th>
                                <th>İstenilen Sertlik</th>
                                <th>İşlem Durumu</th>
                                <th>İşlemler</th>
                            </thead>
                            <tbody id="islem-satir-ekle">
                                <tr v-for="(islem, index) in aktifSiparis.islemler" :key="index">
                                    <td># @{{ index + 1 }}</td>
                                    <td>
                                        <select class="form-select" aria-label="Malzemeler" v-model="islem.malzeme">
                                            <option
                                                v-for="(malzeme, index) in malzemeler"
                                                :value="malzeme"
                                                :key="index"
                                            >
                                                @{{ malzeme.ad }}
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="form-control" type="number" placeholder="Adet" v-model="islem.adet">
                                    </td>
                                    <td>
                                        <input class="form-control" type="number" placeholder="Miktar (KG)" v-model="islem.miktar">
                                    </td>
                                    <td>
                                        <input class="form-control" type="number" placeholder="Dara (KG)" v-model="islem.dara">
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" placeholder="Birim Fiyat" v-model="islem.birimFiyat">
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" placeholder="Kalite" v-model="islem.kalite">
                                    </td>
                                    <td>
                                        <select class="form-select" aria-label="İşlemler" v-model="islem.yapilacakIslem">
                                            <option
                                                v-for="(islem, index) in islemTurleri"
                                                :value="islem"
                                                :key="index"
                                            >
                                                @{{ islem.ad }}
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" placeholder="İstenilen Sertlik" v-model="islem.istenilenSertlik">
                                    </td>
                                    <td>
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
                                    <td>
                                        <button class="btn btn-danger" @click="islemSil(index)">Sil</button>
                                    </td>
                                </tr>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="8">
                                        <button class="btn btn-info btn-sm" @click="islemEkle">
                                            <i class="fa fa-plus"></i>
                                            Ekle
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
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
            }
        },
        mounted() {
            this.siparisleriGetir();
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
                axios.get(url)
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

                    _.forEach(this.siparisler.data, (siparis) => {
                        const terminBirinciFaz = _.floor(siparis.terminSuresi * 30 / 100);
                        const terminIkinciFaz = _.floor(siparis.terminSuresi * 60 / 100);

                        const siparisTarihi = this.m(siparis.tarih);
                        const simdiTarihi = this.m();
                        siparis.gecenSure = simdiTarihi.diff(siparisTarihi, 'days');
                        siparis.gecenSureRenk = siparis.gecenSure > terminIkinciFaz
                            ? "danger"
                            : siparis.gecenSure > terminBirinciFaz
                                ? "warning"
                                : "success";
                    });

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
                };

                this.numaralariGetir();
                this.siparisDurumlariGetir();
                this.firmalariGetir();
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

                    this.aktifSiparis = JSON.parse(JSON.stringify(this.aktifSiparis));
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
                    console.log(error);
                });
            },
            islemEkle() {
                if (!this.malzemeler.length && !this.yukleniyorObjesi.malzemeler) {
                    this.malzemeleriGetir();
                }

                if (!this.islemTurleri.length && !this.yukleniyorObjesi.islemTurleri) {
                    this.islemTurleriGetir();
                }

                if (!this.islemDurumlari.length && !this.yukleniyorObjesi.islemDurumlari) {
                    this.islemDurumlariGetir(true);
                }

                this.aktifSiparis.islemler.push({
                    malzeme: null,
                    adet: 1,
                    miktar: 1,
                    dara: 0,
                    birimFiyat: 0,
                    kalite: "",
                    yapilacakIslem: null,
                    istenilenSertlik: "",
                    islemDurumu: null,
                });
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

                axios.post("/siparisDetay", {
                    siparisId: siparis.siparisId
                })
                .then(response => {
                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    Promise.all(promises)
                    .then(p => {
                        const aktifSiparis = {
                            ...siparis,
                            islemler: response.data.veriler.islemler,
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
                    });
                })
                .catch(error => {
                    console.log(error);
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
                            console.log(error);
                        });
                    } else if (result.isDenied) {}
                });
            },
        }
    };
</script>
@endsection
