@extends('layout') 
@section('content')
<div class="row doruk-content">
    <h4 style="color:#999"><i class="fa fa-bell"></i> BİLDİRİMLER</h4>
    <div class="col-12">
        <div class="card" key="ANASAYFA">
            <div class="card-header">
                <div class="row d-flex align-items-center">
                    <div class="col">
                        <h4>
                            TÜM BİLDİRİMLER
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
                                        @keyup.enter="bildirimleriGetir(undefined, true)"
                                    />
                                    <span @click="bildirimleriGetir(undefined, true)" class="input-group-text waves-effect" id="arama">
                                        <i class="mdi mdi-magnify"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <small class="text-muted">
                                    Bildirim türü, başlığı ve içeriği...
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-rep-plugin">
                    <div class="table-responsive" data-pattern="priority-columns">
                        <table class="table mb-0 table-striped table-hover table-centered">
                            <thead>
                                <tr>
                                    <th>Bildirim Türü</th>
                                    <th>Bildirim</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="bildirimler.veriler && _.size(bildirimler.veriler.data)">
                                    <tr v-for="(bildirim, index) in bildirimler.veriler.data" :key="index">
                                        <td>
                                            <div class="col-12">
                                                <span class="badge" :class="`bg-${bildirim.bildirimTuruJson.renk}`">
                                                    @{{ bildirim.bildirimTuruAdi }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="en-uzun-uzunluk">
                                            <div class="col-12">
                                                <b>@{{ bildirim.baslik }}</b>
                                            </div>
                                            <div class="col-12">
                                                <span v-html="bildirim.icerik"></span>
                                            </div>
                                        </td>
                                        <td class="kisa-uzunluk">
                                            <div class="col-12">
                                                @{{ m(bildirim.created_at).format("L LTS") }}
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="100%" class="text-center py-4">
                                            <small v-if="yukleniyorObjesi.bildirimler">Yükleniyor</small>
                                            <h6 v-else>Kayıt bulunamadı</h6>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer p-1">
                    <div class="d-grid text-center">
                        <button
                            v-if="bildirimler.veriler && bildirimler.veriler.next_page_url"
                            class="btn btn-sm"
                            :class="yukleniyorObjesi.bildirimler ? 'btn-primary' : 'btn-outline-primary'"
                            :disabled="yukleniyorObjesi.bildirimler"
                            @click="bildirimleriGetir(bildirimler.veriler.next_page_url)"
                        >
                            <template v-if="yukleniyorObjesi.bildirimler">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Yükleniyor...
                            </template>
                            <template v-else>
                                <i class="mdi mdi-refresh"></i>
                                Daha fazla göster
                            </template>
                        </button>
                        <small class="text-muted">
                            Tüm bildirimler gösteriliyor.
                        </small>
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
                    filtrelemeObjesi: {
                        arama: "",
                    },
                    yukleniyorObjesi: {
                        bildirimler: false,
                    },
                    bildirimler: {},
                };
            },
            mounted() {
                this.bildirimleriGetir();
            },
            methods: {
                bildirimleriGetir(url = "{{ route('bildirimleriGetir') }}", arama = false) {
                    this.yukleniyorObjesi.bildirimler = true;
                    axios.get(url, {
                        params: {
                            filtreleme: this.filtrelemeObjesi,
                        },
                    })
                    .then(response => {
                        this.yukleniyorObjesi.bildirimler = false;

                        if (!response.data.durum) {
                            return this.uyariAc({
                                baslik: 'Hata',
                                mesaj: response.data.mesaj,
                                tur: "error"
                            });
                        }

                        if (arama || !this.bildirimler.veriler || !this.bildirimler.veriler.data) {
                            this.bildirimler = response.data.bildirimler;
                        }
                        else {
                            const data = this.bildirimler.veriler.data;
                            this.bildirimler = {
                                ...this.bildirimler,
                                ...response.data.bildirimler
                            };

                            this.bildirimler.veriler.data = [
                                ...data,
                                ...response.data.bildirimler.veriler.data
                            ];
                        }

                        this.miniBildirimlerObjesi.okunmamisBildirimSayisi = this.bildirimler.okunmamisBildirimSayisi;
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.bildirimler = false;
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