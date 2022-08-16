@extends('layout')

@section('content')
    <div class="d-inline-flex">
        <h4 style="color:#999">
            <i class="mdi mdi-stove"> </i>
            ISIL İŞLEMLER
        </h4>
        <div class="ms-1">
            <button @click="sorguParametreleriTemizle" v-if="sorguParametreleri.formId" class="btn btn-danger btn-sm">
                <b>Form ID: @{{ sorguParametreleri.formId }}</b>
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <template v-if="aktifSayfa.kod === 'ANASAYFA'">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">FORMLAR</h4>
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
                                            @keyup.enter="filtrele()"
                                            @input="gecikmeliFonksiyon.varsayilan()"
                                        />
                                        <span @click="filtrele()" class="input-group-text waves-effect" id="arama">
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

                                <div class="col-auto">
                                    @can("isil_islem_formu_kaydetme")
                                        <button @click="formEkleAc" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> FORM EKLE</button>                                
                                    @endcan
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
                                                        <div class="row d-flex align-items-center justify-space-between">
                                                            <div class="col">
                                                                <label for="tarihFiltre">Tarih</label>
                                                            </div>
                                                            <div class="col-auto">
                                                                <button
                                                                    v-if="filtrelemeObjesi.baslangicTarihi || filtrelemeObjesi.bitisTarihi"
                                                                    @click="filtrelemeTarihTemizle()"
                                                                    class="btn btn-sm btn-outline-danger p-0 m-0"
                                                                    type="button"
                                                                    aria-label="Tarih temizle"
                                                                >
                                                                    <span class="px-1">
                                                                        Tarihleri Temizle
                                                                        <i class="fa fa-times"></i>
                                                                    </span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="input-group mb-3">
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
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-auto m-0">
                                                    <div class="form-group">
                                                        <label for="sayfalamaSayisi">Sayfalama</label>
                                                        <v-select
                                                            v-model="sayfalamaSayisi"
                                                            :options="sayfalamaSayilari"
                                                            id="sayfalamaSayisi"
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
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-3">
                            <template v-if="yukleniyorObjesi.form">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Yükleniyor...</span>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <template v-if="formlar.data && formlar.data.length">
                                    <div class="table-rep-plugin">
                                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                                            <table id="tech-companies-1" class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ID/Takip No</th>
                                                        <th>Form Adı</th>
                                                        <th class="text-center">İşlem Sayısı</th>
                                                        <th>Baslangıç/Bitiş Tarihi</th>
                                                        <th class="text-center">İşlemler</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template v-for="(form, index) in formlar.data">
                                                        <tr
                                                            :key="index + 'form'"
                                                            @click="formIslemleriGetir(form.id, index)"
                                                            style="cursor: pointer;"
                                                        >
                                                            <td class="kisa-uzunluk">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <span># @{{ form.id }}</span>
                                                                        <i v-if="form.bitisTarihi" class="fas fa-check text-success"></i>
                                                                        <div class="d-inline-flex" v-if="form.islemYukleniyor">
                                                                            <div class="spinner-grow text-primary m-1 spinner-grow-sm" role="status">
                                                                                <span class="sr-only">Yükleniyor...</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <span class="badge badge-pill bg-primary">@{{ form.takipNo }}</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="uzun-uzunluk">@{{ form.formAdi }}</td>
                                                            <td class="text-center kisa-uzunluk">@{{ form.islemSayisi }}</td>
                                                            <td class="kisa-uzunluk">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <span>@{{ m(form.baslangicTarihi).format("L") }}</span>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <span v-if="form.bitisTarihi">
                                                                            @{{ m(form.bitisTarihi).format("L") }}
                                                                        </span>
                                                                        <small v-else class="text-muted">
                                                                            <i>(Form henüz tamamlanmadı)</i>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-center orta-uzunluk">
                                                                <div class="row g-1 d-inline-flex">
                                                                    <div class="col">
                                                                        <button @click.stop="formDetayGoruntule(form)" class="btn btn-info btn-sm">
                                                                            <i class="fas fa-eye"></i>
                                                                        </button>
                                                                    </div>

                                                                    @can("isil_islem_formu_duzenleme")
                                                                        <div class="col">
                                                                            <button @click.stop="formDuzenle(form)" class="btn btn-warning btn-sm">
                                                                                <i class="fas fa-edit"></i>
                                                                            </button>
                                                                        </div>
                                                                    @endcan

                                                                    @can("isil_islem_formu_silme")
                                                                        <div class="col">
                                                                            <button @click.stop="formSil(form)" class="btn btn-danger btn-sm">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </div>
                                                                    @endcan
                                                                    <div class="col-12">
                                                                        <span class="badge badge-pill bg-success">Son Düzenleyen: @{{ form.duzenleyen }}</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr
                                                            v-if="_.size(form.islemler) && form.islemlerAcik"
                                                            class="bg-primary py-0"
                                                            :key="index + 'formIslemleri'"
                                                        >
                                                            <td colspan="100%" class="text-center p-0">
                                                                <div class="col-12 text-center" style="overflow-y: auto; overflow-x: hidden; max-height: 400px; border: 1px solid blue;">
                                                                    <div class="row">
                                                                        <div
                                                                            v-for="(firin, fIndex) in form.islemler"
                                                                            :key="fIndex + 'firin'"
                                                                            class="col-12 p-0"
                                                                            :ref="'firin' + firin.firinId"
                                                                        >
                                                                            <div class="card text-start my-1" style="width: 100%">
                                                                                <div class="card-body">
                                                                                    <h5 class="card-title">
                                                                                        @{{ firin.firinAdi }}
                                                                                        <i v-if="firin.islemDurumuKodu === 'TAMAMLANDI'" class="fas fa-check text-success"></i>
                                                                                    </h5>
                                                                                    <template v-for="(sarj, sIndex) in firin.sarjlar">
                                                                                        <h6 class="card-subtitle my-2 text-muted" :key="sIndex + 'sarj'" :ref="'firin' + firin.firinId + 'sarj' + sarj.sarj">
                                                                                            @{{ sarj.sarj }}. Şarj
                                                                                            <i v-if="sarj.islemDurumuKodu === 'TAMAMLANDI'" class="fas fa-check text-success"></i>
                                                                                            <button
                                                                                                v-if="sarj.bekleyenIslemSayisi > 0"
                                                                                                class="btn btn-sm btn-success ms-1"
                                                                                                @click="sarjIslemleriBaslat(firin, sarj.sarj, form.id, index)"
                                                                                            >
                                                                                                <i class="fas fa-play me-1"></i>
                                                                                                İŞLEMLERİ BAŞLAT
                                                                                            </button>
                                                                                            <button
                                                                                                v-if="sarj.islemdekiIslemSayisi > 0"
                                                                                                class="btn btn-sm btn-danger ms-1"
                                                                                                @click="sarjIslemleriTamamla(firin, sarj.sarj, form.id, index)"
                                                                                            >
                                                                                                <i class="fas fa-play me-1"></i>
                                                                                                İŞLEMLERİ TAMAMLA
                                                                                            </button>
                                                                                        </h6>
                                                                                        <div
                                                                                            class="table-responsive overflow-hidden"
                                                                                            :key="sIndex + 'islemler'"
                                                                                        >
                                                                                            <table id="tech-companies-1" class="table table-striped table-hover">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>İşlem ID</th>
                                                                                                        <th>Resim</th>
                                                                                                        <th>Malzeme</th>
                                                                                                        <th>İşlem</th>
                                                                                                        <th class="text-center">İşlemler</th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                    <template v-for="(islem, iIndex) in sarj.islemler">
                                                                                                        <tr
                                                                                                            :key="iIndex + 'islemler'"
                                                                                                            :class="{
                                                                                                                'table-danger': !!islem.tekrarEdenId,
                                                                                                                'table-primary': islem.islemDurumuKodu === 'ISLEM_BEKLIYOR',
                                                                                                                'table-warning': islem.islemDurumuKodu === 'ISLEMDE',
                                                                                                                'table-success': islem.islemDurumuKodu === 'TAMAMLANDI',
                                                                                                            }"
                                                                                                        >
                                                                                                            <td>
                                                                                                                <div class="row">
                                                                                                                    <div class="col-12 d-inline-flex">
                                                                                                                        <span>
                                                                                                                            # @{{ islem.id }}
                                                                                                                            <i v-if="islem.islemDurumuKodu === 'TAMAMLANDI'" class="fas fa-check text-success"></i>
                                                                                                                        </span>
                                                                                                                        <div v-if="islem.tekrarEdilenId" class="ms-1">
                                                                                                                            <span class="badge rounded-pill bg-danger">Tekrar Edilen İşlem ID: @{{ islem.tekrarEdilenId }}</span>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="col-12">
                                                                                                                        <span class="badge badge-pill bg-primary">Sipariş No: @{{ islem.siparisNo }}</span>
                                                                                                                    </div>
                                                                                                                    <div class="col-12" v-if="islem.islemDurumuKodu !== 'TAMAMLANDI'">
                                                                                                                        <span class="badge badge-pill" :class="`bg-${ islem.gecenSureRenk }`">Termin: @{{ islem.gecenSure }} Gün</span>
                                                                                                                    </div>
                                                                                                                    <div class="col-12">
                                                                                                                        <small class="text-muted">Firma: @{{ islem.firmaAdi }}</small>
                                                                                                                    </div>
                                                                                                                    <div v-if="islem.bolunmusId" class="col-12">
                                                                                                                        <span class="badge rounded-pill bg-warning">
                                                                                                                            Bölünmüş İşlem ID: @{{ islem.bolunmusId }}
                                                                                                                        </span>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </td>
                                                                                                            <td class="text-center">
                                                                                                                <img
                                                                                                                    :src="islem.resimYolu ? islem.resimYolu : varsayilanResimYolu"
                                                                                                                    class="kg-resim-sec"
                                                                                                                    @click.stop="resimOnizlemeAc(islem.resimYolu)"
                                                                                                                />
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
                                                                                                                    <div class="col-12">
                                                                                                                        <small class="text-muted">Net: @{{ islem.net }} kg</small>
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
                                                                                                            <td class="uzun-uzunluk text-center align-center">
                                                                                                                <div class="btn-group row">
                                                                                                                    <div class="col-12">
                                                                                                                        <b :class="islem.islemDurumuRenk">
                                                                                                                            <i
                                                                                                                                class="ml-2"
                                                                                                                                :class="islem.islemDurumuIkon"
                                                                                                                            ></i>
                                                                                                                            @{{ islem.islemDurumuAdi }}
                                                                                                                        </b>
                                                                                                                    </div>
                                                                                                                    <hr class="m-2" />
                                                                                                                    <div class="col-12">
                                                                                                                        @can("isil_islem_duzenleme")
                                                                                                                            <button
                                                                                                                                class="btn btn-primary btn-sm"
                                                                                                                                @click.stop="islemBaslat(islem, index)"
                                                                                                                                v-if="islem.islemDurumuKodu === 'ISLEM_BEKLIYOR'"
                                                                                                                            >
                                                                                                                                <i class="mdi mdi-play"></i>
                                                                                                                            </button>
                                                                                                                            <button
                                                                                                                                v-else-if="islem.islemDurumuKodu === 'ISLEMDE'"
                                                                                                                                class="btn btn-success btn-sm"
                                                                                                                                @click.stop="islemTamamla(islem, index)"
                                                                                                                            >
                                                                                                                                <i class="mdi mdi-check"></i>
                                                                                                                            </button>
                                                                                                                        @endcan
                                                                                                                        <template v-if="islem.islemDurumuKodu === 'TAMAMLANDI'">
                                                                                                                            @can("isil_islem_duzenleme")
                                                                                                                                <button
                                                                                                                                    class="btn btn-danger btn-sm"
                                                                                                                                    @click.stop="islemTamamlandiGeriAl(islem, index)"
                                                                                                                                >
                                                                                                                                    <i class="mdi mdi-close"></i>
                                                                                                                                </button>
                                                                                                                            @endcan
                                                                                                                            <div v-if="islem.tekrarEdenId" class="col-12">
                                                                                                                                <span class="badge rounded-pill bg-danger">Tekrar Eden İşlem ID: @{{ islem.tekrarEdenId }}</span>
                                                                                                                            </div>
                                                                                                                        </template>
                                                                                                                        @can("isil_islem_duzenleme")
                                                                                                                            <button
                                                                                                                                v-if="islem.islemDurumuKodu === 'ISLEMDE'"
                                                                                                                                class="btn btn-warning btn-sm"
                                                                                                                                @click.stop="islemTekrar(islem, index)"
                                                                                                                            >
                                                                                                                                <i class="mdi mdi-replay"></i>
                                                                                                                            </button>
                                                                                                                        @endcan
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <template v-if="_.size(islem.tekrarEdenIslemler)">
                                                                                                            <tr :key="'tekrarEdenler' + iIndex" style="background-color: #F8747450; border: 1px solid #F87474;">
                                                                                                                <td colspan="100%" class="p-0">
                                                                                                                    <div class="d-grid">
                                                                                                                        <button class="btn btn-sm btn-danger btn-block rounded-0 m-0 p-0" type="button" data-bs-toggle="collapse" :data-bs-target="'#collapseExample' + iIndex" aria-expanded="false" :aria-controls="'collapseExample' + iIndex">
                                                                                                                            Tekrar Eden İşlemler <i class="mdi mdi-chevron-down"></i>
                                                                                                                        </button>
                                                                                                                    </div>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                            <template v-for="(tekrarEdenIslem, tiIndex) in islem.tekrarEdenIslemler">
                                                                                                                <tr
                                                                                                                    class="collapse"
                                                                                                                    :id="'collapseExample' + iIndex"
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
                                                                                                                            <div class="col-12">
                                                                                                                                <small class="text-muted">Firma: @{{ tekrarEdenIslem.firmaAdi }}</small>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </td>
                                                                                                                    <td class="text-center">
                                                                                                                        <img
                                                                                                                            :src="tekrarEdenIslem.resimYolu ? tekrarEdenIslem.resimYolu : varsayilanResimYolu"
                                                                                                                            class="kg-resim-sec"
                                                                                                                            @click.stop="resimOnizlemeAc(tekrarEdenIslem.resimYolu)"
                                                                                                                        />
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
                                                                                                                    <td class="uzun-uzunluk text-center align-center">
                                                                                                                        <div class="btn-group row">
                                                                                                                            <div class="col-12">
                                                                                                                                <b :class="tekrarEdenIslem.islemDurumuRenk">
                                                                                                                                    <i
                                                                                                                                        class="ml-2"
                                                                                                                                        :class="tekrarEdenIslem.islemDurumuIkon"
                                                                                                                                    ></i>
                                                                                                                                    @{{ tekrarEdenIslem.islemDurumuAdi }}
                                                                                                                                </b>
                                                                                                                            </div>
                                                                                                                            <hr class="m-2" />
                                                                                                                            <div class="col-12">
                                                                                                                                @can("isil_islem_duzenleme")
                                                                                                                                    <button
                                                                                                                                        class="btn btn-primary btn-sm"
                                                                                                                                        @click.stop="islemBaslat(tekrarEdenIslem, index)"
                                                                                                                                        v-if="tekrarEdenIslem.islemDurumuKodu === 'ISLEM_BEKLIYOR'"
                                                                                                                                    >
                                                                                                                                        <i class="mdi mdi-play"></i>
                                                                                                                                    </button>
                                                                                                                                    <button
                                                                                                                                        v-else-if="tekrarEdenIslem.islemDurumuKodu === 'ISLEMDE'"
                                                                                                                                        class="btn btn-success btn-sm"
                                                                                                                                        @click.stop="islemTamamla(tekrarEdenIslem, index)"
                                                                                                                                    >
                                                                                                                                        <i class="mdi mdi-check"></i>
                                                                                                                                    </button>
                                                                                                                                @endcan
                                                                                                                                <template v-if="tekrarEdenIslem.islemDurumuKodu === 'TAMAMLANDI'">
                                                                                                                                    @can("isil_islem_duzenleme")
                                                                                                                                        <button
                                                                                                                                            class="btn btn-danger btn-sm"
                                                                                                                                            @click.stop="islemTamamlandiGeriAl(tekrarEdenIslem, index)"
                                                                                                                                        >
                                                                                                                                            <i class="mdi mdi-close"></i>
                                                                                                                                        </button>
                                                                                                                                    @endcan
                                                                                                                                    <div v-if="tekrarEdenIslem.tekrarEdenId" class="col-12">
                                                                                                                                        <span class="badge rounded-pill bg-danger">Tekrar Eden İşlem ID: @{{ tekrarEdenIslem.tekrarEdenId }}</span>
                                                                                                                                    </div>
                                                                                                                                </template>
                                                                                                                                @can("isil_islem_duzenleme")
                                                                                                                                    <button
                                                                                                                                        v-if="tekrarEdenIslem.islemDurumuKodu === 'ISLEMDE'"
                                                                                                                                        class="btn btn-warning btn-sm"
                                                                                                                                        @click.stop="islemTekrar(tekrarEdenIslem, index)"
                                                                                                                                    >
                                                                                                                                        <i class="mdi mdi-replay"></i>
                                                                                                                                    </button>
                                                                                                                                @endcan
                                                                                                                            </div>
                                                                                                                            <div class="col-12">
                                                                                                                                <span class="badge badge-pill" :class="`bg-${ tekrarEdenIslem.firinRenk }`">@{{ tekrarEdenIslem.firinAdi }}</span>
                                                                                                                            </div>
                                                                                                                            <div class="col-12">
                                                                                                                                <span class="badge badge-pill bg-secondary">@{{ tekrarEdenIslem.sarj }}. ŞARJ</span>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr
                                                                                                                    v-if="tiIndex === (_.size(islem.tekrarEdenIslemler) - 1)"
                                                                                                                    class="collapse"
                                                                                                                    :id="'collapseExample' + iIndex"
                                                                                                                    :key="'d' + tiIndex + '_' + islem.id"
                                                                                                                >
                                                                                                                    <td colspan="100%">
                                                                                                                        <hr class="m-0 bg-danger" />
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
                                                                            </div>
                                                                        </div>
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
                                        <div class="row d-flex align-items-center justify-content-between">
                                            <div class="col-auto"></div>
                                            <div class="col">
                                                <ul class="pagination pagination-rounded justify-content-center mb-0">
                                                    <li class="page-item">
                                                        <button class="page-link" :disabled="!formlar.prev_page_url" @click="formlariGetir(formlar.prev_page_url)">
                                                            <i class="fas fa-angle-left"></i>
                                                        </button>
                                                    </li>
                                                    <li
                                                        v-for="sayfa in sayfalamaAyarla(formlar.last_page, formlar.current_page)"
                                                        class="page-item"
                                                        :class="[sayfa.aktif ? 'active' : '']"
                                                    >
                                                        <button class="page-link" @click="sayfa.tur === 'SAYFA' ? formlariGetir('/formlar?page=' + sayfa.sayfa) : ()  => {}">@{{ sayfa.sayfa }}</button>
                                                    </li>
                                                    <li class="page-item">
                                                        <button class="page-link" :disabled="!formlar.next_page_url" @click="formlariGetir(formlar.next_page_url)">
                                                            <i class="fas fa-angle-right"></i>
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-auto">
                                                <small class="text-muted">Toplam Kayıt: @{{ formlar.total }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="text-center">
                                        <h4>Isıl İşlem Formu Bulunamadı</h4>
                                    </div>
                                </template>
                            </template>
                        </div>
                        <!-- end col -->
                    </div>
                </template>
                <template v-else-if="aktifSayfa.kod === 'YENI_FORM'">
                    <div class="row">
                        <div class="col-12 col-sm-8">
                            <div class="d-flex flex-row align-items-center">
                                <button @click="geriAnasayfa" class="btn btn-warning"><i class="fas fa-arrow-left"></i> GERİ</button>
                                <h4 class="card-title m-0 ms-2 text-nowrap text-truncate">
                                    ISIL İŞLEM FORMU EKLEME
                                    <div class="col-12" v-if="aktifForm.duzenleyen">
                                        <span class="badge badge-pill bg-success">Son Düzenleyen: @{{ aktifForm.duzenleyen }}</span>
                                    </div>
                                    <div class="d-inline-flex" v-if="araYukleniyor">
                                        <div class="spinner-grow text-primary m-1 spinner-grow-sm" role="status">
                                            <span class="sr-only">Yükleniyor...</span>
                                        </div>
                                    </div>
                                </h4>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 text-end">
                            <button
                                @click="formHazirla()"
                                class="btn btn-primary"
                                :disabled="!_.size(aktifForm.secilenIslemler)"
                            >
                                FORM HAZIRLA
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 col-sm-6 col-md-4 mb-2">
                            <label class="form-label">Form Takip No *</label>
                            <input
                                v-model="aktifForm.takipNo"
                                v-mask="'TKP##########'"
                                class="form-control"
                                placeholder="Form numarası giriniz... (Örn: TKP2022060301)"
                                type="text"
                            />
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 mb-2">
                            <label class="form-label">Form Adı *</label>
                            <input
                                v-model="aktifForm.formAdi"
                                class="form-control"
                                placeholder="Form adı giriniz..."
                                type="text"
                            />
                            <small class="text-muted">Forma özel bir isim girebilirsiniz</small>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 mb-2">
                            <div class="form-group">
                                <label for="baslangicTarihi">Başlangıç Tarihi</label>
                                <input
                                    v-model="aktifForm.baslangicTarihi"
                                    type="date"
                                    class="form-control"
                                    placeholder="gg.aa.yyyy"
                                    data-date-container='#datepicker2'
                                    data-provide="datepicker"
                                    data-date-autoclose="true"
                                    id="baslangicTarihi"
                                />
                                <small class="text-muted">Bitiş tarihi, formdaki tüm işlemler bittiğinde otomatik olarak ayarlanır</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="baslangicTarihi">Açıklama</label>
                            <textarea
                                v-model="aktifForm.aciklama"
                                class="form-control"
                                id="aciklama"
                                name="aciklama"
                                rows="3"
                            ></textarea>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card-header">
                                    <div class="row d-flex align-items-center">
                                        <div class="col">
                                            <h4>İŞLEMLER</h4>
                                        </div>
                                        <div class="col-auto">
                                            <div class="row d-flex align-items-center">
                                                <div class="col">
                                                    <div class="input-group">
                                                        <input
                                                            v-model="filtrelemeObjesi.firmaArama"
                                                            type="text"
                                                            class="form-control"
                                                            placeholder="Arama"
                                                            aria-label="Arama"
                                                            aria-describedby="arama"
                                                            @keyup.enter="firmaGrupluIslemleriGetir()"
                                                            @input="gecikmeliFonksiyon.firmaGrupluIslemleriGetir()"
                                                        />
                                                        <span @click="firmaGrupluIslemleriGetir()" class="input-group-text waves-effect" id="arama">
                                                            <i class="mdi mdi-magnify"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                {{-- <div class="col-auto ps-0">
                                                    <!-- Filtreleme butonu -->
                                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#firmaFiltrelemeModal">
                                                        <i class="fa fa-filter"></i>
                                                    </button>
                                                </div> --}}
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <small class="text-muted">
                                                        Firma adı veya sorumlusu, malzeme, işlem...
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3" v-if="yukleniyorObjesi.firmaGrupluIslemler">
                                <div class="col-12 text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3" v-else-if="_.size(aktifForm.firmaGrupluIslemler.data) === 0">
                                <div class="col-12 text-center">
                                    <h4>Forma eklenecek işlem bulunamadı.</h4>
                                </div>
                            </div>
                            <template v-else>
                                <div class="col-12 mt-2">
                                    <div class="table-rep-plugin">
                                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                                            <table id="tech-companies-1" class="table table-striped table-hover table-centered">
                                                <thead>
                                                    <tr>
                                                        <th>İşlem ID</th>
                                                        <th class="text-center">Termin</th>
                                                        <th class="text-center">Resim</th>
                                                        <th>Malzeme</th>
                                                        <th>İşlem</th>
                                                        <th class="text-center">Fırın*</th>
                                                        <th class="text-center">Şarj*</th>
                                                        <th class="text-center">Ekle</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(islem, iIndex) in aktifForm.firmaGrupluIslemler.data" :key="iIndex">
                                                        <td class="kisa-uzunluk">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <span
                                                                        v-if="sorguParametreleri.islemId && sorguParametreleri.islemId === islem.id"
                                                                        class="badge rounded-pill bg-danger"
                                                                    >
                                                                        # @{{ islem.id }}
                                                                    </span>
                                                                    <span v-else># @{{ islem.id }}</span>
                                                                </div>
                                                                <div class="col-12">
                                                                    <small>@{{ islem.firmaAdi }}</small>
                                                                </div>
                                                                <div class="col-12">
                                                                    <span class="badge bg-primary">Sipariş No: @{{ islem.siparisNo }}</span>
                                                                </div>
                                                                <div v-if="islem.tekrarEdenId" class="col-12">
                                                                    <span class="badge rounded-pill bg-danger">Tekrar Eden İşlem ID: @{{ islem.tekrarEdenId }}</span>
                                                                </div>
                                                                <div v-if="islem.tekrarEdilenId" class="col-12">
                                                                    <span class="badge rounded-pill bg-info">Tekrar Edilen İşlem ID: @{{ islem.tekrarEdilenId }}</span>
                                                                </div>
                                                                <div v-if="islem.bolunmusId" class="col-12">
                                                                    <span class="badge rounded-pill bg-warning">
                                                                        Bölünmüş İşlem ID: @{{ islem.bolunmusId }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="kisa-uzunluk text-center">
                                                            <span class="badge badge-pill" :class="`bg-${ islem.gecenSureRenk }`">@{{ islem.gecenSure }} Gün</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <img
                                                                :src="islem.resimYolu ? islem.resimYolu : varsayilanResimYolu"
                                                                class="kg-resim-sec"
                                                                @click.stop="resimOnizlemeAc(islem.resimYolu)"
                                                            />
                                                        </td>
                                                        <td class="uzun-uzunluk">
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
                                                                <div class="col-12">
                                                                    <small class="text-muted"><b>Net: @{{ islem.net }} kg</b></small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="orta-uzunluk">
                                                            <div class="col-12">
                                                                @{{ islem.islemTuruAdi ? islem.islemTuruAdi : "-" }}
                                                            </div>
                                                            <div class="col-12">
                                                                <small>@{{ islem.istenilenSertlik ? islem.istenilenSertlik : "-" }}</small>
                                                            </div>
                                                            <div class="col-12">
                                                                <small>@{{ islem.kalite ? islem.kalite : "-" }}</small>
                                                            </div>
                                                        </td>
                                                        <td class="orta-uzunluk text-center">
                                                            <div class="form-group">
                                                                <select class="form-control select2" v-model="islem.firin" @change="formaEkle(islem)">
                                                                    <optgroup label="Fırınlar">
                                                                        <option
                                                                            v-for="(firin, firinIndex) in firinlar"
                                                                            :value="firin"
                                                                            :key="firinIndex"
                                                                        >
                                                                            @{{ firin.ad }}
                                                                        </option>
                                                                    </optgroup>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td class="kisa-uzunluk text-center">
                                                            <div class="form-group">
                                                                <input
                                                                    v-model="islem.sarj"
                                                                    type="number"
                                                                    class="form-control"
                                                                    placeholder="Şarj (Örn: 1)"
                                                                    @change="formaEkle(islem)"
                                                                />
                                                            </div>
                                                        </td>
                                                        <td class="orta-uzunluk text-center">
                                                            <button
                                                                @click="formaIslemEkleSil(islem)"
                                                                class="btn"
                                                                :class="islem.secildi ? 'btn-success' : 'btn-outline-primary'"
                                                            >
                                                                <i class="fas" :class="islem.secildi ? 'fa-check' : 'fa-plus'"></i>
                                                            </button>
                                                            <button
                                                                @click="islemBolAc(iIndex)"
                                                                class="btn btn-outline-warning p-1"
                                                            >
                                                                <i class="mdi mdi-call-split font-size-20 mx-1"></i>
                                                            </button>

                                                            <!-- Modal -->
                                                            <div class="modal fade" id="islemBolModal" tabindex="-1" aria-labelledby="islemBolModal" aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content" v-if="aktifBolunecekIslem.islem">
                                                                        <div class="modal-header">
                                                                            <div class="row text-start">
                                                                                <div class="col-12">
                                                                                    <h5 class="modal-title" id="exampleModalLabel">
                                                                                        İşlem Bölme
                                                                                    </h5>
                                                                                </div>
                                                                                <div class="col-12">
                                                                                    <small class="text-muted">@{{ aktifBolunecekIslem.islem.malzemeAdi }} (Net: @{{ aktifBolunecekIslem.islem.net }} kg)</small>
                                                                                </div>
                                                                            </div>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body text-start">
                                                                            <div class="row">
                                                                                <div class="col-12 mb-2" v-for="(bIslem, bIndex) in aktifBolunecekIslem.bolunmusIslemler">
                                                                                    <div class="row align-items-end">
                                                                                        <div class="col-auto">
                                                                                            <div class="form-check">
                                                                                                <input v-model="bIslem.sabit" class="form-check-input" type="checkbox" :id="'flexCheckChecked' + bIndex">
                                                                                                <label class="form-check-label" :for="'flexCheckChecked' + bIndex">
                                                                                                    Sabitle
                                                                                                </label>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col">
                                                                                            <div class="form-group">
                                                                                                <label for="">@{{ bIndex + 1 }}. Net KG (%@{{ _.round(bIslem.yuzde, 2) }})</label>
                                                                                                <input
                                                                                                    v-model="bIslem.net"
                                                                                                    @input="bolunmusIslemNetDegisti(bIslem)"
                                                                                                    type="number"
                                                                                                    class="form-control"
                                                                                                    placeholder="İşlem Adedi"
                                                                                                />
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-auto">
                                                                                            <button
                                                                                                @click="islemBolIslemKaldir(bIndex)"
                                                                                                class="btn btn-outline-danger"
                                                                                                v-if="bIslem.yeniIslem"
                                                                                            >
                                                                                                <i class="mdi mdi-delete"></i>
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-12 mt-1">
                                                                                    <div class="d-grid gap-2">
                                                                                        <button @click="islemBolIslemEkle()" class="btn btn-sm btn-outline-primary" type="button">
                                                                                            <i class="fas fa-plus"></i>
                                                                                            Böl
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <template v-if="_.size(aktifBolunecekIslem.hatalar)">
                                                                                <div class="col-12 text-start" v-for="(hata, hKey, hIndex) in aktifBolunecekIslem.hatalar" :key="hIndex">
                                                                                    <small class="text-danger">
                                                                                        <i class="mdi mdi-info-outline text-danger"></i>
                                                                                        @{{ hIndex + 1 }}) @{{ hata }}
                                                                                    </small>
                                                                                </div>
                                                                            </template>
                                                                            <div class="form-check">
                                                                                <input v-model="aktifBolunecekIslem.kaydettiktenSonraSec" class="form-check-input" type="checkbox" id="kaydettiktenSonraSec">
                                                                                <label class="form-check-label" for="kaydettiktenSonraSec">
                                                                                    <small>
                                                                                        Kaydettikten sonra işlemleri seç
                                                                                    </small>
                                                                                </label>
                                                                            </div>
                                                                            <button
                                                                                type="button"
                                                                                class="btn btn-primary"
                                                                                :disabled="_.size(aktifBolunecekIslem.hatalar) !== 0 || yukleniyorObjesi.islemBol"
                                                                                @click="islemBolKaydet()"
                                                                            >
                                                                                <template v-if="yukleniyorObjesi.islemBol">
                                                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                                                    Kaydediliyor...
                                                                                </template>
                                                                                <template v-else>
                                                                                    İşlemleri Kaydet
                                                                                </template>
                                                                            </button>
                                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="row d-flex align-items-center justify-content-between">
                                        <div class="col-auto"></div>
                                        <div class="col">
                                            <ul class="pagination pagination-rounded justify-content-center mb-0">
                                                <li class="page-item">
                                                    <button class="page-link" :disabled="!aktifForm.firmaGrupluIslemler.prev_page_url" @click="firmaGrupluIslemleriGetir(null, aktifForm.firmaGrupluIslemler.prev_page_url)">
                                                        <i class="fas fa-angle-left"></i>
                                                    </button>
                                                </li>
                                                <li
                                                    v-for="sayfa in sayfalamaAyarla(aktifForm.firmaGrupluIslemler.last_page, aktifForm.firmaGrupluIslemler.current_page)"
                                                    class="page-item"
                                                    :class="[sayfa.aktif ? 'active' : '']"
                                                >
                                                    <button class="page-link" @click="sayfa.tur === 'SAYFA' ? firmaGrupluIslemleriGetir(null, `{{ route("firmaGrupluIslemleriGetir") }}?page=` + sayfa.sayfa) : ()  => {}">@{{ sayfa.sayfa }}</button>
                                                </li>
                                                <li class="page-item">
                                                    <button class="page-link" :disabled="!aktifForm.firmaGrupluIslemler.next_page_url" @click="firmaGrupluIslemleriGetir(null, aktifForm.firmaGrupluIslemler.next_page_url)">
                                                        <i class="fas fa-angle-right"></i>
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-auto">
                                            <small class="text-muted">Toplam Kayıt: @{{ aktifForm.firmaGrupluIslemler.total }}</small>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
                <template v-else-if="aktifSayfa.kod === 'FORM_GORUNUMU'">
                    <div class="row">
                        <div class="col-12 col-sm-8">
                            <div class="d-flex flex-row align-items-center">
                                <button @click="geriYeniForm" class="btn btn-warning"><i class="fas fa-arrow-left"></i> GERİ</button>
                                <h4 class="card-title m-0 ms-2 text-nowrap text-truncate">
                                    @{{ aktifForm.formAdi }}
                                    <div class="col-12" v-if="aktifForm.duzenleyen">
                                        <span class="badge badge-pill bg-success">Son Düzenleyen: @{{ aktifForm.duzenleyen }}</span>
                                    </div>
                                    <div class="d-inline-flex" v-if="araYukleniyor">
                                        <div class="spinner-grow text-primary m-1 spinner-grow-sm" role="status">
                                            <span class="sr-only">Yükleniyor...</span>
                                        </div>
                                    </div>
                                </h4>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 text-end">
                            @can("isil_islem_formu_duzenleme")
                                <button @click="moduDegistir" class="btn btn-outline-info">
                                    <i class="fas fa-eye" v-if="!aktifForm.onizlemeModu"></i>
                                    <i class="fas fa-eye-slash" v-else></i>
                                </button>
                            @endcan
                            <button @click="ciktiAl()" class="btn btn-primary">
                                <i class="fas fa-file-export"></i>
                                ÇIKTI
                            </button>
                            <button @click="ciktiAl(undefined, 'EXCEL')" class="btn btn-secondary" v-if="!isNativeApp">
                                <i class="fas fa-file-excel"></i>
                                EXCEL
                            </button>
                            @canany(["isil_islem_formu_duzenleme", "isil_islem_formu_kaydetme"])
                                <button
                                    @click="formKaydet"
                                    class="btn btn-success"
                                    {{-- v-if="!aktifForm.detayGoruntule" --}}
                                >
                                    <i class="fas fa-save"></i>
                                    KAYDET
                                </button>
                            @endcan
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="table-rep-plugin">
                                <div class="table-responsive mb-0" data-pattern="priority-columns">
                                    <table id="formGorunumu" ref="formGorunumu" class="table">
                                        <thead>
                                            <tr>
                                                <th>Fırın</th>
                                                <th>Şarj</th>
                                                <th class="text-center">Resim</th>
                                                <th>Firma</th>
                                                <th>Malzeme</th>
                                                <th>İst. Sertlik</th>
                                                <th>Kalite</th>
                                                <th>Sıcaklık</th>
                                                <th>Carbon</th>
                                                <th>Süre</th>
                                                <th>Ç. Sertliği</th>
                                                <th>Men. Sıcaklığı</th>
                                                <th>Süre</th>
                                                <th>Son Sertlik</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-for="(firin, firinId, firinIndex) in aktifForm.firinSarjGrupluIslemler">
                                                <template v-for="(sarj, sarjId, sarjIndex) in firin.sarjlar">
                                                    <template v-for="(islem, islemIndex) in sarj.islemler">
                                                        <tr
                                                            :key="firinId + '-' + sarjId + '-' + islemIndex"
                                                            :id="firinId"
                                                        >
                                                            <td
                                                                :style="islemIndex === (_.size(sarj.islemler) - 1) || sarjIndex === 0 ? 'border-bottom: 2px solid black' : ''"
                                                                class="dikey"
                                                                :rowspan="firin.toplamIslemSayisi"
                                                                v-if="sarjIndex === 0 && islemIndex === 0"
                                                            >
                                                                <span>@{{ firin.firinAdi }}</span>
                                                            </td>
                                                            <td
                                                                :style="islemIndex === (_.size(sarj.islemler) - 1) || sarjIndex === 0 ? 'border-bottom: 2px solid black' : ''"
                                                                class="dikey"
                                                                :rowspan="sarj.toplamIslemSayisi"
                                                                v-if="islemIndex === 0"
                                                            >
                                                                <span>@{{ sarj.sarj }}. Şarj</span>
                                                            </td>
                                                            <td :style="islemIndex === (_.size(sarj.islemler) - 1) ? 'border-bottom: 2px solid black' : ''" class="kisa-uzunluk text-center">
                                                                <img
                                                                    :src="islem.resimYolu ? islem.resimYolu : varsayilanResimYolu"
                                                                    class="kg-resim-sec"
                                                                    @click.stop="resimOnizlemeAc(islem.resimYolu)"
                                                                />
                                                            </td>
                                                            <td :style="islemIndex === (_.size(sarj.islemler) - 1) ? 'border-bottom: 2px solid black' : ''" class="orta-uzunluk align-left">@{{ islem.firmaAdi }}</td>
                                                            <td :style="islemIndex === (_.size(sarj.islemler) - 1) ? 'border-bottom: 2px solid black' : ''" class="kisa-uzunluk align-left">@{{ islem.malzemeAdi }}</td>
                                                            <td :style="islemIndex === (_.size(sarj.islemler) - 1) ? 'border-bottom: 2px solid black' : ''" class="kisa-uzunluk">
                                                                <template v-if="aktifForm.onizlemeModu">
                                                                    <span>@{{ islem.istenilenSertlik }}</span>
                                                                </template>
                                                                <template v-else>
                                                                    <input
                                                                        v-model="islem.istenilenSertlik"
                                                                        type="text"
                                                                        class="form-control"
                                                                        placeholder="İstenilen Sertlik"
                                                                    />
                                                                </template>
                                                            </td>
                                                            <td :style="islemIndex === (_.size(sarj.islemler) - 1) ? 'border-bottom: 2px solid black' : ''" class="kisa-uzunluk">
                                                                <template v-if="aktifForm.onizlemeModu">
                                                                    <span>@{{ islem.kalite }}</span>
                                                                </template>
                                                                <template v-else>
                                                                    <input
                                                                        v-model="islem.kalite"
                                                                        type="text"
                                                                        class="form-control"
                                                                        placeholder="Kalite"
                                                                    />
                                                                </template>
                                                            </td>
                                                            <td
                                                                :style="islemIndex === (_.size(sarj.islemler) - 1) || sarjIndex === 0 ? 'border-bottom: 2px solid black' : ''"
                                                                :rowspan="sarj.toplamIslemSayisi"
                                                                v-if="islemIndex === 0"
                                                                class="kisa-uzunluk"
                                                            >
                                                                <template v-if="aktifForm.onizlemeModu">
                                                                    <span>@{{ islem.sicaklik }}</span>
                                                                </template>
                                                                <template v-else>
                                                                    <input
                                                                        v-model="islem.sicaklik"
                                                                        type="text"
                                                                        class="form-control"
                                                                        placeholder="Sıcaklık"
                                                                    />
                                                                </template>
                                                            </td>
                                                            <td
                                                                :style="islemIndex === (_.size(sarj.islemler) - 1) || sarjIndex === 0 ? 'border-bottom: 2px solid black' : ''"
                                                                :rowspan="sarj.toplamIslemSayisi"
                                                                v-if="islemIndex === 0"
                                                                class="kisa-uzunluk"
                                                            >
                                                                <template v-if="aktifForm.onizlemeModu">
                                                                    <span>@{{ islem.carbon }}</span>
                                                                </template>
                                                                <template v-else>
                                                                    <input
                                                                        v-model="islem.carbon"
                                                                        type="number"
                                                                        class="form-control"
                                                                        placeholder="Carbon"
                                                                    />
                                                                </template>
                                                            </td>
                                                            <td
                                                                :style="islemIndex === (_.size(sarj.islemler) - 1) || sarjIndex === 0 ? 'border-bottom: 2px solid black' : ''"
                                                                :rowspan="sarj.toplamIslemSayisi"
                                                                v-if="islemIndex === 0"
                                                                class="kisa-uzunluk"
                                                            >
                                                                <template v-if="aktifForm.onizlemeModu">
                                                                    <span>@{{ islem.beklenenSure }}</span>
                                                                </template>
                                                                <template v-else>
                                                                    <input
                                                                        v-model="islem.beklenenSure"
                                                                        type="number"
                                                                        class="form-control"
                                                                        placeholder="Süre"
                                                                    />
                                                                </template>
                                                            </td>
                                                            <td :style="islemIndex === (_.size(sarj.islemler) - 1) ? 'border-bottom: 2px solid black' : ''" class="kisa-uzunluk">
                                                                <template v-if="aktifForm.onizlemeModu">
                                                                    <span>@{{ islem.cikisSertligi }}</span>
                                                                </template>
                                                                <template v-else>
                                                                    <input
                                                                        v-model="islem.cikisSertligi"
                                                                        type="text"
                                                                        class="form-control"
                                                                        placeholder="Ç. Sertliği"
                                                                    />
                                                                </template>
                                                            </td>
                                                            <td :style="islemIndex === (_.size(sarj.islemler) - 1) ? 'border-bottom: 2px solid black' : ''" class="kisa-uzunluk">
                                                                <template v-if="aktifForm.onizlemeModu">
                                                                    <span>@{{ islem.menevisSicakligi }}</span>
                                                                </template>
                                                                <template v-else>
                                                                    <input
                                                                        v-model="islem.menevisSicakligi"
                                                                        type="text"
                                                                        class="form-control"
                                                                        placeholder="Meneviş Sıcaklığı"
                                                                    />
                                                                </template>
                                                            </td>
                                                            <td :style="islemIndex === (_.size(sarj.islemler) - 1) ? 'border-bottom: 2px solid black' : ''" class="kisa-uzunluk">
                                                                <template v-if="aktifForm.onizlemeModu">
                                                                    <span>@{{ islem.cikisSuresi }}</span>
                                                                </template>
                                                                <template v-else>
                                                                    <input
                                                                        v-model="islem.cikisSuresi"
                                                                        type="number"
                                                                        class="form-control"
                                                                        placeholder="Süre"
                                                                    />
                                                                </template>
                                                            </td>
                                                            <td :style="islemIndex === (_.size(sarj.islemler) - 1) ? 'border-bottom: 2px solid black' : ''" class="kisa-uzunluk">
                                                                <template v-if="aktifForm.onizlemeModu">
                                                                    <span>@{{ islem.sonSertlik }}</span>
                                                                </template>
                                                                <template v-else>
                                                                    <input
                                                                        v-model="islem.sonSertlik"
                                                                        type="text"
                                                                        class="form-control"
                                                                        placeholder="Son Sertlik"
                                                                    />
                                                                </template>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </template>
                                                {{-- <tr>
                                                    <td colspan="100%" class="px-0">
                                                        <hr class="m-0 text-dark" />
                                                    </td>
                                                </tr> --}}
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
<script>
    let mixinApp = {
        data() {
            return {
                aktifSayfa: {
                    kod: "ANASAYFA",
                    baslik: "Formlar",
                },
                sayfalar: [
                    {
                        kod: "ANASAYFA",
                        baslik: "Formlar",
                    },
                    {
                        kod: "YENI_FORM",
                        baslik: "Form Oluştur",
                    },
                    {
                        kod: "FORM_GORUNUMU",
                        baslik: "Form Görünümü",
                    },
                ],
                formlar: {},
                aktifForm: null,
                yukleniyorObjesi: {
                    takipNo: false,
                    firmaGrupluIslemler: false,
                    firinlar: false,
                    form: false,
                    islemBol: false,
                },
                firinlar: [],
                sorguParametreleri: {
                    formId: null,
                    islemNo: null,
                },
                filtrelemeObjesi: {
                    arama: "",
                    baslangicTarihi: null,
                    bitisTarihi: null,
                    takipNo: '',
                    formAdi: '',
                    firmaArama: "",
                },
                sayfalamaSayilari: [10, 25, 50, 100],
                sayfalamaSayisi: 10,
                aktifBolunecekIslem: {
                    islem: null,
                    bolunmusIslemler: [],
                    hatalar: {},
                    kaydettiktenSonraSec: true,
                    modal: null,
                    firma: null,
                },
            }
        },
        mounted() {
            this.onyukleme();
            // this.formlariGetir();
            // this.formEkleAc();
            // this.formHazirla();
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
            onyukleme() {
                let url = new URL(window.location.href);
                this.sorguParametreleri.formId = _.toNumber(url.searchParams.get("formId"));
                this.sorguParametreleri.firinId = _.toNumber(url.searchParams.get("firinId"));
                this.sorguParametreleri.sarj = _.toNumber(url.searchParams.get("sarj"));

                if (this.sorguParametreleri.formId) {
                    this.filtrelemeObjesi.formId = this.sorguParametreleri.formId;
                }

                this.gecikmeliFonksiyonCalistir(this.filtrele);
                this.gecikmeliFonksiyonCalistir(this.firmaGrupluIslemleriGetir, {
                    fonksiyonKey: "firmaGrupluIslemleriGetir",
                });

                this.formlariGetir();
            },
            formlariGetir(url = "/formlar") {
                this.yukleniyorObjesi.form = true;
                axios.get(url, {
                    params: {
                        filtreleme: this.filtrelemeObjesi,
                        sayfalamaSayisi: this.sayfalamaSayisi,
                    }
                })
                .then(async response => {
                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    this.formlar = response.data.formlar;

                    if (this.sorguParametreleri.formId) {
                        await this.formIslemleriGetir(this.formlar.data[0].id, 0);

                        try {
                            setTimeout(() => {
                                if (this.sorguParametreleri.sarj && this.sorguParametreleri.firinId) {
                                    const ref = this.$refs['firin' + this.sorguParametreleri.firinId + 'sarj' + this.sorguParametreleri.sarj];
                                    const firinSarjRef = ref && ref[0]
                                        ? ref[0]
                                        : ref;

                                    if (ref && firinSarjRef) {
                                        firinSarjRef.scrollIntoView({
                                            behavior: 'smooth',
                                            block: 'start',
                                        });
                                    }
                                }
                                else if (this.sorguParametreleri.firinId) {
                                    const ref = this.$refs['firin' + this.sorguParametreleri.firinId];
                                    const firinRef = ref && ref[0]
                                        ? ref[0]
                                        : ref;

                                    if (ref && firinRef) {
                                        firinRef.scrollIntoView({
                                            behavior: 'smooth',
                                            block: 'start',
                                        });
                                    }
                                }
                            }, 500);
                        }
                        catch (e) {
                            console.log(e);
                        }
                    }

                    this.yukleniyorObjesi.form = false;
                }).catch(error => {
                    this.yukleniyorObjesi.form = false;
                    this.uyariAc({
                        baslik: 'Hata',
                        mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                        tur: "error"
                    });
                    console.log(error);
                });
            },
            aktifSayfaDegistir(kod) {
                this.aktifSayfa = _.find(this.sayfalar, { kod });
            },
            formEkleAc() {
                this.aktifForm = {
                    formAdi: '',
                    takipNo: '',
                    baslangicTarihi: this.m().format("YYYY-MM-DD"),
                    bitisTarihi: null,
                    aciklama: "",
                    firmaGrupluIslemler: [],
                    secilenIslemler: [],
                    firinSarjGrupluIslemler: {},
                };

                if (!_.size(this.firinlar)) {
                    this.firinlariGetir();
                }

                this.takipNumarasiGetir();
                this.firmaGrupluIslemleriGetir();
                this.aktifSayfaDegistir("YENI_FORM");
            },
            firinlariGetir() {
                this.yukleniyorObjesi.firinlar = true;
                return axios.get('{{ route('firinlariGetir') }}')
                    .then(response => {
                        this.yukleniyorObjesi.firinlar = false;
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
                        this.yukleniyorObjesi.firinlar = false;
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                        console.log(error);
                    });
            },
            takipNumarasiGetir() {
                this.yukleniyorObjesi.takipNo = true;
                axios.get("/takipNumarasiGetir")
                .then(response => {
                    this.yukleniyorObjesi.takipNo = false;
                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    this.aktifForm.takipNo = response.data.takipNo;
                    this.formAdiOlustur();

                    this.aktifForm = _.cloneDeep(this.aktifForm);
                })
                .catch(error => {
                    this.yukleniyorObjesi.takipNo = false;
                    this.uyariAc({
                        baslik: 'Hata',
                        mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                        tur: "error"
                    });
                    console.log(error);
                });
            },
            formAdiOlustur() {
                if (this.aktifForm.formAdi) {
                    return;
                }

                this.aktifForm.formAdi = this.aktifForm.takipNo + " - Isıl İşlem Formu";
            },
            geriAnasayfa() {
                this.aktifSayfaDegistir("ANASAYFA");

                this.aktifForm = null;

                this.filtrelemeObjesi = {
                    arama: "",
                    baslangicTarihi: null,
                    bitisTarihi: null,
                    takipNo: '',
                    formAdi: '',
                    firmaArama: "",
                }
            },
            geriYeniForm() {
                if (this.aktifForm.geriFonksiyon) {
                    return this.aktifForm.geriFonksiyon();
                }

                this.aktifSayfaDegistir("YENI_FORM");
            },
            firmaGrupluIslemleriGetir(formId = null, url = "/firmaGrupluIslemleriGetir") {
                this.yukleniyorObjesi.firmaGrupluIslemler = true;
                return axios.get(url, {
                    params: {
                        formId: formId || this.aktifForm.id,
                        filtreleme: {
                            arama: this.filtrelemeObjesi.firmaArama,
                        }
                    }
                })
                .then(response => {
                    this.yukleniyorObjesi.firmaGrupluIslemler = false;
                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    this.aktifForm.firmaGrupluIslemler = _.cloneDeep(response.data.firmaGrupluIslemler);

                    if (this.aktifForm.id && _.size(this.aktifForm.firmaGrupluIslemler.data)) {
                        this.aktifForm.secilenIslemler = _.unionBy(response.data.secilenIslemler, this.aktifForm.secilenIslemler, "id");

                        for (const [index, islem] of _.toPairs(this.aktifForm.secilenIslemler)) {
                            const _islem = _.find(this.aktifForm.firmaGrupluIslemler.data, { id: islem.id });
                            if (_islem) {
                                _islem.firin = islem.firin && islem.firin.id ? _.find(this.firinlar, { id: islem.firin.id }) : null;
                                _islem.sarj = islem.sarj;
                                _islem.secildi = true;
                            }

                            this.formaEkle(islem, { cloneYap: false });
                        }

                        this.aktifForm = _.cloneDeep(this.aktifForm);
                    }
                })
                .catch(error => {
                    this.yukleniyorObjesi.firmaGrupluIslemler = false;
                    this.uyariAc({
                        baslik: 'Hata',
                        mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                        tur: "error"
                    });
                    console.log(error);
                });
            },
            formaIslemEkleSil(islem) {
                islem.secildi = !islem.secildi;

                const islemIndex = _.findIndex(this.aktifForm.secilenIslemler, { id: islem.id });
                if (islemIndex > -1) {
                    this.aktifForm.secilenIslemler.splice(islemIndex, 1);

                    // Eğer düzenleme modundaysa silineceklere ekle
                    if (this.aktifForm.id && _.find(this.aktifForm.baslangictakiIslemler, { id: islem.id })) {
                        this.aktifForm.silinecekIslemler.push(islem.id);
                    }
                } else {
                    this.aktifForm.secilenIslemler.push(islem);

                    // Eğer düzenleme modundaysa silineceklerde varsa kaldır
                    if (this.aktifForm.id && _.includes(this.aktifForm.silinecekIslemler, islem.id)) {
                        this.aktifForm.silinecekIslemler = _.without(this.aktifForm.silinecekIslemler, islem.id);
                    }
                }

                this.aktifForm = _.cloneDeep(this.aktifForm);
            },
            formaEkle(islem, p = {}) {
                const cloneYap = "cloneYap" in p ? p.cloneYap : true;

                const islemIndex = _.findIndex(this.aktifForm.firmaGrupluIslemler.data, { id: islem.id });
                if (islemIndex > -1) {
                    islem = this.aktifForm.firmaGrupluIslemler.data[islemIndex];
                    this.aktifForm.firmaGrupluIslemler.data[islemIndex].secildi = true;
                }

                const secilenIndex = _.findIndex(this.aktifForm.secilenIslemler, { id: islem.id });
                if (secilenIndex === -1) {
                    this.aktifForm.secilenIslemler.push(islem);
                }
                else {
                    this.aktifForm.secilenIslemler[secilenIndex] = islem;
                }

                if (cloneYap) {
                    this.aktifForm = _.cloneDeep(this.aktifForm);
                }
            },
            formHazirla() {
                if (!_.size(this.aktifForm.secilenIslemler)) {
                    return this.uyariAc({
                        baslik: 'Hata',
                        mesaj: 'Lütfen en az bir işlem seçiniz.',
                        tur: "error"
                    });
                }

                this.aktifForm.onizlemeModu = false;

                this.aktifForm.firinSarjGrupluIslemler = {};

                let oncekiFirinId = null;

                for (const [index, islem] of _.toPairs(this.aktifForm.secilenIslemler)) {
                    if (!islem.firin) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: `Formda görmek istediğiniz, ${ islem.siparisNo } sipariş numaralı ${ islem.malzemeAdi } malzemesi için bir fırın seçmelisiniz.`,
                            tur: "error"
                        });
                    }

                    if (!islem.sarj) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: `Formda görmek istediğiniz, ${ islem.siparisNo } sipariş numaralı ${ islem.malzemeAdi } malzemesi için geçerli bir sarj seçmelisiniz.`,
                            tur: "error"
                        });
                    }

                    const firinId = islem.firin.id;
                    const sarjId = islem.sarj;

                    if (!this.aktifForm.firinSarjGrupluIslemler[firinId]) {
                        this.aktifForm.firinSarjGrupluIslemler[firinId] = {
                            firinId,
                            firinAdi: islem.firin.ad,
                            toplamIslemSayisi: 0,
                            sarjlar: {},
                        };
                    }

                    if (!this.aktifForm.firinSarjGrupluIslemler[firinId].sarjlar[sarjId]) {
                        this.aktifForm.firinSarjGrupluIslemler[firinId].sarjlar[sarjId] = {
                            sarj: islem.sarj,
                            toplamIslemSayisi: 0,
                            islemler: [],
                        };
                    }

                    this.aktifForm.firinSarjGrupluIslemler[firinId].sarjlar[sarjId].islemler.push(islem);

                    this.aktifForm.firinSarjGrupluIslemler[firinId].sarjlar[sarjId].toplamIslemSayisi++;
                    this.aktifForm.firinSarjGrupluIslemler[firinId].toplamIslemSayisi++;
                }

                this.aktifSayfaDegistir("FORM_GORUNUMU");
            },
            ciktiAl(id = "formGorunumu", tur = "RESIM") {
                const baslangicDurum = !!this.aktifForm.onizlemeModu;

                this.aktifForm.onizlemeModu = true;
                this.aktifForm = _.cloneDeep(this.aktifForm);

                this.$nextTick(() => {
                    if (tur === 'EXCEL') {
                        const exportToExcel = (type = "xlsx") => {
                            var elt = document.getElementById(id);
                            elt = this.$refs.formGorunumu;
                            var wb = XLSX.utils.table_to_book(elt);
                            wb.Sheets["Sheet1"];
                            return XLSX.writeFile(wb, this.aktifForm.formAdi + '.' + type);
                        }

                        exportToExcel();

                        this.aktifForm.onizlemeModu = baslangicDurum;
                    }
                    else {
                        html2canvas(document.getElementById(id)).then(canvas => {
                            const uzanti = "png";
                            const base64 = canvas.toDataURL('image/png');
                            const link = document.createElement('a');
                            link.href = base64;
                            link.download = this.aktifForm.formAdi + '.' + uzanti;
                            link.click();

                            if (this.isNativeApp) {
                                window.ReactNativeWebView.postMessage(JSON.stringify({
                                    kod: "INDIR",
                                    dosya: base64,
                                    dosyaAdi: this.aktifForm.formAdi,
                                    dosyaUzantisi: uzanti,
                                }));
                            }

                            this.aktifForm.onizlemeModu = baslangicDurum;
                        });
                    }
                });
            },
            moduDegistir() {
                this.aktifForm.onizlemeModu = !this.aktifForm.onizlemeModu;

                this.aktifForm = _.cloneDeep(this.aktifForm);
            },
            formKaydet() {
                const islem = () => {
                    this.yukleniyorObjesi.form = true;
                    axios.post("{{ route('formKaydet') }}", {
                        form: this.aktifForm,
                        silinecekIslemler: this.aktifForm.silinecekIslemler,
                    })
                    .then(response => {
                        this.yukleniyorObjesi.form = false;

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

                        this.formlariGetir();
                        this.geriAnasayfa();
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.form = false;
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                        console.log(error);
                    });
                };

                const silinecekIslemlerToplam = _.size(this.aktifForm.silinecekIslemler);
                if (silinecekIslemlerToplam) {
                    Swal.fire({
                        title: "Uyarı",
                        text: `Eğer devam ederseniz, ${silinecekIslemlerToplam} adet işlem kaldırılıcak. Devam etmek istiyor musunuz?`,
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
            async formDuzenle(form) {
                this.yukleniyorObjesi.form = true;

                this.aktifForm = {
                    formAdi: '',
                    takipNo: '',
                    baslangicTarihi: this.m().format("YYYY-MM-DD"),
                    bitisTarihi: null,
                    aciklama: "",
                    firmaGrupluIslemler: [],
                    secilenIslemler: [],
                    firinSarjGrupluIslemler: {},
                    silinecekIslemler: [],
                    baslangictakiIslemler: [],
                    ...form,
                };

                if (!_.size(this.firinlar)) {
                    await this.firinlariGetir();
                }

                await this.firmaGrupluIslemleriGetir(this.aktifForm.id);

                this.aktifForm.baslangictakiIslemler = _.cloneDeep(this.aktifForm.secilenIslemler);

                this.yukleniyorObjesi.form = false;

                this.aktifSayfaDegistir("YENI_FORM");
            },
            formSil(form) {
                const islem = () => {
                    this.yukleniyorObjesi.form = true;

                    axios.post("{{ route('formSil') }}", {
                        formId: form.id,
                    })
                    .then(response => {
                        this.yukleniyorObjesi.form = false;

                        if (!response.data.durum) {
                            return this.uyariAc({
                                baslik: 'Hata',
                                mesaj: response.data.mesaj,
                                tur: "error"
                            });
                        }

                        this.uyariAc({
                            baslik: 'Başarılı',
                            mesaj: 'Form başarıyla silindi.',
                            tur: "success"
                        });

                        this.formlariGetir();
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.form = false;
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                        console.log(error);
                    });
                };

                Swal.fire({
                    title: "Uyarı",
                    text: `Bu forma ait durumu, "İşlemde" veya "Tamamlandı" olan işlem varsa formu silemezsiniz.
                        Formu sildiğinizde forma ait tüm işlemler "Başlanmadı" olarak ayarlanacaktır.
                        Formu silmek istediğinizden emin misiniz?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sil',
                    cancelButtonText: 'İptal',
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        islem();
                    }
                });
            },
            async formDetayGoruntule(form) {
                await this.formDuzenle(form);
                console.log(this.aktifForm.baslangictakiIslemler);

                this.formHazirla();
                this.aktifForm.onizlemeModu = true;
                this.aktifForm.detayGoruntule = true;
                this.aktifForm.geriFonksiyon = () => {
                    this.geriAnasayfa();
                };

                this.aktifForm = _.cloneDeep(this.aktifForm);
            },
            sorguParametreleriTemizle() {
                this.sorguParametreleri = {
                    formId: null,
                    firinId: null,
                    sarjId: null,
                };

                delete this.filtrelemeObjesi.formId;

                window.history.replaceState({}, document.title, (new URL(window.location.href)).pathname)

                this.formlariGetir();
            },
            filtrele() {
                this.formlariGetir();
            },
            filtrelemeTarihTemizle() {
                this.filtrelemeObjesi.baslangicTarihi = null;
                this.filtrelemeObjesi.bitisTarihi = null;
            },
            formIslemleriGetir(formId, formIndex, acikTut = false) {
                if (this.formlar.data[formIndex].islemlerYukleniyor || (!acikTut && this.formlar.data[formIndex].islemlerAcik)) {
                    this.formlar.data[formIndex].islemlerAcik = !this.formlar.data[formIndex].islemlerAcik;
                    return;
                }
                this.formlar.data[formIndex].islemYukleniyor = true;
                this.formlar = _.cloneDeep(this.formlar);
                return axios.get("/firinSarjGrupluIslemleriGetir", {
                    params: {
                        formId,
                    }
                }).then(response => {
                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    this.formlar.data[formIndex].islemler = response.data.firinSarjGrupluIslemler;

                    this.formlar.data[formIndex].islemYukleniyor = false;
                    this.formlar.data[formIndex].islemlerAcik = true;
                    this.formlar = _.cloneDeep(this.formlar);
                }).catch(error => {
                    this.formlar.data[formIndex].islemYukleniyor = false;
                    this.uyariAc({
                        baslik: 'Hata',
                        mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                        tur: "error"
                    });
                    console.log(error);
                    this.formlar = _.cloneDeep(this.formlar);
                });
            },
            sarjIslemleriBaslat(firin, sarj, formId, formIndex) {
                axios.post("/sarjIslemleriBaslat", {
                    firin,
                    formId,
                    sarj,
                }).then(response => {
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

                    this.formIslemleriGetir(formId, formIndex, true);
                }).catch(error => {
                    this.uyariAc({
                        baslik: 'Hata',
                        mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                        tur: "error"
                    });
                    console.log(error);
                });
            },
            sarjIslemleriTamamla(firin, sarj, formId, formIndex) {
                axios.post("/sarjIslemleriTamamla", {
                    firin,
                    formId,
                    sarj,
                }).then(response => {
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

                    this.formIslemleriGetir(formId, formIndex, true);
                }).catch(error => {
                    this.uyariAc({
                        baslik: 'Hata',
                        mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                        tur: "error"
                    });
                    console.log(error);
                });
            },
            islemBaslat(islem, formIndex) {
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

                    this.formIslemleriGetir(islem.formId, formIndex, true);
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
            islemTamamla(islem, formIndex) {
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

                    // this.isilIslemleriGetir();
                    this.formIslemleriGetir(islem.formId, formIndex, true);
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
            islemTekrar(islem, formIndex) {
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

                        // this.isilIslemleriGetir();
                        this.formIslemleriGetir(islem.formId, formIndex, true);
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
                    text: "İşlem tekrardan başlatılacaktır. Lütfen işlemi tekrar etme sebebini giriniz.",
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
                // window.location.href = "{{ route('isil-islemler') }}?islemId=" + islem.id + "&formId=" + islem.formId;
            },
            islemTamamlandiGeriAl(islem, formIndex) {
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

                    // this.isilIslemleriGetir();
                    this.formIslemleriGetir(islem.formId, formIndex, true);
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
            goto(refName, container = window) {
                var element = this.$refs[refName];
                var top = element.offsetTop;

                container.scrollTo(0, top);
            },
            islemBolAc(islemIndex) {
                const islem = _.cloneDeep(this.aktifForm.firmaGrupluIslemler.data[islemIndex]);
                this.aktifBolunecekIslem = {
                    islem: {
                        ...islem,
                    },
                    bolunmusIslemler: [],
                    hatalar: {},
                    kaydettiktenSonraSec: true,
                    modal: new bootstrap.Modal(document.getElementById("islemBolModal")),
                };

                this.aktifBolunecekIslem.modal.show();

                this.islemBolIslemEkle(islem);
            },
            islemBolIslemEkle(islem = null) {
                const _islem = { ...(islem || this.aktifBolunecekIslem.islem) };

                if (!islem) {
                    _islem.net = 0;
                    _islem.yeniIslem = true;
                }

                _islem.yuzde = (_islem.net / this.aktifBolunecekIslem.islem.net) * 100;

                this.aktifBolunecekIslem.bolunmusIslemler.push(_islem);

                this.bolunmusIslemNetDegisti(_.cloneDeep(_islem));
            },
            islemBolIslemKaldir(index) {
                const islem = _.cloneDeep(this.aktifBolunecekIslem.bolunmusIslemler[index]);
                this.aktifBolunecekIslem.bolunmusIslemler.splice(index, 1);
                this.bolunmusIslemNetDegisti(islem);
            },
            bolunmusIslemNetDegisti(islem) {
                const aktifDegisen = _.find(this.aktifBolunecekIslem.bolunmusIslemler, { aktifDegisen: true });
                if (aktifDegisen && aktifDegisen.id !== islem.id) {
                    delete aktifDegisen.aktifDegisen;
                }

                islem.aktifDegisen = true;
                islem.sabit = true;

                const [sabitOlanlar, sabitOlmayanlar] = _.partition(this.aktifBolunecekIslem.bolunmusIslemler, {
                    sabit: true,
                });

                let toplamNet = this.aktifBolunecekIslem.islem.net;
                let sabitOlmayanIslemSayisi = _.size(sabitOlmayanlar);

                _.forEach(sabitOlanlar, (sabitOlan) => {
                    toplamNet -= sabitOlan.net;

                    sabitOlan.yuzde = (sabitOlan.net / this.aktifBolunecekIslem.islem.net) * 100;
                })

                const net = _.floor(toplamNet / sabitOlmayanIslemSayisi);
                const kusuratNet = toplamNet - (net * sabitOlmayanIslemSayisi);

                if (net === 0) {
                    this.aktifBolunecekIslem.hatalar.sayiNegatif = "Bir işlemin toplam net ağırlığı sıfır (0) olamaz.";
                }
                else if (net < 0) {
                    this.aktifBolunecekIslem.hatalar.sayiNegatif = "Toplam net ağırlıktan yüksek değer girdiniz.";
                }
                else {
                    delete this.aktifBolunecekIslem.hatalar.sayiNegatif;
                }

                _.forEach(sabitOlmayanlar, (sabitOlmayan, index) => {
                    sabitOlmayan.net = net;

                    if (sabitOlmayanIslemSayisi === (index + 1)) {
                        sabitOlmayan.net += kusuratNet;
                    }

                    sabitOlmayan.yuzde = sabitOlmayan.net / this.aktifBolunecekIslem.islem.net * 100;
                })

                let _toplamNet = 0;
                let sifirdanKucukKayitVar = false;
                _.forEach(this.aktifBolunecekIslem.bolunmusIslemler, (bolunmusIslem) => {
                    _toplamNet += _.toNumber(bolunmusIslem.net);

                    if (bolunmusIslem.net <= 0) {
                        sifirdanKucukKayitVar = true;
                    }
                });

                if (sifirdanKucukKayitVar) {
                    this.aktifBolunecekIslem.hatalar.sayiNegatifVeyaSifir = "Bir işlemin toplam net ağırlığı sıfır (0) veya sıfırdan küçük olamaz.";
                }
                else {
                    delete this.aktifBolunecekIslem.hatalar.sayiNegatifVeyaSifir;
                }

                if (_toplamNet !== this.aktifBolunecekIslem.islem.net) {
                    this.aktifBolunecekIslem.hatalar.toplamNet = `Toplam net ağırlığı eşleşmiyor. 
                    (Böldüğünüz toplam net ağırlık ${_toplamNet} kg,
                    olması gereken net ağırlık ${this.aktifBolunecekIslem.islem.net} kg.
                    ${this.aktifBolunecekIslem.islem.net - _toplamNet > 0 ? 'Eksik' : 'Fazla'}
                    net ağırlık: ${Math.abs(this.aktifBolunecekIslem.islem.net - _toplamNet)} kg)`;
                }
                else {
                    delete this.aktifBolunecekIslem.hatalar.toplamNet;
                }
            },
            islemBolKaydet() {
                this.yukleniyorObjesi.islemBol = true;

                axios.post("{{ route('islemBol') }}", {
                    ...this.aktifBolunecekIslem,
                })
                .then(async response => {
                    this.yukleniyorObjesi.islemBol = false;
                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    const duzenlenenIslemler = response.data.islemler;

                    if (this.aktifBolunecekIslem.kaydettiktenSonraSec) {
                        _.forEach(this.aktifBolunecekIslem.bolunmusIslemler, (islem, index) => {
                            if (index === 0) {
                                const secilenIslemIndex = _.findIndex(this.aktifForm.secilenIslemler, { id: islem.id });
                                if (secilenIslemIndex) {
                                    this.aktifForm.secilenIslemler.splice(secilenIslemIndex, 1);
                                }
                            }

                            this.aktifForm.secilenIslemler.push({
                                ...islem,
                                ...(duzenlenenIslemler[index] || {}),
                            });
                        })
                    }

                    await this.firmaGrupluIslemleriGetir(
                        undefined,
                        `{{ route("firmaGrupluIslemleriGetir") }}?page=` + this.aktifForm.firmaGrupluIslemler.current_page
                    );

                    // İŞLEM BÖLÜNDÜKTEN SONRA SEÇME İŞLEMİNİ YAP

                    // const islemIndex = _.findIndex(this.aktifForm.firmaGrupluIslemler.data, {
                    //     id: this.aktifBolunecekIslem.islem.id,
                    // });

                    // if (islemIndex > -1) {
                    //     // this.aktifForm.firmaGrupluIslemler.data.splice(islemIndex, 1, ...duzenlenenIslemler);

                    //     if (this.aktifBolunecekIslem.kaydettiktenSonraSec) {
                    //         // const secilecekIslemler = _.filter(
                    //         //     this.aktifForm.firmaGrupluIslemler.data,
                    //         //     islem => _.find(duzenlenenIslemler, { id: islem.id })
                    //         // );

                    //         _.forEach(duzenlenenIslemler, islem => {
                    //             this.formaEkle(
                    //                 _.find(this.aktifForm.firmaGrupluIslemler.data, { id: islem.id }),
                    //                 {
                    //                     cloneYap: false,
                    //                 }
                    //             );
                    //         })
                    //     }
                    // }

                    this.aktifBolunecekIslem.modal.hide();

                    this.aktifBolunecekIslem = {
                        islem: null,
                        bolunmusIslemler: [],
                        hatalar: {},
                        modal: null,
                    };

                    this.uyariAc({
                        toast: {
                            status: true,
                            message: response.data.mesaj,
                        },
                    });

                    this.aktifForm = _.cloneDeep(this.aktifForm);
                })
                .catch(err => {
                    this.yukleniyorObjesi.islemBol = false;
                    this.uyariAc({
                        baslik: 'Hata',
                        mesaj: "İşlem bölme işlemi sırasında bir hata oluştu.",
                        tur: "error"
                    });
                    console.log(err);
                });
            },
            firmaGrupluFirmaIslemleriGetir(firmaId) {
                this.yukleniyorObjesi.firmaGrupluIslemler = true;
                return axios.get("{{ route('firmaGrupluIslemleriGetir') }}", {
                    params: {
                        firmaId,
                    }
                })
                .then(response => {
                    this.yukleniyorObjesi.firmaGrupluIslemler = false;
                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    return response.data.firmaGrupluIslemler.data[0].islemler;
                })
                .catch(err => {
                    this.yukleniyorObjesi.firmaGrupluIslemler = false;
                    this.uyariAc({
                        baslik: 'Hata',
                        mesaj: "Firma gruplu işlemleri getirme işlemi sırasında bir hata oluştu.",
                        tur: "error"
                    });
                    console.log(err);
                    return [];
                });
            },
        }
    };
</script>
@endsection

@section('style')
    <style>
        table .dikey {
            /* writing-mode: vertical-rl; */
            text-align: center;
        }

        table .dikey span {
            /* transform: rotate(-90deg); */
        }

        table#formGorunumu td {
            vertical-align: middle;
            text-align: center;
        }
    </style>
@endsection