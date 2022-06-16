@extends('layout') 
@section('content')
<div class="row doruk-content">
    <h4 style="color:#999"><i class="fa fa-home"></i> ANASAYFA</h4>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row d-flex align-items-center">
                    <div class="col">
                        <h4>
                            <button class="btn btn-warning" v-if="aktifSayfa.geriFonksiyon" @click="aktifSayfa.geriFonksiyon()">
                                <i class="fa fa-arrow-left"></i> GERİ
                            </button>
                            @{{ aktifSayfa.baslik }}
                        </h4>
                    </div>
                    <div class="col-auto">
                        <!-- Filtreleme butonu -->
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#filtrelemeModal">
                            <i class="fa fa-filter"></i>
                        </button>

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
                                                    <label for="firinFiltre">Fırın</label>
                                                    <v-select
                                                        v-model="filtrelemeObjesi.firin"
                                                        :options="firinlar"
                                                        label="ad"
                                                        multiple
                                                        id="firinFiltre"
                                                    ></v-select>
                                                </div>
                                            </div>
                                            <div class="col-12 m-0">
                                                <div class="form-group">
                                                    <label for="islemDurumuFiltre">İşlem Durumları</label>
                                                    <v-select
                                                        v-model="filtrelemeObjesi.islemDurumu"
                                                        :options="islemDurumlari"
                                                        label="ad"
                                                        multiple
                                                        id="islemDurumuFiltre"
                                                    ></v-select>
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
                        <!-- KAYDET BUTONU -->
                        {{-- <button v-if="aktifSayfa.kod === 'YENI_KULLANICI'" class="btn btn-primary" @click="kullaniciKaydet()">
                            <i class="fa fa-save"></i> KAYDET
                        </button>
                        <!-- ROL KAYDET BUTONU -->
                        <button v-if="aktifSayfa.kod === 'YENI_ROL'" class="btn btn-primary" @click="rolKaydet()">
                            <i class="fa fa-save"></i> ROL KAYDET
                        </button> --}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <template v-if="aktifSayfa.kod === 'ANASAYFA'">
                    <div class="table-responsive">
                        <table id="tech-companies-1" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>İşlem ID</th>
                                    <th>Malzeme</th>
                                    <th>İşlem</th>
                                    <th>Fırın/Şarj</th>
                                    <th class="text-center">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="yukleniyorObjesi.islemler">
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="col-12 text-center">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="sr-only">Yükleniyor...</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template v-else-if="!_.size(islemler.data)">
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="col-12 text-center">
                                                <h5>İşlem Bulunamadı</h5>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <template v-for="(islem, iIndex) in islemler.data">
                                        <tr
                                            @click.stop=""
                                            style="cursor: pointer;"
                                            :style="{
                                                backgroundColor: islem.tekrarEdenId ? '#F8747450' : '#fff',
                                                border: islem.tekrarEdenId ? '1px solid #F87474' : '',
                                                borderRadius: islem.tekrarEdenId ? '4px' : '',
                                            }"
                                            :key="iIndex"
                                        >
                                            <td>
                                                <div class="row">
                                                    <div class="col-12 d-inline-flex">
                                                        <span># @{{ islem.id }}</span>
                                                        <div v-if="islem.tekrarEdilenId" class="ms-1">
                                                            <span class="badge rounded-pill bg-danger">Tekrar Edilen İşlem ID: @{{ islem.tekrarEdilenId }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <span class="badge badge-pill bg-primary">Sipariş No: @{{ islem.siparisNo }}</span>
                                                    </div>
                                                    <div class="col-12">
                                                        <span class="badge badge-pill" :class="`bg-${ islem.gecenSureRenk }`">Termin: @{{ islem.gecenSure }} Gün</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-12">
                                                        @{{ islem.malzemeAdi }}
                                                    </div>
                                                    <div class="col-12">
                                                        <small class="text-muted">Adet: @{{ islem.adet }} adet</small>
                                                    </div>
                                                    <div class="col-12">
                                                        <small class="text-muted">Miktar: @{{ islem.miktar }} kg</small>
                                                    </div>
                                                    <div class="col-12">
                                                        <small class="text-muted">Dara: @{{ islem.dara }} kg</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <small class="text-muted">Türü: @{{ islem.islemTuruAdi ? islem.islemTuruAdi : "-" }}</small>
                                                    </div>
                                                    <div class="col-12">
                                                        <small class="text-muted">İ. Sertlik: @{{ islem.istenilenSertlik ? islem.istenilenSertlik : "-" }}</small>
                                                    </div>
                                                    <div class="col-12">
                                                        <small class="text-muted">Kalite: @{{ islem.kalite ? islem.kalite : "-" }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <span class="badge badge-pill" :class="`bg-${ islem.firinRenk }`">@{{ islem.firinAdi }}</span>
                                                    </div>
                                                    <div class="col-12">
                                                        <span class="badge badge-pill bg-secondary">@{{ islem.sarj }}. ŞARJ</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="uzun-uzunluk text-center align-center">
                                                <div class="btn-group row">
                                                    <div class="col-12">
                                                        <b :class="islem.islemDurumuRenk">
                                                            @{{ islem.islemDurumuAdi }}
                                                            <i
                                                                class="ml-2"
                                                                :class="islem.islemDurumuIkon"
                                                            ></i>
                                                        </b>
                                                    </div>
                                                    <hr class="m-2" />
                                                    <div class="col-12">
                                                        <button
                                                            class="btn btn-primary btn-sm"
                                                            @click.stop="islemBaslat(islem)"
                                                            v-if="islem.islemDurumuKodu === 'ISLEM_BEKLIYOR'"
                                                        >
                                                            <i class="mdi mdi-play"></i>
                                                        </button>
                                                        <button
                                                            v-else-if="islem.islemDurumuKodu === 'ISLEMDE'"
                                                            class="btn btn-success btn-sm"
                                                            @click.stop="islemTamamla(islem)"
                                                        >
                                                            <i class="mdi mdi-check"></i>
                                                        </button>
                                                        <template v-if="islem.islemDurumuKodu === 'TAMAMLANDI'">
                                                            <button
                                                                v-if="islem.bildirim !== 1"
                                                                class="btn btn-info btn-sm"
                                                                @click.stop="islemBildirimAt(islem)"
                                                            >
                                                                <i class="mdi mdi-bell"></i>
                                                            </button>
                                                            <button
                                                                class="btn btn-danger btn-sm"
                                                                @click.stop="islemTamamlandiGeriAl(islem)"
                                                            >
                                                                <i class="mdi mdi-close"></i>
                                                            </button>
                                                            <div v-if="islem.tekrarEdenId" class="col-12">
                                                                <span class="badge rounded-pill bg-danger">Tekrar Eden İşlem ID: @{{ islem.tekrarEdenId }}</span>
                                                            </div>
                                                        </template>
                                                        <button
                                                            v-if="islem.islemDurumuKodu === 'ISLEMDE'"
                                                            class="btn btn-warning btn-sm"
                                                            @click.stop="islemTekrar(islem)"
                                                        >
                                                            <i class="mdi mdi-replay"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <template v-if="_.size(islem.tekrarEdenIslemler)">
                                            <tr :key="'tekrarEdenler' + iIndex" style="background-color: #F8747450; border: 1px solid #F87474;">
                                                <td colspan="100%" class="p-0">
                                                    <div class="d-grid">
                                                        <button class="btn btn-sm btn-danger btn-block rounded-0 m-0 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                                            Tekrar Eden İşlemler <i class="mdi mdi-chevron-down"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr
                                                v-for="(tekrarEdenIslem, tiIndex) in islem.tekrarEdenIslemler"
                                                class="collapse"
                                                id="collapseExample"
                                                style="background-color: #F8747425; border-right: 1px solid #F87474; border-left: 1px solid #F87474;"
                                                :style="tiIndex === (_.size(islem.tekrarEdenIslemler) - 1) ? 'border-bottom: 1px solid #F87474;' : ''"
                                                :key="tiIndex + '_' + islem.id"
                                            >
                                                <td>
                                                    <div class="row">
                                                        <div class="col-12 d-inline-flex">
                                                            <span># @{{ tekrarEdenIslem.id }}</span>
                                                            <div v-if="tekrarEdenIslem.tekrarEdilenId" class="ms-1">
                                                                <span class="badge rounded-pill bg-danger">Tekrar Edilen İşlem ID: @{{ tekrarEdenIslem.tekrarEdilenId }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <span class="badge badge-pill bg-primary">Sipariş No: @{{ tekrarEdenIslem.siparisNo }}</span>
                                                        </div>
                                                        <div class="col-12">
                                                            <span class="badge badge-pill" :class="`bg-${ tekrarEdenIslem.gecenSureRenk }`">Termin: @{{ tekrarEdenIslem.gecenSure }} Gün</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            @{{ tekrarEdenIslem.malzemeAdi }}
                                                        </div>
                                                        <div class="col-12">
                                                            <small class="text-muted">Adet: @{{ tekrarEdenIslem.adet }} adet</small>
                                                        </div>
                                                        <div class="col-12">
                                                            <small class="text-muted">Miktar: @{{ tekrarEdenIslem.miktar }} kg</small>
                                                        </div>
                                                        <div class="col-12">
                                                            <small class="text-muted">Dara: @{{ tekrarEdenIslem.dara }} kg</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <small class="text-muted">Türü: @{{ tekrarEdenIslem.islemTuruAdi ? tekrarEdenIslem.islemTuruAdi : "-" }}</small>
                                                        </div>
                                                        <div class="col-12">
                                                            <small class="text-muted">İ. Sertlik: @{{ tekrarEdenIslem.istenilenSertlik ? tekrarEdenIslem.istenilenSertlik : "-" }}</small>
                                                        </div>
                                                        <div class="col-12">
                                                            <small class="text-muted">Kalite: @{{ tekrarEdenIslem.kalite ? tekrarEdenIslem.kalite : "-" }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <span class="badge badge-pill" :class="`bg-${ tekrarEdenIslem.firinRenk }`">@{{ tekrarEdenIslem.firinAdi }}</span>
                                                        </div>
                                                        <div class="col-12">
                                                            <span class="badge badge-pill bg-secondary">@{{ tekrarEdenIslem.sarj }}. ŞARJ</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="uzun-uzunluk text-center align-center">
                                                    <div class="btn-group row">
                                                        <div class="col-12">
                                                            <b :class="tekrarEdenIslem.islemDurumuRenk">
                                                                @{{ tekrarEdenIslem.islemDurumuAdi }}
                                                                <i
                                                                    class="ml-2"
                                                                    :class="tekrarEdenIslem.islemDurumuIkon"
                                                                ></i>
                                                            </b>
                                                        </div>
                                                        <hr class="m-2" />
                                                        <div class="col-12">
                                                            <button
                                                                class="btn btn-primary btn-sm"
                                                                @click.stop="islemBaslat(tekrarEdenIslem)"
                                                                v-if="tekrarEdenIslem.islemDurumuKodu === 'ISLEM_BEKLIYOR'"
                                                            >
                                                                <i class="mdi mdi-play"></i>
                                                            </button>
                                                            <button
                                                                v-else-if="tekrarEdenIslem.islemDurumuKodu === 'ISLEMDE'"
                                                                class="btn btn-success btn-sm"
                                                                @click.stop="islemTamamla(tekrarEdenIslem)"
                                                            >
                                                                <i class="mdi mdi-check"></i>
                                                            </button>
                                                            <template v-if="tekrarEdenIslem.islemDurumuKodu === 'TAMAMLANDI'">
                                                                <button
                                                                    v-if="tekrarEdenIslem.bildirim !== 1"
                                                                    class="btn btn-info btn-sm"
                                                                    @click.stop="islemBildirimAt(tekrarEdenIslem)"
                                                                >
                                                                    <i class="mdi mdi-bell"></i>
                                                                </button>
                                                                <button
                                                                    class="btn btn-danger btn-sm"
                                                                    @click.stop="islemTamamlandiGeriAl(tekrarEdenIslem)"
                                                                >
                                                                    <i class="mdi mdi-close"></i>
                                                                </button>
                                                                <div v-if="tekrarEdenIslem.tekrarEdenId" class="col-12">
                                                                    <span class="badge rounded-pill bg-danger">Tekrar Eden İşlem ID: @{{ tekrarEdenIslem.tekrarEdenId }}</span>
                                                                </div>
                                                            </template>
                                                            <button
                                                                v-if="tekrarEdenIslem.islemDurumuKodu === 'ISLEMDE'"
                                                                class="btn btn-warning btn-sm"
                                                                @click.stop="islemTekrar(tekrarEdenIslem)"
                                                            >
                                                                <i class="mdi mdi-replay"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </template>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>
            <div class="card-footer">
                <div class="row d-flex align-items-center justify-content-between">
                    <div class="col-auto"></div>
                    <div class="col">
                        <ul class="pagination pagination-rounded justify-content-center mb-0">
                            <li class="page-item">
                                <button class="page-link" :disabled="!islemler.prev_page_url" @click="isilIslemleriGetir(islemler.prev_page_url)">Önceki</button>
                            </li>
                            <li
                                v-for="sayfa in islemler.last_page"
                                class="page-item"
                                :class="[islemler.current_page === sayfa ? 'active' : '']"
                            >
                                <button class="page-link" @click='isilIslemleriGetir("{{ route("islemler") }}?page=" + sayfa)'>@{{ sayfa }}</button>
                            </li>
                            <li class="page-item">
                                <button class="page-link" :disabled="!islemler.next_page_url" @click="isilIslemleriGetir(islemler.next_page_url)">Sonraki</button>
                            </li>
                        </ul>
                    </div>
                    <div class="col-auto">
                        <small class="text-muted">Toplam Kayıt: @{{ islemler.total }}</small>
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
                        baslik: "Isıl İşlemler",
                    },
                    sayfalar: [
                        {
                            kod: "ANASAYFA",
                            baslik: "Isıl İşlemler",
                        },
                        {
                            kod: "ISLEM_DETAY",
                            baslik: "İşlem Detay",
                            geriFonksiyon: () => this.geriAnasayfa(),
                        },
                    ],
                    islemler: {},
                    yukleniyorObjesi: {
                        islemler: false,
                    },
                    filtrelemeObjesi: {
                        termin: 0,
                        firin: null,
                        islemDurumu: null,
                        limit: 10,
                    },
                    firinlar: @json($firinlar),
                    islemDurumlari: @json($islemDurumlari),
                };
            },
            mounted() {
                this.isilIslemleriGetir();
            },
            methods: {
                isilIslemleriGetir(url = "{{ route('islemler') }}") {
                    this.yukleniyorObjesi.islemler = true;
                    axios.get(url, {
                        params: {
                            filtreleme: this.filtrelemeObjesi,
                        },
                    })
                    .then(response => {
                        this.yukleniyorObjesi.islemler = false;
                        if (!response.data.durum) {
                            return this.uyariAc({
                                baslik: 'Hata',
                                mesaj: response.data.mesaj,
                                tur: "error"
                            });
                        }

                        this.islemler = response.data.islemler;
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.islemler = false;
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                        console.log(error);
                    });
                },
                islemBaslat(islem) {
                    this.yukleniyorObjesi.islemler = true;
                    axios.post("{{ route('islemDurumuDegistir') }}", {
                        islem: islem,
                        islemDurumuKodu: "ISLEMDE"
                    })
                    .then(response => {
                        this.yukleniyorObjesi.islemler = false;
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
                            tur: "success"
                        });

                        this.isilIslemleriGetir();
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.islemler = false;
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                        console.log(error);
                    });
                },
                islemTamamla(islem) {
                    this.yukleniyorObjesi.islemler = true;
                    axios.post("{{ route('islemDurumuDegistir') }}", {
                        islem: islem,
                        islemDurumuKodu: "TAMAMLANDI"
                    })
                    .then(response => {
                        this.yukleniyorObjesi.islemler = false;
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
                            tur: "success"
                        });

                        this.isilIslemleriGetir();
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.islemler = false;
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                        console.log(error);
                    });
                },
                islemTekrar(islem) {
                    const fonksiyon = (aciklama) => {
                        this.yukleniyorObjesi.islemler = true;
                        islem.aciklama = aciklama;
                        console.log("islem açıklaması", islem.aciklama);
                        axios.post("{{ route('islemTekrarEt') }}", {
                            islem: islem,
                        })
                        .then(response => {
                            this.yukleniyorObjesi.islemler = false;
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
                                tur: "success"
                            });

                            this.isilIslemleriGetir();
                        })
                        .catch(error => {
                            this.yukleniyorObjesi.islemler = false;
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                                tur: "error"
                            });
                            console.log(error);
                        });
                    };

                    Swal.fire({
                        title: 'İşlem Tekrar Edilsin mi?',
                        text: "İşlem tekrardan başlatılacaktır. İşlemi tekrar etmek istediğinize emin misiniz?",
                        icon: 'warning',
                        input: 'textarea',
                        showCancelButton: true,
                        cancelButtonText: 'İptal',
                        confirmButtonText: 'Tekrar Et',
                        inputPlaceholder: 'Tekrar açıklaması...',
                        inputAttributes: {
                            'aria-label': 'Tekrar açıklaması'
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fonksiyon(result.value);
                        }
                    });
                },
                islemTamamlandiGeriAl(islem) {
                    this.yukleniyorObjesi.islemler = true;
                    axios.post("{{ route('islemTamamlandiGeriAl') }}", {
                        islem: islem,
                    })
                    .then(response => {
                        this.yukleniyorObjesi.islemler = false;
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
                            tur: "success"
                        });

                        this.isilIslemleriGetir();
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.islemler = false;
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                        console.log(error);
                    });
                },
                filtrele() {
                    this.isilIslemleriGetir();
                },
            }
        };
    </script>
@endsection