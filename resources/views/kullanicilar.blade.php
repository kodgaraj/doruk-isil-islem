@extends('layout') 
@section('content')
<div class="row doruk-content">
    <h4 style="color:#999"><i class="fa fa-home"></i> KULLANICILAR</h4>
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
                        <button v-if="aktifSayfa.kod === 'ANASAYFA'" class="btn btn-sm btn-primary" @click="yeniKullaniciEkleAc('YENI_KULLANICI')">
                            <i class="fa fa-plus"></i> KULLANICI EKLE
                        </button>
                        <!-- KAYDET BUTONU -->
                        <button v-if="aktifSayfa.kod === 'YENI_KULLANICI'" class="btn btn-primary" @click="kullaniciKaydet()">
                            <i class="fa fa-save"></i> KAYDET
                        </button>
                        <!-- ROL KAYDET BUTONU -->
                        <button v-if="aktifSayfa.kod === 'YENI_ROL'" class="btn btn-primary" @click="rolKaydet()">
                            <i class="fa fa-save"></i> ROL KAYDET
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <template v-if="aktifSayfa.kod === 'ANASAYFA'">
                    <template v-if="yukleniyorObjesi.kullanicilariGetir">
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
                                            <th>Kullanıcı Adı</th>
                                            <th>E-Posta</th>
                                            <th>Rol</th>
                                            <th class="text-center">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(kullanici, index) in kullanicilar.data" :key="index">
                                            <td># @{{ kullanici.id }}</td>
                                            <td class="kisa-uzunluk">@{{ kullanici.name }}</td>
                                            <td class="kisa-uzunluk">@{{ kullanici.email }}</td>
                                            <td class="kisa-uzunluk">@{{ kullanici.roller }}</td>
                                            <td class="orta-uzunluk text-center">
                                                <button class="btn btn-sm btn-primary" @click="kullaniciDuzenle(kullanici)">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" @click="kullaniciSil(kullanici)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
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
                                            <button class="page-link" :disabled="!kullanicilar.prev_page_url" @click="kullanicilariGetir(kullanicilar.prev_page_url)">Önceki</button>
                                        </li>
                                        <li
                                            v-for="sayfa in kullanicilar.last_page"
                                            class="page-item"
                                            :class="[kullanicilar.current_page === sayfa ? 'active' : '']"
                                        >
                                            <button class="page-link" @click='kullanicilariGetir("{{ route("kullanicilar") }}?page=" + sayfa)'>@{{ sayfa }}</button>
                                        </li>
                                        <li class="page-item">
                                            <button class="page-link" :disabled="!kullanicilar.next_page_url" @click="kullanicilariGetir(kullanicilar.next_page_url)">Sonraki</button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-auto">
                                    <small class="text-muted">Toplam Kayıt: @{{ kullanicilar.total }}</small>
                                </div>
                            </div>
                        </div>
                    </template>
                </template>
                <template v-else-if="aktifSayfa.kod === 'YENI_KULLANICI'">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="name">Kullanıcı Adı *</label>
                            <input id="name" type="text" class="form-control" v-model="yeniKullanici.name" placeholder="Kullanıcı Adı">
                            <small class="form-text text-muted">Kullanıcının görünecek ismi</small>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="email">E-posta *</label>
                            <input autocomplete="off" id="email" type="text" class="form-control" v-model="yeniKullanici.email" placeholder="E-Posta">
                            <small class="form-text text-muted">Sisteme giriş için zorunlu</small>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="roles">Roller *</label>
                            <div class="row d-flex align-items-center">
                                <div class="col">
                                    <v-select
                                        v-model="yeniKullanici.roles"
                                        :options="roller"
                                        label="slug"
                                        id="roles"
                                        multiple
                                    >
                                        <div slot="no-options">Rol bulunamadı!</div>
                                    </v-select>
                                </div>
                                <div class="col-auto ps-0">
                                    <button class="btn btn-sm btn-primary" @click="rolEkleAc(true)">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="password">Şifre @{{ !yeniKullanici.id ? '*' : '' }}</label>
                            <input autocomplete="new-password" id="password" type="password" class="form-control" v-model="yeniKullanici.password" placeholder="Şifre">
                        </div>
                    </div>
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
                        baslik: "Kullanıcı Tanımlama ve Yetkilendirme",
                    },
                    sayfalar: [
                        {
                            kod: "ANASAYFA",
                            baslik: "Kullanıcı Tanımlama ve Yetkilendirme",
                        },
                        {
                            kod: "YENI_KULLANICI",
                            baslik: "Kullanıcı Oluştur",
                            geriFonksiyon: () => this.geriAnasayfa(),
                        },
                        {
                            kod: "YENI_ROL",
                            baslik: "Rol Oluştur",
                            geriFonksiyon: () => this.geriAnasayfa(),
                        },
                    ],
                    yukleniyorObjesi: {
                        kullanicilariGetir: false,
                        kullaniciKaydet: false,
                        kullaniciSil: false,
                        rolKaydet: false,
                    },
                    kullanicilar: {},
                    roller: @json($roller),
                    izinler: @json($izinler),
                    yeniKullanici: {
                        name: "",
                        email: "",
                        password: "",
                        roller: null,
                    },
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
                this.kullanicilariGetir();
            },
            methods: {
                kullanicilariGetir() {
                    this.yukleniyorObjesi.kullanicilariGetir = true;
                    axios.get('/kullanicilariGetir')
                        .then(response => {
                            this.yukleniyorObjesi.kullanicilariGetir = false;

                            if (!response.data.durum) {
                                return this.uyariAc({
                                    baslik: 'Hata',
                                    mesaj: response.data.mesaj,
                                    tur: "error"
                                });
                            }

                            this.kullanicilar = response.data.kullanicilar;
                        })
                        .catch(error => {
                            this.yukleniyorObjesi.kullanicilariGetir = false;
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                                tur: "error"
                            });
                            console.log(error);
                        });
                },
                yeniKullaniciEkleAc() {
                    this.aktifSayfa = _.find(this.sayfalar, { kod: "YENI_KULLANICI" });
                },
                geriAnasayfa() {
                    this.aktifSayfa = _.find(this.sayfalar, { kod: "ANASAYFA" });

                    this.yeniKullanici = {
                        name: "",
                        email: "",
                        password: "",
                        roller: null,
                    };

                    this.yeniRol = {
                        name: "",
                        slug: "",
                        description: "",
                        permissions: [],
                    };

                    this.aktifSayfa = _.cloneDeep(this.aktifSayfa);
                },
                kullaniciKaydet() {
                    if (!this.yeniKullanici.name) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: 'Kullanıcı adı boş olamaz!',
                            tur: "error"
                        });
                    }

                    if (!this.yeniKullanici.email) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: 'E-posta boş olamaz!',
                            tur: "error"
                        });
                    }

                    if (!this.yeniKullanici.id && !this.yeniKullanici.password) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: 'Şifre boş olamaz!',
                            tur: "error"
                        });
                    }

                    this.yukleniyorObjesi.kullaniciKaydet = true;
                    axios.post('/kullaniciKaydet', {
                        kullanici: this.yeniKullanici,
                    })
                    .then(response => {
                        this.yukleniyorObjesi.kullaniciKaydet = false;

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

                        this.kullanicilariGetir();
                        this.geriAnasayfa();
                    })
                    .catch(error => {
                        this.yukleniyorObjesi.kullaniciKaydet = false;
                        this.uyariAc({
                            baslik: 'Hata',
                            mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                            tur: "error"
                        });
                        console.log(error);
                    });
                },
                kullaniciDuzenle(kullanici) {
                    this.yeniKullanici = _.cloneDeep(kullanici);

                    this.yeniKullaniciEkleAc();
                },
                kullaniciSil(kullanici) {
                    const islem = (cikisDurum) => {
                        this.yukleniyorObjesi.kullaniciSil = true;

                        axios.post("{{ route('kullaniciSil') }}", {
                            id: kullanici.id,
                        })
                        .then(response => {
                            this.yukleniyorObjesi.kullaniciSil = false;

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

                            if (cikisDurum) {
                                return window.location.reload();
                            }

                            this.kullanicilariGetir();
                        })
                        .catch(error => {
                            this.yukleniyorObjesi.kullaniciSil = false;
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data.hataKodu,
                                tur: "error"
                            });
                            console.log(error);
                        });
                    };

                    const sistemdenCikisDurum = kullanici.id == {{ Auth::user()->id }};

                    Swal.fire({
                        title: "Uyarı",
                        text: `${ sistemdenCikisDurum ? 'İşleme devam ederseniz, sistemden çıkış yapılacaktır. ' : '' } "${ kullanici.name }" adlı kullanıcıyı silmek istediğinize emin misiniz?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sil',
                        cancelButtonText: 'İptal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            islem(sistemdenCikisDurum);
                        }
                    });
                },
                rolEkleAc(hizliEkle = false) {
                    this.aktifSayfa = _.find(this.sayfalar, { kod: "YENI_ROL" });

                    if (hizliEkle) {
                        this.yeniRol.hizliEkle = true;
                        this.aktifSayfa.geriFonksiyon = () => {
                            this.yeniRol = {
                                name: "",
                                slug: "",
                                description: "",
                                permissions: [],
                            };
                            this.yeniKullaniciEkleAc();
                        };
                    }
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

                        if (this.yeniRol.hizliEkle) {
                            if (!this.yeniKullanici.roles) {
                                this.yeniKullanici.roles = [];
                            }

                            this.roller.push(rol);
                            this.yeniKullanici.roles.push(rol);
                            this.aktifSayfa.geriFonksiyon();
                        } else {
                            this.geriAnasayfa();
                        }
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