@extends('layout')
@section('style')

@endsection
@section('content')
    <div class="row doruk-content">
        <h4 style="color:#999"><i class="fa fa-low-vision"></i> KISITLAMALAR</h4>
        <div class="col-12">
            <div class="card" key="ANASAYFA">
                <div class="card-header">
                    <div class="row d-flex align-items-center">
                        <div class="col">
                            <h4>
                                @{{ aktifSayfa.baslik }}
                            </h4>
                        </div>
                        <div class="col-12 col-md-4 text-end">
                            <button
                                    @click="kisitGuncelle()"
                                    class="btn btn-success"
                                >
                                    <i class="fas fa-save"></i> KAYDET
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <template v-if="aktifSayfa.kod === 'ANASAYFA'">
                        <div class="row">
                            <template v-if="yukleniyor">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Yükleniyor...</span>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <div class="col-12 col-sm-6 col-md-6 mb-2">
                                    <div class="form-group">
                                        <label for="saatBaslangic">Kısıtlama Başlangıç Saati</label>
                                        <input
                                            v-model="kisitlar.saatBaslangic"
                                            type="time"
                                            :max="kisitlar.saatBitis"
                                            class="form-control"
                                            data-time-container='#datepicker2'
                                            data-provide="datepicker"
                                            data-date-autoclose="true" id="saatBaslangic"
                                        />
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-6 mb-2">
                                    <div class="form-group">
                                        <label for="saatBitis">Kısıtlama Bitiş Saati</label>
                                        <input
                                            v-model="kisitlar.saatBitis"
                                            type="time"
                                            :min="kisitlar.saatBaslangic"
                                            class="form-control"
                                            data-date-container='#datepicker2'
                                            data-provide="datepicker"
                                            data-date-autoclose="true" id="saatBitis"
                                        />
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-6 mb-2">
                                    <div class="form-group">
                                        <label for="kullanicilar">İzin Verilen Kullanıcılar</label>
                                        <v-select
                                            v-model="kisitlar.kullanicilar"
                                            :options="kullanicilar"
                                            label="name"
                                            multiple
                                            id="kullanicilar"
                                        ></v-select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-6 mb-2">
                                    <div class="form-group">
                                        <label for="roller">İzin Verilen Roller</label>
                                        <v-select
                                            v-model="kisitlar.roller"
                                            :options="roller"
                                            label="name"
                                            multiple
                                            id="roller"
                                        ></v-select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-12 mb-2">
                                    <label for="aciklama">İzin Verilen IP'ler</label>
                                        <textarea
                                            v-model="kisitlar.ipler"
                                            class="form-control"
                                            id="ip"
                                            rows="3"
                                            placeholder="İzin Verilen IP'leri virgül ile giriniz..."
                                        ></textarea>
                                        <small class="text-muted">Bir aralık - ile belirtilir Örn: 192.345.1.2, 192.345.1.3 - 192.345.1.255</small>
                                </div>
                            </template>
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
                        baslik: "Giriş Kısıtlama Yönetimi",
                    },
                    sayfalar: [{
                            kod: "ANASAYFA",
                            baslik: "Giriş Kısıtlama Yönetimi",
                        }
                    ],
                    yukleniyor: false,
                    kisitlar: @json($kisitlar),
                    kullanicilar: @json($kullanicilar),
                    roller: @json($roller)
                };
            },
            methods: {
                kisitGuncelle() {
                    axios.post("{{ route('kisitGuncelle') }}", {
                            saatBaslangic: this.kisitlar.saatBaslangic,
                            saatBitis: this.kisitlar.saatBitis,
                            ipler: this.kisitlar.ipler,
                            id: this.kisitlar.id,
                            kullanicilar: this.kisitlar.kullanicilar,
                            roller: this.kisitlar.roller
                        })
                        .then(response => {
                            this.yukleniyor = true;

                            if (!response.data.durum) {
                                return this.uyariAc({
                                    baslik: 'Hata',
                                    mesaj: response.data.mesaj,
                                    tur: "error"
                                });
                            }

                            this.yukleniyor = false;
                            this.uyariAc({
                                toast: {
                                    status: true,
                                    message: response.data.mesaj,
                                },
                            });


                        })
                        .catch(error => {
                            this.yukleniyor = false;
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response
                                    .data.hataKodu,
                                tur: "error"
                            });
                            console.log(error);
                        });

                }

            }
        };
    </script>
@endsection
