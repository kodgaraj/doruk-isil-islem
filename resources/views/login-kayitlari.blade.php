@extends('layout')
@section('content')
<div class="row doruk-content">
    <h4 style="color:#999"><i class="fa fa-clipboard-list"></i> LOGİN KAYITLARI</h4>
    <div class="col-12">
        <div class="card" key="ANASAYFA">
            <div class="card-header">
                <div class="row d-flex align-items-center">
                    <div class="col">
                        <h4>
                            KAYITLAR
                        </h4>
                    </div>
                    <div class="col-auto">
                        <div class="row d-flex align-items-center">
                            <div class="col">
                                <div class="input-group">
                                    <input
                                        v-model="filtrelemeObjesi.arama"
                                        type="text"
                                        class="form-control"
                                        placeholder="Arama"
                                        aria-label="Arama"
                                        aria-describedby="arama"
                                        @keyup.enter="loginKayitlariGetir()"
                                        @input="gecikmeliFonksiyon.varsayilan()"
                                    />
                                    <span @click="loginKayitlariGetir()" class="input-group-text waves-effect" id="arama">
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
                                    Kullanıcı adı/id, işlem türü,ip adresi, işlem mesajı...
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
                                                    <label for="kullaniciFiltre">Kullanıcılar</label>
                                                    <v-select
                                                        v-model="filtrelemeObjesi.kullanicilar"
                                                        :options="kullanicilar"
                                                        label="name"
                                                        multiple
                                                        id="kullaniciFiltre"
                                                    ></v-select>
                                                </div>
                                            </div>
                                            <div class="col-12 m-0">
                                                <div class="form-group">
                                                    <label for="islemDurumuFiltre">İşlemler</label>
                                                    <v-select
                                                        v-model="filtrelemeObjesi.islemler"
                                                        :options="islemler"
                                                        label="ad"
                                                        multiple
                                                        id="islemDurumuFiltre"
                                                    ></v-select>
                                                </div>
                                            </div>
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
                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="loginKayitlariGetir()">ARA</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <template v-if="yukleniyorObjesi.loginKayitlari">
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
                            <table class="table table-striped table-hover table-centered">
                                <thead>
                                    <tr>
                                        <th>İşlem Türü</th>
                                        <th>İşlemi Yapan</th>
                                        <th>İşlem</th>
                                        <th>İp Adresi</th>
                                        <th>Tarih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="_.size(loginKayitlari.data)">
                                        <tr v-for="(log, index) in loginKayitlari.data" :key="index">
                                            <td>
                                                <div class="col-12">
                                                    <span class="badge" :class="`bg-${log.olay.renk}`">
                                                        @{{ log.olay.ad }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="kisa-uzunluk">
                                                <div class="col-12">
                                                    @{{ log.kullaniciAdi }}
                                                </div>
                                                {{-- <div class="col-12">
                                                    <span class="badge bg-secondary">
                                                        Kullanıcı ID: @{{ log.user_id }}
                                                    </span>
                                                </div> --}}
                                            </td>
                                            <td class="en-uzun-uzunluk">
                                                <div class="col-12">
                                                    @{{ log.aciklama }}
                                                </div>
                                                {{-- <div class="col-12">
                                                    <span class="badge bg-secondary">
                                                        İp Adresi: @{{ log.ip }}
                                                    </span>
                                                </div> --}}
                                            </td>
                                            <td class="kisa-uzunluk">
                                                <div class="col-12">
                                                    <span class="badge bg-warning" >@{{ log.ip }}</span>
                                                </div>
                                            </td>
                                            <td class="kisa-uzunluk">
                                                <div class="col-12">
                                                    @{{ m(log.created_at).format("L LTS") }}
                                                </div>
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
                                        <button class="page-link" :disabled="!loginKayitlari.prev_page_url" @click="loginKayitlariGetir(loginKayitlari.prev_page_url)">
                                            <i class="fas fa-angle-left"></i>
                                        </button>
                                    </li>
                                    <li
                                        v-for="sayfa in sayfalamaAyarla(loginKayitlari.last_page, loginKayitlari.current_page)"
                                        class="page-item"
                                        :class="[sayfa.aktif ? 'active' : '']"
                                    >
                                        <button class="page-link" @click="sayfa.tur === 'SAYFA' ? loginKayitlariGetir(`{{ route("loginKayitlariGetir") }}?page=` + sayfa.sayfa) : ()  => {}">@{{ sayfa.sayfa }}</button>
                                    </li>
                                    <li class="page-item">
                                        <button class="page-link" :disabled="!loginKayitlari.next_page_url" @click="loginKayitlariGetir(loginKayitlari.next_page_url)">
                                            <i class="fas fa-angle-right"></i>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-auto">
                                <small class="text-muted">Toplam Kayıt: @{{ loginKayitlari.total }}</small>
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
        let mixinApp = {
            data() {
                return {
                    yukleniyorObjesi: {
                        loginKayitlari: false,
                    },
                    loginKayitlari: {},
                    filtrelemeObjesi: {
                        arama: "",
                        baslangicTarihi: "",
                        bitisTarihi: "",
                        kullanicilar: [],
                        islemler: [],
                    },
                    kullanicilar: @json($kullanicilar),
                    islemler: @json($islemler),
                };
            },
            mounted() {
                this.gecikmeliFonksiyonCalistir(this.loginKayitlariGetir);

                this.loginKayitlariGetir();
            },
            methods: {
                loginKayitlariGetir(url = "{{ route('loginKayitlariGetir') }}") {
                    this.yukleniyorObjesi.loginKayitlari = true;
                    axios.get(url, {
                        params: {
                            filtreleme: this.filtrelemeObjesi,
                        },
                    })
                    .then(response => {
                        this.yukleniyorObjesi.loginKayitlari = false;

                        if (!response.data.durum) {
                            return this.uyariAc({
                                baslik: 'Hata',
                                mesaj: response.data.mesaj,
                                tur: "error"
                            });
                        }
                        this.loginKayitlari = response.data.loginKayitlari;
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.loginKayitlari = false;
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                        console.log(error);
                    });
                },
                tarihleriTemizle() {
                    this.filtrelemeObjesi.baslangicTarihi = "";
                    this.filtrelemeObjesi.bitisTarihi = "";
                },
            }
        };
    </script>
@endsection
