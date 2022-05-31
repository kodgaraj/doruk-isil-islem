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
                                                        <td>@{{ siparis.tarih }}</td>
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
                            <h4 class="card-title">SİPARİŞ EKLEME</h4>
                        </div>
                        <div class="col-4 text-end">
                            <button @click="geri" class="btn btn-primary btn-sm"><i class="fas fa-arrow-left"></i> GERİ</button>
                        </div>
                    </div>

                    <div class="row">
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
                                                @{{ firma.sorumluKisi }}
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
                                <th>No</th>
                                <th>Malzeme</th>
                                <th>Miktar KG</th>
                                <th>Adet</th>
                                <th>Kalite</th>
                                <th>Yapılacak İşlem</th>
                                <th>İstenilen Sertlik</th>
                                <th>İşlemler</th>
                            </thead>
                            <tbody id="urun-satir-ekle">
                                <tr v-for="(urun, index) in aktifSiparis.urunler" :key="index">
                                    <td>@{{ index + 1 }}</td>
                                    <td>
                                        <select class="form-select" aria-label="Default select example">
                                            <option selected>Select</option>
                                            <option>Large select</option>
                                            <option>Small select</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" placeholder="Miktar KG" v-model="urun.miktar">
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" placeholder="Adet" v-model="urun.adet">
                                    </td>
                                    <td>
                                        <select class="form-select" aria-label="Default select example">
                                            <option selected>Select</option>
                                            <option>Large select</option>
                                            <option>Small select</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select" aria-label="Default select example">
                                            <option selected>Select</option>
                                            <option>Large select</option>
                                            <option>Small select</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select" aria-label="Default select example">
                                            <option selected>Select</option>
                                            <option>Large select</option>
                                            <option>Small select</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger" @click="urunSil(index)">Sil</button>
                                    </td>
                                </tr>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="8">
                                        <button class="btn btn-success" @click="urunEkle">Ekle</button>
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
                siparisler: [],
                aktifSiparis: null,
                yukleniyorObjesi: {
                    numaralar: false,
                    siparisDurumlari: false,
                    firmalar: false,
                },
                siparisDurumlari: [],
                firmalar: [],
            }
        },
        mounted() {
            this.siparisleriGetir();
            this.siparisEklemeAc();
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
                })
                .catch(error => {
                    this.yukleniyorDurum(false);
                    console.log(error);
                });
            },
            siparisEklemeAc() {
                this.aktifSiparis = {
                    tarih: moment().format("YYYY-MM-DD"),
                    siparisNo: "",
                    siparisAdi: "",
                    terminSuresi: 5,
                    urunler: [],
                };

                this.numaralariGetir();
                this.siparisDurumlariGetir();
                this.firmalariGetir();
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
                axios.get("/siparisDurumlariGetir")
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

                    const siparisAlindiDurum = _.find(this.siparisDurumlari, {
                        kod: "SIPARIS_ALINDI"
                    });

                    if (siparisAlindiDurum) {
                        this.aktifSiparis.siparisDurumu = siparisAlindiDurum;
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
                axios.get("/firmalariGetir")
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

            urunEkle() {
                this.urunler.push(this.urun);
                this.urun = {
                    malzeme: '',
                    miktar: '',
                    adet: '',
                    kalite: '',
                    yapilacak_islem: '',
                    istenilen_sertlik: ''
                }
            },
            urunSil(index) {
                this.urunler.splice(index, 1);
            },
            siparisEkle() {
                this.aktifSiparis = {
                    tarih: '',
                    sira_no: '',
                    musteri: '',
                    urunler: this.urunler
                }
            },
            geri() {
                this.aktifSiparis = null;
            }
        }
    };
</script>
@endsection
