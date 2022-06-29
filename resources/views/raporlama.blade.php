@extends('layout') 
@section('content')
<div class="row doruk-content">
    <h4 style="color:#999"><i class="fa fa-chart-line"></i> RAPORLAMA</h4>
    @can("siparis_ucreti_goruntuleme")
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>YILLIK CİRO</h4>
                </div>
                <div class="card-body">
                    <!-- yükleniyor -->
                    <div v-if="yukleniyorObjesi.yillikCiro">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Yükleniyor...</span>
                            </div>
                        </div>
                    </div>
                    <apexchart v-else type="bar" :options="yillikCiro.chartOptions" :series="yillikCiro.series"></apexchart>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4>AYLIK CİRO</h4>
                        </div>
                        <div class="col-auto">
                            <v-select
                                style="min-width: 111px"
                                v-model="aylikCiro.aktifYil"
                                :options="yillar"
                                id="aylikCiro"
                                @input="aylikCiroGetir"
                            ></v-select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- yükleniyor -->
                    <div v-if="yukleniyorObjesi.aylikCiro">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Yükleniyor...</span>
                            </div>
                        </div>
                    </div>
                    <apexchart v-else type="bar" :options="aylikCiro.chartOptions" :series="aylikCiro.series"></apexchart>
                </div>
            </div>
        </div>
    @endcan

    <!-- FIRIN BAZLI TONAJ/FİYAT -->
    <div class="col-12">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="row d-flex align-items-center">
                    <div class="col">
                        <div class="row">
                            <div class="col-12">
                                <h4 class="m-0">FIRIN BAZLI TONAJ @can("siparis_ucreti_goruntuleme") /TUTAR @endcan</h4>
                            </div>
                            <div class="col-12">
                                <div class="btn-group mr-2" role="group" aria-label="First group">
                                    <button
                                        type="button"
                                        class="btn"
                                        :class="{
                                            'btn-primary': firinBazliTonaj.orderTuru === 'tonaj',
                                            'btn-secondary': firinBazliTonaj.orderTuru !== 'tonaj'
                                        }"
                                        @click="orderTuruAyarla('FIRIN', 'tonaj')"
                                        key="ordered-tonaj"
                                    >
                                        Tonaj
                                        <i v-if="firinBazliTonaj.orderTuru === 'tonaj'" class="mdi mdi-arrow-down"></i>
                                    </button>
                                    @can("siparis_ucreti_goruntuleme")
                                        <button
                                            type="button"
                                            class="btn"
                                            :class="{
                                                'btn-primary': firinBazliTonaj.orderTuru === 'tutar',
                                                'btn-secondary': firinBazliTonaj.orderTuru !== 'tutar'
                                            }"
                                            @click="orderTuruAyarla('FIRIN', 'tutar')"
                                            key="ordered-tutar"
                                        >
                                            Tutar
                                            <i v-if="firinBazliTonaj.orderTuru === 'tutar'" class="mdi mdi-arrow-down"></i>
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="input-group">
                            <span class="input-group-text d-none d-sm-block">Başlangıç</span>
                            <input
                                v-model="firinBazliTonaj.baslangicTarihi"
                                type="date"
                                class="form-control"
                                placeholder="Başlangıç"
                                data-date-container='#datepicker2'
                                data-provide="datepicker"
                                data-date-autoclose="true"
                                id="tarih"
                                aria-label="Başlangıç"
                            />
                            <span class="input-group-text d-none d-sm-block">Bitiş</span>
                            <input
                                v-model="firinBazliTonaj.bitisTarihi"
                                type="date"
                                class="form-control"
                                placeholder="Bitiş"
                                data-date-container='#datepicker2'
                                data-provide="datepicker"
                                data-date-autoclose="true"
                                id="tarih"
                                aria-label="Bitiş"
                            />
                            <span @click="firinBazliTonajGetir()" class="input-group-text waves-effect" id="firinBazliTonaj">
                                <i class="mdi mdi-magnify"></i>
                            </span>
                            <span @click="firinBazliTonajFiltreTemizle()" class="input-group-text waves-effect" id="firinBazliTonaj">
                                <i class="fa fa-eraser"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- yükleniyor -->
            <div class="col-12 mt-5" v-if="yukleniyorObjesi.firinBazliTonaj">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Yükleniyor...</span>
                    </div>
                </div>
            </div>
            <template v-else>
                <template v-if="_.size(firinBazliTonaj.firinlar) > 0">
                    <div v-for="(firin, index) in firinBazliTonaj.firinlar" class="col-12 col-md-6" :key="index">
                        <div class="card">
                            <div class="card-header">
                                <div class="row d-flex justify-content-between">
                                    <div class="col">
                                        @{{ firin.ad }}
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge" :class="`bg-${ firin.json ? firin.json.renk : 'primary' }`">@{{ firin.kod }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <h3>@{{ tonaCevir(firin.tonaj) }} Ton</h3>
                                <h5>@{{ firin.tonaj }} KG</h5>
                                @can("siparis_ucreti_goruntuleme")
                                    <hr />
                                    <h5>
                                        Kazanılan Tutar: @{{ ins1000Sep(
                                            formatNum(firin.tutar)
                                        ) }} ₺
                                    </h5>
                                    <h6>
                                        KG Başı Tutar: @{{ ins1000Sep(
                                            formatNum(
                                                birimBasiTutar(firin.tonaj, firin.tutar)
                                            )
                                        )}} ₺
                                    </h6>
                                @endcan
                            </div>
                            <hr />
                            <div v-if="firinBazliIslemTuru.hazirlananChartBilgileri[firin.id]" class="card-body">
                                <h5>İşlem Türleri</h5>
                                <!-- yükleniyor -->
                                <div class="text-center" v-if="yukleniyorObjesi.firinBazliIslemTurleri">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Yükleniyor...</span>
                                    </div>
                                </div>
                                <apexchart
                                    v-else
                                    :options="firinBazliIslemTuru.hazirlananChartBilgileri[firin.id].chartOptions"
                                    :series="firinBazliIslemTuru.hazirlananChartBilgileri[firin.id].chartSeries"
                                    type="bar"
                                ></apexchart>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="col-12">
                        <div class="row p-5 d-flex align-items-center justify-content-center">
                            Kayıt bulunamadı
                        </div>
                    </div>
                </template>
            </template>
        </div>
    </div>

    <!-- FİRMA BAZLI TONAJ/FİYAT -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row d-flex align-items-center justify-content-between">
                    <div class="col-12">
                        <h4>FİRMA BAZLI TONAJ @can("siparis_ucreti_goruntuleme") /TUTAR @endcan</h4>
                    </div>
                    <div class="col-12 col-md-auto">
                        <div class="row d-flex align-items-center">
                            <div class="col">
                                <div class="input-group">
                                    <input
                                        v-model="firmaBazliBilgiler.arama"
                                        type="text"
                                        class="form-control"
                                        placeholder="Arama"
                                        aria-label="Arama"
                                        aria-describedby="arama"
                                        @keyup.enter="firmaBazliBilgileriGetir()"
                                    />
                                    <span @click="firmaBazliBilgileriGetir()" class="input-group-text waves-effect" id="arama">
                                        <i class="mdi mdi-magnify"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <small class="text-muted">
                                    Firma adı, firma sorumlusu...
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-auto">
                        <div class="row d-flex align-items-center">
                            <div class="input-group">
                                <span class="input-group-text d-none d-sm-block">Başlangıç</span>
                                <input
                                    v-model="firmaBazliBilgiler.baslangicTarihi"
                                    type="date"
                                    class="form-control"
                                    placeholder="Başlangıç"
                                    data-date-container='#datepicker2'
                                    data-provide="datepicker"
                                    data-date-autoclose="true"
                                    id="tarih"
                                    aria-label="Başlangıç"
                                />
                                <span class="input-group-text d-none d-sm-block">Bitiş</span>
                                <input
                                    v-model="firmaBazliBilgiler.bitisTarihi"
                                    type="date"
                                    class="form-control"
                                    placeholder="Bitiş"
                                    data-date-container='#datepicker2'
                                    data-provide="datepicker"
                                    data-date-autoclose="true"
                                    id="tarih"
                                    aria-label="Bitiş"
                                />
                                <span @click="firmaBazliBilgileriGetir()" class="input-group-text waves-effect" id="firmaBazliBilgiArama">
                                    <i class="mdi mdi-magnify"></i>
                                </span>
                                <span @click="firmaBazliBilgileriFiltreTemizle()" class="input-group-text waves-effect" id="firmaBazliBilgiTemizle">
                                    <i class="fa fa-eraser"></i>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <small class="text-muted">
                                    Tarih aralıkları belirleyebilirsiniz...
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tech-companies-1" class="table table-hover table-centered">
                        <thead>
                            <tr>
                                <th>Firma ID</th>
                                <th>Firma/Sahibi</th>
                                <th>
                                    <span
                                        v-if="firmaBazliBilgiler.orderTuru === 'tonaj'"
                                        class="bg-primary text-white p-1 rounded"
                                        key="ordered"
                                    >
                                        Tonaj
                                        <i class="mdi mdi-arrow-down"></i>
                                    </span>
                                    <span
                                        v-else
                                        @click="orderTuruAyarla('FIRMA', 'tonaj')"
                                        class="waves-effect"
                                        style="cursor: pointer;"
                                        key="unordered"
                                    >
                                        Tonaj
                                    </span>
                                </th>
                                @can("siparis_ucreti_goruntuleme")
                                    <th>
                                        <span
                                            v-if="firmaBazliBilgiler.orderTuru === 'tutar'"
                                            class="bg-primary text-white p-1 rounded"
                                        >
                                            Tutar
                                            <i class="mdi mdi-arrow-down"></i>
                                        </span>
                                        <span
                                            v-else
                                            @click="orderTuruAyarla('FIRMA', 'tutar')"
                                            class="waves-effect"
                                            style="cursor: pointer;"
                                        >
                                            Tutar
                                        </span>
                                    </th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            <!-- yükleniyor -->
                            <tr v-if="yukleniyorObjesi.firmaBazliBilgiler" key="yukleniyor">
                                <td colspan="100%">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Yükleniyor...</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <template v-else-if="_.size(firmaBazliBilgiler.firmalar.data) > 0">
                                <tr v-for="(firma, index) in firmaBazliBilgiler.firmalar.data" :key="index">
                                    <td class="kisa-uzunluk"># @{{ firma.id }}</td>
                                    <td class="orta-uzunluk">
                                        <div class="row">
                                            <div class="col-12">
                                                <b>@{{ firma.firmaAdi }}</b>
                                            </div>
                                            <div class="col-12">
                                                @{{ firma.sorumluKisi }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="kisa-uzunluk">
                                        <div class="row">
                                            <div class="col-12">
                                                <b>@{{ tonaCevir(firma.tonaj) }} Ton</b>
                                            </div>
                                            <div class="col-12">
                                                @{{ firma.tonaj }} KG
                                            </div>
                                        </div>
                                    </td>
                                    @can("siparis_ucreti_goruntuleme")
                                        <td class="kisa-uzunluk">
                                            <b>
                                                @{{ ins1000Sep(formatNum(firma.tutar ? firma.tutar : 0)) }} ₺
                                            </b>
                                        </td>
                                    @endcan
                                </tr>
                            </template>
                            <template v-else>
                                <tr key="kayit_bulunamadi">
                                    <td colspan="100%" class="text-center p-5">
                                        Kayıt bulunamadı
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
                                <button class="page-link" :disabled="!firmaBazliBilgiler.firmalar.prev_page_url" @click="firmaBazliBilgileriGetir(firmaBazliBilgiler.firmalar.prev_page_url)">Önceki</button>
                            </li>
                            <li
                                v-for="sayfa in firmaBazliBilgiler.firmalar.last_page"
                                class="page-item"
                                :class="[firmaBazliBilgiler.firmalar.current_page === sayfa ? 'active' : '']"
                            >
                                <button class="page-link" @click='firmaBazliBilgileriGetir("{{ route("firmaBazliBilgileriGetir") }}?page=" + sayfa)'>@{{ sayfa }}</button>
                            </li>
                            <li class="page-item">
                                <button class="page-link" :disabled="!firmaBazliBilgiler.firmalar.next_page_url" @click="firmaBazliBilgileriGetir(firmaBazliBilgiler.firmalar.next_page_url)">Sonraki</button>
                            </li>
                        </ul>
                    </div>
                    <div class="col-auto">
                        <small class="text-muted">Toplam Kayıt: @{{ firmaBazliBilgiler.firmalar.total }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <!-- apexcharts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.3/apexcharts.min.js"></script>
    <!-- vue apexcharts -->
    <script src="https://unpkg.com/vue-apexcharts"></script>

    <script>

        function ins1000Sep(val) {
            val = val.split(",");
            val[0] = val[0].split("").reverse().join("");
            val[0] = val[0].replace(/(\d{3})/g, "$1.");
            val[0] = val[0].split("").reverse().join("");
            val[0] = val[0].indexOf(".") == 0 ? val[0].substring(1) : val[0];
            return val.join(",");
        }

        function rem1000Sep(val) {
            return val.replace(/./g, "");
        }

        function formatNum(val) {
            val = Math.round(val * 100) / 100;
            val = val.toString().replace(".", ",");
            val = ("" + val).indexOf(",") > -1 ? val + "00" : val + ",00";
            var dec = val.indexOf(",");
            return dec == val.length-3 || dec == 0 ? val : val.substring(0, dec+3);
        }

        let mixinApp = {
            data() {
                return {
                    siparisYillari: {
                        ilkSiparisYili: {{ $ilkSiparisYili }},
                        sonSiparisYili: {{ $sonSiparisYili }},
                    },
                    yukleniyorObjesi: {
                        aylikCiro: false,
                        yillikCiro: false,
                        firinBazliTonaj: false,
                        firmaBazliBilgiler: false,
                        firinBazliIslemTurleri: false,
                    },
                    yillikCiro: {
                        chartOptions: {
                            chart: {
                                id: 'chart-yillik-ciro'
                            },
                            xaxis: {
                                labels: {
                                    rotate: -45
                                },
                                categories: []
                            },
                            dataLabels: {
                                formatter: function (val) {
                                    return ins1000Sep(formatNum(val)) + " ₺";
                                }
                            },
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: function (val) {
                                        return ins1000Sep(formatNum(val)) + " ₺";
                                    }
                                }
                            },
                            noData: {
                                text: "Veri bulunamadı",
                            },
                        },
                        series: [
                            {
                                name: 'Ciro',
                                data: []
                            },
                        ],
                    },
                    aylikCiro: {
                        aktifYil: null,
                        chartOptions: {
                            chart: {
                                id: 'chart-aylik-ciro'
                            },
                            xaxis: {
                                labels: {
                                    rotate: -45
                                },
                                categories: []
                            },
                            dataLabels: {
                                formatter: function (val) {
                                    return ins1000Sep(formatNum(val)) + " ₺";
                                }
                            },
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: function (val) {
                                        return ins1000Sep(formatNum(val)) + " ₺";
                                    }
                                }
                            },
                            noData: {
                                text: "Veri bulunamadı",
                            },
                        },
                        series: [
                            {
                                name: 'Ciro',
                                data: []
                            },
                        ]
                    },
                    firinBazliTonaj: {
                        orderTuru: "tonaj",
                        baslangicTarihi: null,
                        bitisTarihi: null,
                        firinlar: [],
                    },
                    firmaBazliBilgiler: {
                        orderTuru: "tonaj",
                        arama: "",
                        baslangicTarihi: null,
                        bitisTarihi: null,
                        firmalar: {},
                    },
                    firinBazliIslemTuru: {
                        orderTuru: "toplam",
                        baslangicTarihi: null,
                        bitisTarihi: null,
                        veriler: {},
                        hazirlananChartBilgileri: {},

                        chartOptions: {
                            colors: [
                                "#3AB0FF",
                                "#F94C66",
                            ],
                            chart: {
                                id: 'chart-firin-bazli-islem-turu',
                                stacked: true,
                            },
                            xaxis: {
                                labels: {
                                    rotate: -45
                                },
                                categories: []
                            },
                            dataLabels: {},
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                }
                            },
                            tooltip: {
                                y: {}
                            },
                            noData: {
                                text: "Veri bulunamadı",
                            },
                        },
                        series: [],
                    },
                };
            },
            computed: {
                // Sipariş yılları arasındaki yılları döner
                yillar() {
                    let yillar = [];
                    for (let i = this.siparisYillari.sonSiparisYili; i >= this.siparisYillari.ilkSiparisYili; i--) {
                        yillar.push(i);
                    }
                    return yillar;
                },
            },
            created() {
                Vue.use(VueApexCharts);
                Vue.component('apexchart', VueApexCharts);
            },
            mounted() {
                if (_.size(this.yillar)) {
                    this.aylikCiro.aktifYil = this.yillar[0];
                }

                this.yillikCiroGetir();
                this.aylikCiroGetir();
                this.firinBazliTonajGetir();
                this.firmaBazliBilgileriGetir();
                this.firinBazliIslemTurleriGetir();
            },
            methods: {
                yillikCiroGetir() {
                    this.yukleniyorObjesi.yillikCiro = true;
                    axios.get('/yillikCiroGetir')
                    .then(response => {
                        const yillikCiro = response.data.yillikCiro;

                        this.yillikCiro.chartOptions.xaxis.categories = yillikCiro.yillar;
                        this.yillikCiro.series[0].data = yillikCiro.ciro;

                        this.yillikCiro = _.cloneDeep(this.yillikCiro);
                        this.yukleniyorObjesi.yillikCiro = false;
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.yillikCiro = false;
                        console.log(error);
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                    });
                },
                aylikCiroGetir() {
                    this.yukleniyorObjesi.aylikCiro = true;
                    axios.get('/aylikCiroGetir', {
                        params: {
                            yil: this.aylikCiro.aktifYil
                        }
                    })
                    .then(response => {
                        const aylikCiro = response.data.aylikCiro;

                        this.aylikCiro.chartOptions.xaxis.categories = aylikCiro.aylar;
                        this.aylikCiro.series[0].data = aylikCiro.ciro;

                        this.aylikCiro = _.cloneDeep(this.aylikCiro);
                        this.yukleniyorObjesi.aylikCiro = false;
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.aylikCiro = false;
                        console.log(error);
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                    });
                },
                firinBazliTonajGetir() {
                    this.yukleniyorObjesi.firinBazliTonaj = true;
                    axios.get('/firinBazliTonaj', {
                        params: {
                            orderTuru: this.firinBazliTonaj.orderTuru,
                            baslangicTarihi: this.firinBazliTonaj.baslangicTarihi,
                            bitisTarihi: this.firinBazliTonaj.bitisTarihi,
                        }
                    })
                    .then(response => {
                        if (!response.data.durum) {
                            this.yukleniyorObjesi.firinBazliTonaj = false;
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: response.data.mesaj + " - Hata Kodu: " + response.data.hataKodu,
                                tur: "error"
                            });
                            return;
                        }

                        this.firinBazliTonaj.firinlar = response.data.firinlar;
                        this.firinBazliTonaj = _.cloneDeep(this.firinBazliTonaj);
                        this.yukleniyorObjesi.firinBazliTonaj = false;
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.firinBazliTonaj = false;
                        console.log(error);
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                    });
                },
                firinBazliTonajFiltreTemizle() {
                    this.firinBazliTonaj.baslangicTarihi = null;
                    this.firinBazliTonaj.bitisTarihi = null;
                    this.firinBazliTonajGetir();
                    this.firinBazliIslemTurleriGetir();
                },
                firmaBazliBilgileriGetir(url = '{{ route('firmaBazliBilgileriGetir') }}') {
                    this.yukleniyorObjesi.firmaBazliBilgiler = true;
                    axios.get(url, {
                        params: {
                            orderTuru: this.firmaBazliBilgiler.orderTuru,
                            arama: this.firmaBazliBilgiler.arama,
                            baslangicTarihi: this.firmaBazliBilgiler.baslangicTarihi,
                            bitisTarihi: this.firmaBazliBilgiler.bitisTarihi,
                        }
                    })
                    .then(response => {
                        if (!response.data.durum) {
                            this.yukleniyorObjesi.firmaBazliBilgiler = false;
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: response.data.mesaj + " - Hata Kodu: " + response.data.hataKodu,
                                tur: "error"
                            });
                            return;
                        }

                        this.firmaBazliBilgiler.firmalar = response.data.firmalar;
                        this.firmaBazliBilgiler = _.cloneDeep(this.firmaBazliBilgiler);
                        this.yukleniyorObjesi.firmaBazliBilgiler = false;
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.firmaBazliBilgiler = false;
                        console.log(error);
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                    });
                },
                firmaBazliBilgileriFiltreTemizle() {
                    this.firmaBazliBilgiler.baslangicTarihi = null;
                    this.firmaBazliBilgiler.bitisTarihi = null;
                    this.firmaBazliBilgileriGetir();
                },
                orderTuruAyarla(tip, orderTuru) {
                    if (tip === "FIRIN") {
                        this.firinBazliTonaj.orderTuru = orderTuru;
                        this.firinBazliTonajGetir();
                    }
                    else if (tip === "FIRMA") {
                        this.firmaBazliBilgiler.orderTuru = orderTuru;
                        this.firmaBazliBilgileriGetir();
                    }
                },
                firinBazliIslemTurleriGetir() {
                    this.yukleniyorObjesi.firinBazliIslemTurleri = true;

                    this.firinBazliIslemTuru.hazirlananChartBilgileri = {};

                    axios.get('/firinBazliIslemTurleriGetir', {
                        params: {
                            baslangicTarihi: this.firinBazliTonaj.baslangicTarihi,
                            bitisTarihi: this.firinBazliTonaj.bitisTarihi,
                        }
                    })
                    .then(response => {
                        if (!response.data.durum) {
                            this.yukleniyorObjesi.firinBazliIslemTurleri = false;
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: response.data.mesaj + " - Hata Kodu: " + response.data.hataKodu,
                                tur: "error"
                            });
                            return;
                        }

                        this.firinBazliIslemTuru.veriler = response.data.islemTurleri;

                        _.forEach(this.firinBazliIslemTuru.veriler.chartVerileri, (firin) => {
                            const chartOptions = _.cloneDeep(this.firinBazliIslemTuru.chartOptions);
                            const chartSeries = [];

                            chartOptions.xaxis.categories = firin.islemler;
                            chartSeries.push({
                                name: "İşlem",
                                data: firin.tekrarEtmeyenSayisi
                            });
                            chartSeries.push({
                                name: "Tekrar Eden İşlem",
                                data: firin.tekrarEdenSayisi
                            });

                            this.firinBazliIslemTuru.hazirlananChartBilgileri[firin.firinId] = {
                                chartOptions: chartOptions,
                                chartSeries: chartSeries
                            };
                        });

                        this.firinBazliIslemTuru = _.cloneDeep(this.firinBazliIslemTuru);

                        this.yukleniyorObjesi.firinBazliIslemTurleri = false;
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.firinBazliIslemTurleri = false;
                        console.log(error);
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                    });
                },
                tonaCevir(kilo) {
                    return (kilo / 1000).toFixed(3);
                },
                birimBasiTutar(birim, tutar) {
                    return (tutar / birim).toFixed(2);
                },
                ins1000Sep: ins1000Sep,
                formatNum: formatNum,
                rem1000Sep: rem1000Sep,
            }
        };
    </script>
@endsection

@section('style')
    <!-- apexcharts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.3/apexcharts.css">
@endsection