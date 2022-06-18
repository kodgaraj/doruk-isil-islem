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
                        <div class="col-8">
                            <h4 class="card-title">FORMLAR</h4>
                        </div>
                        @can("isil_islem_formu_kaydetme")
                            <div class="col-4 text-end">
                                <button @click="formEkleAc" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> FORM EKLE</button>
                            </div>
                        @endcan
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
                                                        <th class="text-center">Form ID</th>
                                                        <th>Takip No</th>
                                                        <th>Form Adı</th>
                                                        <th class="text-center">İşlem Sayısı</th>
                                                        <th>Baslangıç/Bitiş Tarihi</th>
                                                        <th class="text-center">İşlemler</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(form, index) in formlar.data" :key="index">
                                                        <td class="kisa-uzunluk text-center"># @{{ form.id }}</td>
                                                        <td class="kisa-uzunluk">@{{ form.takipNo }}</td>
                                                        <td class="uzun-uzunluk">@{{ form.formAdi }}</td>
                                                        <td class="text-center kisa-uzunluk">@{{ form.islemSayisi }}</td>
                                                        <td class="kisa-uzunluk">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <span>@{{ form.baslangicTarihi }}</span>
                                                                </div>
                                                                <div class="col-12">
                                                                    <span v-if="form.bitisTarihi">
                                                                        @{{ form.bitisTarihi }}
                                                                    </span>
                                                                    <small v-else class="text-muted">
                                                                        Form henüz tamamlanmadı
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center orta-uzunluk">
                                                            <div class="row g-1 d-inline-flex">
                                                                <div class="col">
                                                                    <button @click="formDetayGoruntule(form)" class="btn btn-info btn-sm">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                </div>

                                                                @can("isil_islem_formu_duzenleme")
                                                                    <div class="col">
                                                                        <button @click="formDuzenle(form)" class="btn btn-warning btn-sm">
                                                                            <i class="fas fa-edit"></i>
                                                                        </button>
                                                                    </div>
                                                                @endcan

                                                                @can("isil_islem_formu_silme")
                                                                    <div class="col">
                                                                        <button @click="formSil(form)" class="btn btn-danger btn-sm">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                @endcan
                                                            </div>
                                                        </td>
                                                    </tr>
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
                                                        <button class="page-link" :disabled="!formlar.prev_page_url" @click="formlariGetir(formlar.prev_page_url)">Önceki</button>
                                                    </li>
                                                    <li
                                                        v-for="sayfa in formlar.last_page"
                                                        class="page-item"
                                                        :class="[formlar.current_page === sayfa ? 'active' : '']"
                                                    >
                                                        <button class="page-link" @click="formlariGetir('/formlar?page=' + sayfa)">@{{ sayfa }}</button>
                                                    </li>
                                                    <li class="page-item">
                                                        <button class="page-link" :disabled="!formlar.next_page_url" @click="formlariGetir(formlar.next_page_url)">Sonraki</button>
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
                        <div class="col-8">
                            <div class="d-flex flex-row align-items-center">
                                <button @click="geriAnasayfa" class="btn btn-warning"><i class="fas fa-arrow-left"></i> GERİ</button>
                                <h4 class="card-title m-0 ms-2">
                                    ISIL İŞLEM FORMU EKLEME
                                    <div class="d-inline-flex" v-if="araYukleniyor">
                                        <div class="spinner-grow text-primary m-1 spinner-grow-sm" role="status">
                                            <span class="sr-only">Yükleniyor...</span>
                                        </div>
                                    </div>
                                </h4>
                            </div>
                        </div>
                        <div class="col-4 text-end">
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
                            <div class="col-12" v-for="(firma, fIndex) in aktifForm.firmaGrupluIslemler.data" :key="fIndex">
                                <div class="row">
                                    <h5>@{{ firma.firmaAdi }} (@{{ firma.sorumluKisi }})</h5>
                                </div>
                                <div class="table-rep-plugin">
                                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                                        <table id="tech-companies-1" class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>İşlem ID</th>
                                                    <th class="text-center">Termin</th>
                                                    <th>Malzeme</th>
                                                    <th>İşlem</th>
                                                    <th>İstenilen Sertlik</th>
                                                    <th>Kalite</th>
                                                    <th class="text-center">Fırın*</th>
                                                    <th class="text-center">Şarj*</th>
                                                    <th class="text-center">Ekle</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(islem, iIndex) in firma.islemler" :key="iIndex">
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
                                                                <span class="badge bg-primary">Sipariş No: @{{ islem.siparisNo }}</span>
                                                            </div>
                                                            <div v-if="islem.tekrarEdenId" class="col-12">
                                                                <span class="badge rounded-pill bg-danger">Tekrar Eden İşlem ID: @{{ islem.tekrarEdenId }}</span>
                                                            </div>
                                                            <div v-if="islem.tekrarEdilenId" class="col-12">
                                                                <span class="badge rounded-pill bg-info">Tekrar Edilen İşlem ID: @{{ islem.tekrarEdilenId }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="kisa-uzunluk text-center">
                                                        <span class="badge badge-pill" :class="`bg-${ islem.gecenSureRenk }`">@{{ islem.gecenSure }} Gün</span>
                                                    </td>
                                                    <td class="orta-uzunluk">
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
                                                    <td class="kisa-uzunluk">@{{ islem.islemTuruAdi ? islem.islemTuruAdi : "-" }}</td>
                                                    <td class="kisa-uzunluk">@{{ islem.istenilenSertlik ? islem.istenilenSertlik : "-" }}</td>
                                                    <td class="kisa-uzunluk">@{{ islem.kalite ? islem.kalite : "-" }}</td>
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
                                                    <td class="kisa-uzunluk text-center">
                                                        <button
                                                            @click="formaIslemEkleSil(islem)"
                                                            class="btn"
                                                            :class="islem.secildi ? 'btn-success' : 'btn-outline-primary'"
                                                        >
                                                            <i class="fas" :class="islem.secildi ? 'fa-check' : 'fa-plus'"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <ul class="pagination pagination-rounded justify-content-center mb-0">
                                    <li class="page-item">
                                        <button class="page-link" :disabled="!aktifForm.firmaGrupluIslemler.prev_page_url" @click="firmaGrupluIslemleriGetir(null, aktifForm.firmaGrupluIslemler.prev_page_url)">Önceki</button>
                                    </li>
                                    <li
                                        v-for="sayfa in aktifForm.firmaGrupluIslemler.last_page"
                                        class="page-item"
                                        :class="[aktifForm.firmaGrupluIslemler.current_page === sayfa ? 'active' : '']"
                                    >
                                        <button class="page-link" @click="firmaGrupluIslemleriGetir(null, '/firmaGrupluIslemleriGetir?page=' + sayfa)">@{{ sayfa }}</button>
                                    </li>
                                    <li class="page-item">
                                        <button class="page-link" :disabled="!aktifForm.firmaGrupluIslemler.next_page_url" @click="firmaGrupluIslemleriGetir(null, aktifForm.firmaGrupluIslemler.next_page_url)">Sonraki</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-else-if="aktifSayfa.kod === 'FORM_GORUNUMU'">
                    <div class="row">
                        <div class="col-8">
                            <div class="d-flex flex-row align-items-center">
                                <button @click="geriYeniForm" class="btn btn-warning"><i class="fas fa-arrow-left"></i> GERİ</button>
                                <h4 class="card-title m-0 ms-2">
                                    @{{ aktifForm.formAdi }}
                                    <div class="d-inline-flex" v-if="araYukleniyor">
                                        <div class="spinner-grow text-primary m-1 spinner-grow-sm" role="status">
                                            <span class="sr-only">Yükleniyor...</span>
                                        </div>
                                    </div>
                                </h4>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            @can("isil_islem_formu_duzenleme")
                                <button @click="moduDegistir" class="btn btn-outline-info">
                                    <i class="fas fa-eye" v-if="!aktifForm.onizlemeModu"></i>
                                    <i class="fas fa-eye-slash" v-else></i>
                                </button>
                            @endcan
                            <button @click="ciktiAl" class="btn btn-primary">
                                <i class="fas fa-file-export"></i>
                                ÇIKTI
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
                                    <table id="formGorunumu" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fırın</th>
                                                <th>Şarj</th>
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
                                                        <tr :key="firinId + '-' + sarjId + '-' + islemIndex">
                                                            <td
                                                                class="dikey"
                                                                :rowspan="firin.toplamIslemSayisi"
                                                                v-if="sarjIndex === 0 && islemIndex === 0"
                                                            >
                                                                <span>@{{ firin.firinAdi }}</span>
                                                            </td>
                                                            <td
                                                                class="dikey"
                                                                :rowspan="sarj.toplamIslemSayisi"
                                                                v-if="islemIndex === 0"
                                                            >
                                                                <span>@{{ sarj.sarj }}. Şarj</span>
                                                            </td>
                                                            <td class="orta-uzunluk align-left">@{{ islem.firmaAdi }}</td>
                                                            <td class="kisa-uzunluk align-left">@{{ islem.malzemeAdi }}</td>
                                                            <td class="kisa-uzunluk">
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
                                                            <td class="kisa-uzunluk">
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
                                                            <td class="kisa-uzunluk">
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
                                                            <td class="kisa-uzunluk">
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
                                                            <td class="kisa-uzunluk">
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
                                                            <td class="kisa-uzunluk">
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
                },
                firinlar: [],
                sorguParametreleri: {
                    formId: null,
                    islemNo: null,
                },
            }
        },
        mounted() {
            this.formlariGetir();
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
            formlariGetir(url = "/formlar") {
                this.yukleniyorObjesi.form = true;
                axios.get(url).then(response => {
                    if (!response.data.durum) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: response.data.mesaj,
                            tur: "error"
                        });
                    }

                    this.formlar = response.data.formlar;

                    let url = new URL(window.location.href);
                    this.sorguParametreleri.formId = _.toNumber(url.searchParams.get("formId"));
                    this.sorguParametreleri.islemId = _.toNumber(url.searchParams.get("islemId"));
                    if (this.sorguParametreleri.formId && this.sorguParametreleri.islemId) {
                        const form = _.find(this.formlar.data, { id: this.sorguParametreleri.formId });

                        if (form) {
                            this.formDuzenle(form);
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
                        formId,
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

                    this.aktifForm.firmaGrupluIslemler = response.data.firmaGrupluIslemler;
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
            formaEkle(islem, cloneYap = true) {
                const islemIndex = _.findIndex(this.aktifForm.secilenIslemler, { id: islem.id });
                if (islemIndex === -1) {
                    this.aktifForm.secilenIslemler.push(islem);
                }

                islem.secildi = true;

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
            ciktiAl() {
                const baslangicDurum = !!this.aktifForm.onizlemeModu;

                this.aktifForm.onizlemeModu = true;
                this.aktifForm = _.cloneDeep(this.aktifForm);
                this.$nextTick(() => {
                    html2canvas(document.getElementById("formGorunumu")).then(canvas => {
                        var a = document.createElement("a");
                        a.href = canvas.toDataURL("image/png");
                        a.download = this.aktifForm.formAdi + ".png";
                        a.click();
                        this.aktifForm.onizlemeModu = baslangicDurum;
                    });
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
                            baslik: 'Başarılı',
                            mesaj: 'Form başarıyla kaydedildi.',
                            tur: "success"
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
            formDuzenle(form) {
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

                const promises = [];

                if (!_.size(this.firinlar)) {
                    promises.push(this.firinlariGetir());
                }

                promises.push(this.firmaGrupluIslemleriGetir(this.aktifForm.id));

                return Promise.all(promises)
                .then((p) => {
                    this.yukleniyorObjesi.form = false;
                    // this.aktifForm.secilenIslemler = response.data.secilenIslemler;

                    for (const [index, firma] of _.toPairs(this.aktifForm.firmaGrupluIslemler.data)) {
                        for (const [index, islem] of _.toPairs(firma.islemler)) {
                            if (islem.firinId) {
                                islem.firin = _.find(this.firinlar, { id: islem.firinId });

                                this.formaEkle(islem, false);
                            }
                        }
                    }

                    this.aktifForm.baslangictakiIslemler = _.cloneDeep(this.aktifForm.secilenIslemler);

                    this.aktifSayfaDegistir("YENI_FORM");

                    this.aktifForm = _.cloneDeep(this.aktifForm);
                });
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
            formDetayGoruntule(form) {
                this.formDuzenle(form).then(() => {
                    this.formHazirla();
                    this.aktifForm.onizlemeModu = true;
                    this.aktifForm.detayGoruntule = true;
                    this.aktifForm.geriFonksiyon = () => {
                        this.geriAnasayfa();
                    };

                    this.aktifForm = _.cloneDeep(this.aktifForm);
                });
            },
            sorguParametreleriTemizle() {
                this.sorguParametreleri = {
                    formId: null,
                    islemId: null,
                };

                window.history.replaceState({}, document.title, (new URL(window.location.href)).pathname)
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