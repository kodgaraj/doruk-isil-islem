@extends('layout') 
@section('content')
<div class="row doruk-content">
    <h4 style="color:#999"><i class="fa fa-home"></i> ROLLER</h4>
    <div class="col-12">
        <div class="card" key="ANASAYFA">
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
                        @can("rol_kaydetme")
                            <button v-if="aktifSayfa.kod === 'ANASAYFA'" class="btn btn-sm btn-primary" @click="rolEkleAc()">
                                <i class="fa fa-plus"></i> ROL EKLE
                            </button>
                        @endcan
                        @canany(["rol_kaydetme", "rol_duzenleme"])
                            <!-- ROL KAYDET BUTONU -->
                            <button v-if="aktifSayfa.kod === 'YENI_ROL'" class="btn btn-primary" @click="rolKaydet()">
                                <i class="fa fa-save"></i> ROL KAYDET
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body">
                <template v-if="aktifSayfa.kod === 'ANASAYFA'">
                    <template v-if="yukleniyorObjesi.rolleriGetir">
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
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Rol</th>
                                            <th>Kod</th>
                                            <th class="text-center">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(rol, index) in roller.data" :key="index">
                                            <td># @{{ rol.id }}</td>
                                            <td class="kisa-uzunluk">@{{ rol.slug }}</td>
                                            <td class="kisa-uzunluk">@{{ rol.name }}</td>
                                            <td class="orta-uzunluk text-center">
                                                @can("rol_duzenleme")
                                                <button class="btn btn-sm btn-primary" @click="rolDuzenle(rol)">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                @endcan

                                                @can("rol_silme")
                                                    <button class="btn btn-sm btn-danger" @click="rolSil(rol)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>

                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row d-flex align-items-center justify-content-between">
                                <div class="col-auto"></div>
                                <div class="col">
                                    <ul class="pagination pagination-rounded justify-content-center mb-0">
                                        <li class="page-item">
                                            <button class="page-link" :disabled="!roller.prev_page_url" @click="rolleriGetir(roller.prev_page_url)">Önceki</button>
                                        </li>
                                        <li
                                            v-for="sayfa in roller.last_page"
                                            class="page-item"
                                            :class="[roller.current_page === sayfa ? 'active' : '']"
                                        >
                                            <button class="page-link" @click='rolleriGetir("{{ route("rolleriGetir") }}?page=" + sayfa)'>@{{ sayfa }}</button>
                                        </li>
                                        <li class="page-item">
                                            <button class="page-link" :disabled="!roller.next_page_url" @click="rolleriGetir(roller.next_page_url)">Sonraki</button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-auto">
                                    <small class="text-muted">Toplam Kayıt: @{{ roller.total }}</small>
                                </div>
                            </div>
                        </div>
                    </template>
                </template>
                <template v-else-if="aktifSayfa.kod === 'YENI_ROL'">
                    <!-- rolEkle -->
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="name">Rol adı *</label>
                            <input
                                v-model="yeniRol.slug"
                                autocomplete="off"
                                id="name"
                                type="text"
                                class="form-control"
                                placeholder="Rol adı"
                            />
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="slug">Rol kodu *</label>
                            <input
                                :value="rolKoduOlustur"
                                autocomplete="off"
                                id="slug"
                                type="text"
                                class="form-control"
                                placeholder="Rol kodu"
                                disabled
                            />
                            <small class="form-text text-muted">Rol adından otomatik oluşturulur</small>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="izinler">İzinler *</label>
                            <v-select
                                v-model="yeniRol.permissions"
                                :options="izinler"
                                label="slug"
                                id="izinler"
                                multiple
                            >
                                <div slot="no-options">İzin bulunamadı!</div>
                            </v-select>
                        </div>
                        <div class="col-12">
                            <label for="description">Açıklama</label>
                            <textarea
                                v-model="yeniRol.description"
                                id="description"
                                class="form-control"
                                placeholder="Açıklama"
                                rows="2"
                            ></textarea>
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
                    aktifSayfa: {
                        kod: "ANASAYFA",
                        baslik: "Rol Yönetimi",
                    },
                    sayfalar: [
                        {
                            kod: "ANASAYFA",
                            baslik: "Rol Yönetimi",
                        },
                        {
                            kod: "YENI_ROL",
                            baslik: "Rol Oluştur",
                            geriFonksiyon: () => this.geriAnasayfa(),
                        },
                    ],
                    yukleniyorObjesi: {
                        rolleriGetir: false,
                        rolSil: false,
                        rolKaydet: false,
                    },
                    roller: {},
                    izinler: @json($izinler),
                    yeniRol: {
                        name: "",
                        slug: "",
                        description: "",
                        permissions: [],
                    },
                };
            },
            computed: {
                rolKoduOlustur() {
                    const slug = _.toLower(this.turkceKarakterCevir(this.yeniRol.slug));

                    this.yeniRol.name = _.replace(slug, /[^a-zA-Z]+/g, "-");

                    return this.yeniRol.name;
                },
            },
            mounted() {
                this.rolleriGetir();
            },
            methods: {
                rolleriGetir(url = "{{ route('rolleriGetir') }}") {
                    this.yukleniyorObjesi.rolleriGetir = true;
                    axios.get(url)
                    .then(response => {
                        this.yukleniyorObjesi.rolleriGetir = false;

                        if (!response.data.durum) {
                            return this.uyariAc({
                                baslik: 'Hata',
                                mesaj: response.data.mesaj,
                                tur: "error"
                            });
                        }

                        this.roller = response.data.roller;
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.rolleriGetir = false;
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                        console.log(error);
                    });
                },
                geriAnasayfa() {
                    this.aktifSayfa = _.find(this.sayfalar, { kod: "ANASAYFA" });

                    this.yeniRol = {
                        name: "",
                        slug: "",
                        description: "",
                        permissions: [],
                    };

                    this.aktifSayfa = _.cloneDeep(this.aktifSayfa);
                },
                rolDuzenle(rol) {
                    this.yeniRol = _.cloneDeep(rol);

                    this.rolEkleAc();
                },
                rolSil(rol) {
                    const islem = (cikisDurum) => {
                        this.yukleniyorObjesi.rolSil = true;

                        axios.post("{{ route('rolSil') }}", {
                            id: rol.id,
                        })
                        .then(response => {
                            this.yukleniyorObjesi.rolSil = false;

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

                            this.rolleriGetir();
                            this.geriAnasayfa();
                        })
                        .catch(error => {
                            this.yukleniyorObjesi.rolSil = false;
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
                        text: `"${ rol.slug }" adlı rolü silmek istediğinize emin misiniz?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sil',
                        cancelButtonText: 'İptal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            islem();
                        }
                    });
                },
                rolEkleAc() {
                    this.aktifSayfa = _.find(this.sayfalar, { kod: "YENI_ROL" });
                },
                rolKaydet() {
                    if (!this.yeniRol.name) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: 'Rol adı boş olamaz!',
                            tur: "error"
                        });
                    }

                    if (!this.yeniRol.slug) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: 'Rol kısa adı boş olamaz!',
                            tur: "error"
                        });
                    }

                    this.yukleniyorObjesi.rolKaydet = true;
                    axios.post('/rolKaydet', {
                        rol: this.yeniRol,
                    })
                    .then(response => {
                        this.yukleniyorObjesi.rolKaydet = false;

                        if (!response.data.durum) {
                            return this.uyariAc({
                                baslik: 'Hata',
                                mesaj: response.data.mesaj,
                                tur: "error"
                            });
                        }

                        const rol = response.data.rol;

                        this.uyariAc({
                            toast: {
                                status: true,
                                message: response.data.mesaj,
                            },
                        });

                        this.rolleriGetir();
                        this.geriAnasayfa();
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.rolKaydet = false;
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                        console.log(error);
                    });
                },
                turkceKarakterCevir(str) {
                    return str
                        .replace(/[Çç]/g, "C")
                        .replace(/[Ğğ]/g, "G")
                        .replace(/[İı]/g, "I")
                        .replace(/[Öö]/g, "O")
                        .replace(/[Şş]/g, "S")
                        .replace(/[Üü]/g, "U");
                },
            }
        };
    </script>
@endsection