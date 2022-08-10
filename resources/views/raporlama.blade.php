@extends('layout') 
@section('content')
<div class="row doruk-content">
    <h4 style="color:#999"><i class="fa fa-chart-line"></i> RAPORLAMA</h4>
    @can("siparis_ucreti_goruntuleme")
        <!-- GENEL İŞLEM RAPORLARI -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row d-flex align-items-center">
                        <div class="col">
                            <h4>GENEL İŞLEM RAPORLARI</h4>
                        </div>
                        <div class="col-auto">
                            <div class="row d-flex align-items-center">
                                <div class="col">
                                    <div class="input-group">
                                        <input
                                            v-model="islemlerFiltrelemeObjesi.arama"
                                            type="text"
                                            class="form-control"
                                            placeholder="Arama"
                                            aria-label="Arama"
                                            aria-describedby="arama"
                                            @keyup.enter="isilIslemleriGetir()"
                                        />
                                        <span @click="isilIslemleriGetir()" class="input-group-text waves-effect" id="arama">
                                            <i class="mdi mdi-magnify"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-auto ps-0">
                                    <!-- Filtreleme butonu -->
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#islemFiltrelemeModal">
                                        <i class="fa fa-filter"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <small class="text-muted">
                                        Sipariş no, firma, fırın, malzeme...
                                    </small>
                                </div>
                            </div>

                            <div class="modal fade" id="islemFiltrelemeModal" tabindex="-1" aria-labelledby="islemFiltrelemeModal" aria-hidden="true">
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
                                                        <label for="firinFiltre">Fırın</label>
                                                        <v-select
                                                            v-model="islemlerFiltrelemeObjesi.firin"
                                                            :options="firinlar"
                                                            label="ad"
                                                            multiple
                                                            id="firinFiltre"
                                                        ></v-select>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label for="islemTarihAraligi">Tarih Aralığı</label>
                                                    <div class="input-group" id="islemTarihAraligi">
                                                        <span class="input-group-text d-none d-sm-block">Başlangıç</span>
                                                        <input
                                                            v-model="islemlerFiltrelemeObjesi.baslangicTarihi"
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
                                                            v-model="islemlerFiltrelemeObjesi.bitisTarihi"
                                                            type="date"
                                                            class="form-control"
                                                            placeholder="Bitiş"
                                                            data-date-container='#datepicker2'
                                                            data-provide="datepicker"
                                                            data-date-autoclose="true"
                                                            id="tarih"
                                                            aria-label="Bitiş"
                                                        />
                                                        <span @click="islemTarihAraligiTemizle()" class="input-group-text waves-effect" id="islemTarihAraligiButon">
                                                            <i class="fa fa-eraser"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="sayfalamaSayisi">Sayfalama Sayısı</label>
                                                        <v-select
                                                            v-model="islemlerFiltrelemeObjesi.limit"
                                                            :options="sayfalamaSayilari"
                                                            id="sayfalamaSayisi"
                                                        ></v-select>
                                                        <small class="text-muted">Gösterilecek kayıt sayısı</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            @can("siparis_ucreti_goruntuleme")
                                                <button type="button" class="btn btn-success" data-bs-dismiss="modal" @click="excelCikti()">
                                                    <i class="fas fa-file-excel"></i>
                                                    EXCEL
                                                </button>
                                            @endcan
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">VAZGEÇ</button>
                                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="isilIslemleriGetir()">ARA</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- yükleniyor -->
                    <div v-if="yukleniyorObjesi.islemler">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Yükleniyor...</span>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div class="table-responsive">
                            <table id="tech-companies-1" class="table table-striped mb-1 table-bordered table-centered table-nowrap table-sm">
                                <thead>
                                    <tr>
                                        <th>Sipariş No</th>
                                        <th>Resim</th>
                                        <th>Fırın</th>
                                        <th>Firma</th>
                                        <th>Malzeme</th>
                                        <th>İşlem</th>
                                        <th>Net KG</th>
                                        @can("siparis_ucreti_goruntuleme")
                                            <th>Birim Fiyat</th>
                                            <th>Tutar (₺)</th>
                                            <th>Tutar ($)</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="!_.size(islemler.data)">
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
                                                :key="iIndex"
                                            >
                                                <td>
                                                    @{{ islem.siparisNo }}
                                                </td>
                                                <td class="text-center">
                                                    <img
                                                        :src="islem.resimYolu ? islem.resimYolu : varsayilanResimYolu"
                                                        class="kg-resim-sec"
                                                        @click.stop="resimOnizlemeAc(islem.resimYolu)"
                                                    />
                                                </td>
                                                <td>
                                                    <span class="badge badge-pill" :class="`bg-${ islem.firinRenk }`">@{{ islem.firinAdi }}</span>
                                                </td>
                                                <td>
                                                    @{{ islem.firmaAdi }}
                                                </td>
                                                <td>
                                                    @{{ islem.malzemeAdi }}
                                                </td>
                                                <td>
                                                    @{{ islem.islemTuruAdi ?? '-' }}
                                                </td>
                                                <td>
                                                    @{{ islem.net }} kg
                                                </td>
                                                @can("siparis_ucreti_goruntuleme")
                                                    <td>
                                                        @{{ islem.birimFiyatYazi }}
                                                    </td>
                                                    <td>
                                                        @{{ islem.tutarTLYazi }}
                                                    </td>
                                                    <td>
                                                        @{{ islem.tutarUSDYazi }}
                                                    </td>
                                                @endcan
                                            </tr>
                                        </template>
                                        @can("siparis_ucreti_goruntuleme")
                                        @endcan
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row d-flex align-items-center justify-content-between">
                        <div class="col-auto"></div>
                        <div class="col">
                            <ul class="pagination pagination-rounded justify-content-center mb-0">
                                <li class="page-item">
                                    <button class="page-link" :disabled="!islemler.prev_page_url" @click="isilIslemleriGetir(islemler.prev_page_url)">
                                        <i class="fas fa-angle-left"></i>
                                    </button>
                                </li>
                                <li
                                    v-for="sayfa in sayfalamaAyarla(islemler.last_page, islemler.current_page)"
                                    class="page-item"
                                    :class="[sayfa.aktif ? 'active' : '']"
                                >
                                    <button class="page-link" @click="sayfa.tur === 'SAYFA' ? isilIslemleriGetir('{{ route("islemler") }}?page=' + sayfa.sayfa) : ()  => {}">@{{ sayfa.sayfa }}</button>
                                </li>
                                <li class="page-item">
                                    <button class="page-link" :disabled="!islemler.next_page_url" @click="isilIslemleriGetir(islemler.next_page_url)">
                                        <i class="fas fa-angle-right"></i>
                                    </button>
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

        <!-- CİROLAR -->
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
                                <h4 class="m-0">
                                    FIRIN BAZLI TONAJ
                                    @can("siparis_ucreti_goruntuleme")
                                        /TUTAR
                                    @endcan
                                </h4>
                            </div>

                            <div class="col-12">
                                <h6>
                                    @{{ firinBazliTonaj.toplamlar.firinSayisi ?? 0 }} Fırın |
                                    @{{ firinBazliTonaj.toplamlar.tonajYazi ? ins1000Sep(firinBazliTonaj.toplamlar.tonajYazi) : 0 }} |
                                    @{{ firinBazliTonaj.toplamlar.tutarTLYazi ? ins1000Sep(firinBazliTonaj.toplamlar.tutarTLYazi) : 0 }} &
                                    @{{ firinBazliTonaj.toplamlar.tutarUSDYazi ? ins1000Sep(firinBazliTonaj.toplamlar.tutarUSDYazi) : 0 }}
                                </h6>
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
                                <h3>@{{ ins1000Sep(firin.tonajYazi) }}</h3>
                                <h5>@{{ tonaCevir(firin.tonaj, true) }} Ton</h5>
                                @can("siparis_ucreti_goruntuleme")
                                    <hr />
                                    <h5>
                                        Kazanılan TL Tutar: @{{ ins1000Sep(firin.tutarTLYazi) }}
                                    </h5>
                                    <h5>
                                        Kazanılan USD Tutar: @{{ ins1000Sep(firin.tutarUSDYazi) }}
                                    </h5>
                                    {{-- <h6>
                                        KG Başı Tutar: @{{ ins1000Sep(
                                            formatNum(
                                                birimBasiTutar(firin.tonaj, firin.tutar)
                                            )
                                        )}} ₺
                                    </h6> --}}
                                @endcan
                            </div>
                            <hr class="m-0" />
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
                                    height="400"
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
                                                <b>
                                                    @{{ ins1000Sep(firma.tonajYazi) }}
                                                </b>
                                            </div>
                                            <div class="col-12">
                                                @{{ tonaCevir(firma.tonaj, true) }} Ton
                                            </div>
                                        </div>
                                    </td>
                                    @can("siparis_ucreti_goruntuleme")
                                        <td class="kisa-uzunluk">
                                            <div class="col-12">
                                                <b>
                                                    @{{ firma.tutarTLYazi }}
                                                </b>
                                            </div>
                                            <div class="col-12">
                                                <b>
                                                    @{{ firma.tutarUSDYazi }}
                                                </b>
                                            </div>
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
                                <button class="page-link" :disabled="!firmaBazliBilgiler.firmalar.prev_page_url" @click="firmaBazliBilgileriGetir(firmaBazliBilgiler.firmalar.prev_page_url)">
                                    <i class="fas fa-angle-left"></i>
                                </button>
                            </li>
                            <li
                                v-for="sayfa in sayfalamaAyarla(firmaBazliBilgiler.firmalar.last_page, firmaBazliBilgiler.firmalar.current_page)"
                                class="page-item"
                                :class="[sayfa.aktif ? 'active' : '']"
                            >
                                <button class="page-link" @click="sayfa.tur === 'SAYFA' ? firmaBazliBilgileriGetir(`{{ route("firmaBazliBilgileriGetir") }}?page=` + sayfa.sayfa) : ()  => {}">@{{ sayfa.sayfa }}</button>
                            </li>
                            <li class="page-item">
                                <button class="page-link" :disabled="!firmaBazliBilgiler.firmalar.next_page_url" @click="firmaBazliBilgileriGetir(firmaBazliBilgiler.firmalar.next_page_url)">
                                    <i class="fas fa-angle-right"></i>
                                </button>
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
            if (val === null || val === undefined) {
                return;
            }

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
                        islemler: false,
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
                            yaxis: {
                                labels: {
                                    formatter: function (val) {
                                        return ins1000Sep(formatNum(val));
                                    }
                                }
                            },

                            dataLabels: {
                                formatter: function (val, opts) {
                                    let paraBirimi = opts.w.config.series[opts.seriesIndex].name === "TL" ? " ₺" : " $";
                                    return ins1000Sep(formatNum(val)) + paraBirimi;
                                }
                            },
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: function (val, opts) {
                                        let paraBirimi = opts.w.config.series[opts.seriesIndex].name === "TL" ? " ₺" : " $";
                                        return ins1000Sep(formatNum(val)) + paraBirimi;
                                    }
                                },
                                enabled: true,
                                shared: true,
                                intersect: false,
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
                            yaxis: {
                                labels: {
                                    formatter: function (val) {
                                        return ins1000Sep(formatNum(val));
                                    }
                                }
                            },
                            dataLabels: {
                                formatter: function (val, opts) {
                                    let paraBirimi = opts.w.config.series[opts.seriesIndex].name === "TL" ? " ₺" : " $";
                                    return ins1000Sep(formatNum(val)) + paraBirimi;
                                }
                            },
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: function (val, opts) {
                                        let paraBirimi = opts.w.config.series[opts.seriesIndex].name === "TL" ? " ₺" : " $";
                                        return ins1000Sep(formatNum(val)) + paraBirimi;
                                    }
                                },
                                enabled: true,
                                shared: true,
                                intersect: false,
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
                        toplamlar: {},
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
                                categories: [],
                                labels: {
                                    formatter: function (val) {
                                        const karakterSayisi = _.size(val);
                                        if (karakterSayisi > 16) {
                                            return [val.substring(0, 16), val.substring(16)];
                                        } else {
                                            return val;
                                        }
                                    }
                                },
                            },
                            dataLabels: {},
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                }
                            },
                            tooltip: {
                                y: {},
                                enabled: true,
                                shared: true,
                                intersect: false,
                            },
                            noData: {
                                text: "Veri bulunamadı",
                            },
                        },
                        series: [],
                    },
                    islemler: {},
                    islemlerFiltrelemeObjesi: {
                        arama: "",
                        firin: null,
                        baslangicTarihi: null,
                        bitisTarihi: null,
                        limit: 10,
                    },
                    sayfalamaSayilari: [10, 25, 50, 100, 250, 500],
                    firinlar: @json($firinlar),
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

                this.isilIslemleriGetir();
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

                        this.yillikCiro.series = [];
                        _.forEach(yillikCiro.ciro, (ciro, paraBirimi) => {
                            this.yillikCiro.series.push({
                                name: paraBirimi,
                                data: ciro,
                            });
                        });

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

                        this.aylikCiro.series = [];
                        _.forEach(aylikCiro.ciro, (ciro, paraBirimi) => {
                            this.aylikCiro.series.push({
                                name: paraBirimi,
                                data: ciro
                            });
                        });

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
                        this.firinBazliTonaj.toplamlar = response.data.toplamlar;
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
                tonaCevir(kilo, yazi = false) {
                    const ton = _.round(kilo / 1000, 2);
                    return !yazi ? ton : ton.toString().split(".").join(",");
                },
                birimBasiTutar(birim, tutar) {
                    return (tutar / birim).toFixed(2);
                },
                isilIslemleriGetir(url = "{{ route('islemler') }}", cikti = false) {
                    this.yukleniyorObjesi.islemler = true;
                    axios.get(url, {
                        params: {
                            cikti,
                            filtreleme: this.islemlerFiltrelemeObjesi,
                        },
                        responseType: cikti ? "blob" : "json",
                    })
                    .then(async response => {
                        this.yukleniyorObjesi.islemler = false;

                        if (cikti) {
                            const dosyaAdi = 'İşlem Listesi ' + moment().format('L LTS');
                            const uzanti = "xlsx";
                            // convert blob
                            const blob = new Blob([response.data]);
                            if (this.isNativeApp) {
                                const base64 = await this.blobToBase64(blob);
                                window.ReactNativeWebView.postMessage(JSON.stringify({
                                    kod: "INDIR",
                                    dosya: base64,
                                    dosyaAdi: dosyaAdi,
                                    dosyaUzantisi: uzanti,
                                }));
                                return;
                            }
                            const url = window.URL.createObjectURL(blob);
                            const link = document.createElement('a');
                            link.href = url;
                            link.download = dosyaAdi + '.' + uzanti;
                            link.click();

                            window.URL.revokeObjectURL(url);

                            return;
                        }

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
                excelCikti() {
                    this.isilIslemleriGetir(undefined, true);
                },
                islemTarihAraligiTemizle() {
                    this.islemlerFiltrelemeObjesi.baslangicTarihi = null;
                    this.islemlerFiltrelemeObjesi.bitisTarihi = null;
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