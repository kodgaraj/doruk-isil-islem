@extends('layout') 
@section('content')
<div class="row doruk-content">
    <h4 style="color:#999"><i class="fab fa-wpforms"> </i> ISIL İŞLEM TAKİP</h4>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <template v-if="aktifIslem === null">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">ISIL İŞLEMLER</h4>
                        </div>
                        <div class="col-4 text-end">
                            <button @click="islemEkle" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> ISIL İŞLEM EKLE</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-rep-plugin">
                                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                                            <table id="tech-companies-1" class="table table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Firma</th>
                                                        <th data-priority="1">Fırın</th>
                                                        <th data-priority="2">Şarj</th>
                                                        <th data-priority="3">Sepet</th>
                                                        <th data-priority="4">Malzeme</th>
                                                        <th data-priority="5">Kalite</th>
                                                        <th data-priority="6">Son Sertlik</th>
                                                        <th data-priority="7">İşlemler</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template v-if="islemler.length">
                                                        <tr v-for="(islem, index) in islemler" :key="index">
                                                            <th>
                                                                @{{ islem.firma }}
                                                            </th>
                                                            <td>
                                                                <button type="button" class="btn btn-sm waves-effect waves-light" :class="islem.firinClass">
                                                                    @{{ islem.firin }}
                                                                </button>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm waves-effect waves-light" :class="islem.sarjClass">
                                                                    @{{ islem.sarj }}
                                                                </button>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm waves-effect waves-light" :class="islem.sepetClass">
                                                                    @{{ islem.sepet }}
                                                                </button>
                                                            </td>
                                                            <td>
                                                                @{{ islem.malzeme }}
                                                            </td>
                                                            <td>
                                                                @{{ islem.kalite }}
                                                            </td>
                                                            <td>
                                                                @{{ islem.sonSertlik }}
                                                            </td>
                                                            <td>
                                                                <button @click="islemDuzenle(islem)" class="btn btn-sm btn-warning waves-effect waves-light">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button @click="islemSil(islem)" class="btn btn-sm btn-danger waves-effect waves-light">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                    <template v-else>
                                                        <tr>
                                                            <td colspan="8" class="text-center">
                                                                <h6>İşlem Bulunamadı</h6>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                </template>
                <template v-else>
                    <h4 class="card-title">SİPARİŞ TAKİP FORMU</h4>
                    <button @click="geri" class="btn btn-primary btn-sm"><i class="fas fa-arrow-left"></i> GERİ</button>
                    <BR></BR>

                    <div class="mb-3 row">
                        <label for="example-date-input" class="col-md-2 col-form-label">Tarih</label>
                        <div class="col-md-10">
                            <input class="form-control" type="date" id="example-date-input">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="sira-no-input" class="col-md-2 col-form-label">Sıra No</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" placeholder="Sıra No" id="sira-no-input">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="example-email-input" class="col-md-2 col-form-label">Müşteri</label>
                        <div class="col-md-10">
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Select</option>
                                <option>Large select</option>
                                <option>Small select</option>
                            </select>
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
                                <tr v-for="(isilIslem, index) in isilIslemler" :key="index">
                                    <td>@{{ index + 1 }}</td>
                                    <td>
                                        <select class="form-select" aria-label="Default select example">
                                            <option selected>Select</option>
                                            <option>Large select</option>
                                            <option>Small select</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" placeholder="Miktar KG" v-model="isilIslem.miktar">
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" placeholder="Adet" v-model="isilIslem.adet">
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
                                        <button class="btn btn-danger" @click="islemSil(index)">Sil</button>
                                    </td>
                                </tr>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="8">
                                        <button class="btn btn-success" @click="isilIslemEkle">Ekle</button>
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
        data() {
            return {
                aktifIslem: null,
                islemler: [
                    {
                        firma: "Firma 1",
                        firin: "Kamaralı Fırın",
                        sarj: "1. Şarj",
                        sepet: "Uzun Sepet",
                        malzeme: "Pul",
                        kalite: "Kalite 1",
                        sonSertlik: "Çok Sert",
                        firinClass: "btn-danger",
                        sarjClass: "btn-dark",
                        sepetClass: "btn-outline-warning",
                    },
                    {
                        firma: "Firma 2",
                        firin: "Kamaralı Fırın",
                        sarj: "2. Şarj",
                        sepet: "Uzun Sepet",
                        malzeme: "Çelik Mil",
                        kalite: "Kalite 2",
                        sonSertlik: "Yumuşak",
                        firinClass: "btn-danger",
                        sarjClass: "btn-dark",
                        sepetClass: "btn-outline-warning",
                    },
                    {
                        firma: "Firma 3",
                        firin: "Alüminyum Fırın",
                        sarj: "1. Şarj",
                        sepet: "Geniş Sepet",
                        malzeme: "Çelik Mil",
                        kalite: "Kalite 1",
                        sonSertlik: "Sert",
                        firinClass: "btn-secondary",
                        sarjClass: "btn-dark",
                        sepetClass: "btn-outline-warning",
                    },
                ],
                isilIslem: {
                    malzeme: '',
                    miktar: '',
                    adet: '',
                    kalite: '',
                    yapilacak_islem: '',
                    istenilen_sertlik: ''
                },
                isilIslemler: [],
            };
        },
        methods: {
            islemEkle() {
                this.aktifIslem = {
                    tarih: '',
                    sira_no: '',
                    musteri: '',
                };
            },
            islemSil(index) {
                this.isilIslemler.splice(index, 1);
            },
            isilIslemEkle() {
                this.isilIslemler.push({ ...this.isilIslem });
            },
            geri() {
                this.aktifIslem = null;
            },
        }
    };
</script>
@endsection
