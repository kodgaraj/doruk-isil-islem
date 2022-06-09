@extends('layout') 
@section('content')
<div class="row doruk-content">
    <h4 style="color:#999"><i class="fa fa-home"></i> ANASAYFA</h4>
    <div class="col-12 col-sm-4">
        <div class="card" @click="siparisSayfasiAc()">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="avatar-sm font-size-20 me-3">
                        <span class="avatar-title bg-soft-primary text-primary rounded">
                            <i class="mdi mdi-tag-plus-outline"></i>
                        </span>
                    </div>
                    <div class="flex-1">
                        <div class="font-size-16 mt-2">Siparişler</div>
                    </div>
                </div>
                <h4 class="mt-4">@{{ toplamKayitlar.siparisler }}</h4>
                {{-- <div class="row">
                    <div class="col-7">
                        <p class="mb-0"><span class="text-success me-2"> 0.28% <i
                                    class="mdi mdi-arrow-up"></i> </span></p>
                    </div>
                    <div class="col-5 align-self-center">
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 62%"
                                aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card" @click="kullanicilarSayfasiAc()">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="avatar-sm font-size-20 me-3">
                        <span class="avatar-title bg-soft-primary text-primary rounded">
                            <i class="mdi mdi-account-multiple-outline"></i>
                        </span>
                    </div>
                    <div class="flex-1">
                        <div class="font-size-16 mt-2">Kullanıcılar</div>

                    </div>
                </div>
                <h4 class="mt-4">@{{ toplamKayitlar.kullanicilar }}</h4>
                {{-- <div class="row">
                    <div class="col-7">
                        <p class="mb-0"><span class="text-success me-2"> 0.16% <i
                                    class="mdi mdi-arrow-up"></i> </span></p>
                    </div>
                    <div class="col-5 align-self-center">
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 62%"
                                aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card" @click="isilIslemSayfasiAc()">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="avatar-sm font-size-20 me-3">
                        <span class="avatar-title bg-soft-primary text-primary rounded">
                            <i class="mdi mdi-stove"></i>
                        </span>
                    </div>
                    <div class="flex-1">
                        <div class="font-size-16 mt-2">İşlemler</div>

                    </div>
                </div>
                <h4 class="mt-4">@{{ toplamKayitlar.islemler }}</h4>
                {{-- <div class="row">
                    <div class="col-7">
                        <p class="mb-0"><span class="text-success me-2"> 0.16% <i
                                    class="mdi mdi-arrow-up"></i> </span></p>
                    </div>
                    <div class="col-5 align-self-center">
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 62%"
                                aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h4 class="card-title mb-4">Isıl İşlemler</h4>
                    </div>
                    <div class="col-4 text-end ">
                        <button @click="isilIslemSayfasiAc()" class="btn btn-primary btn-sm">Tümünü Gör</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tech-companies-1" class="table table-striped table-hover">
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
                                <tr
                                    v-for="(islem, iIndex) in islemler.data"
                                    @click.stop="islemDetayiAc(islem)"
                                    style="cursor: pointer;"
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
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
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
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        let mixinApp = {
            data() {
                return {
                    islemler: {},
                    yukleniyorObjesi: {
                        islemler: false,
                    },
                    firinlar: @json($firinlar),
                    toplamKayitlar: @json($toplamKayitlar),
                };
            },
            mounted() {
                this.isilIslemleriGetir();
            },
            methods: {
                siparisSayfasiAc: function () {
                    window.location.href = "{{ route('siparis-formu') }}";
                },
                kullanicilarSayfasiAc: function () {
                    console.log('kullanicilarSayfasiAc');
                },
                isilIslemSayfasiAc: function () {
                    window.location.href = "{{ route('isil-islemler') }}";
                },
                isilIslemleriGetir(url = "{{ route('islemler') }}") {
                    this.yukleniyorObjesi.islemler = true;
                    axios.get(url)
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
                islemDetayiAc(islem) {
                    window.location.href = "{{ route('isil-islemler') }}?islemId=" + islem.id + "&formId=" + islem.formId;
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
            }
        };
    </script>
@endsection