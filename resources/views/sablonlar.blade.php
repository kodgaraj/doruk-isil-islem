@extends('layout')
@section('style')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link href="https://unpkg.com/quill-table-ui@1.0.5/dist/index.css" rel="stylesheet">

<style>
    .a4 {
        width: 21cm;
        min-height: 29.7cm;
        padding: 2cm;
        margin: 1cm auto;
        border: 1px #D3D3D3 solid;
        border-radius: 5px;
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection
@section('content')
    <div class="row doruk-content">
        <h4 style="color:#999"><i class="fa fa-home"></i> ŞABLONLAR</h4>
        <div class="col-12">
            <div class="card" key="ANASAYFA">
                <div class="card-header">
                    <div class="row d-flex align-items-center">
                        <div class="col">
                            <h4>
                                <button class="btn btn-warning" v-if="aktifSayfa.geriFonksiyon"
                                    @click="aktifSayfa.geriFonksiyon()">
                                    <i class="fa fa-arrow-left"></i> GERİ
                                </button>
                                @{{ aktifSayfa.baslik }}
                            </h4>
                        </div>
                        <div class="col-auto">
                            <button v-if="aktifSayfa.kod === 'ANASAYFA'" class="btn btn-sm btn-primary"
                                @click="sablonEkleAc()">
                                <i class="fa fa-plus"></i> ŞABLON EKLE
                            </button>
                            <!-- sablon KAYDET BUTONU -->
                            <button v-if="aktifSayfa.kod === 'YENI_SABLON' && !yukleniyorObjesi.sablonGoster" class="btn btn-success" @click="onizle()">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button v-if="aktifSayfa.kod === 'YENI_SABLON' && yukleniyorObjesi.sablonGoster" class="btn btn-danger" @click="onizle()">
                                <i class="fa fa-eye-slash"></i>
                            </button>
                            <button v-if="aktifSayfa.kod === 'YENI_SABLON' && !yukleniyorObjesi.sablonEkle" class="btn btn-primary" @click="sablonEkle()">
                                <i class="fa fa-save"></i> ŞABLON KAYDET
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <template v-if="aktifSayfa.kod === 'ANASAYFA'">
                        <template v-if="yukleniyorObjesi.sablonlariGetir">
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
                                                <th>Şablon Adı</th>
                                                <th class="text-center">Şablon Türü</th>
                                                <th class="text-center">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(sablon, index) in sablonlar" :key="index">
                                                <td># @{{ sablon.id }}</td>
                                                <td class="uzun-uzunluk">
                                                    <div class="col-12">
                                                        @{{ sablon.sablonAdi }}
                                                    </div>

                                                </td>
                                                <td class="kisa-uzunluk text-center">
                                                    @{{ sablon.tur }}
                                                </td>
                                                <td class="kisa-uzunluk text-center">
                                                    <button class="btn btn-sm btn-primary" @click="sablonDuzenle(sablon)">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" @click="sablonCogalt(sablon)">
                                                        <i class="fa fa-clone"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" @click="sablonSil(sablon)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                    </table>
                                </div>
                            </div>

                        </template>
                    </template>
                    <template v-else-if="aktifSayfa.kod === 'YENI_SABLON'">
                        <!-- ŞablonEkle -->
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="name">Şablon adı *</label>
                                <input v-model="yeniSablon.sablonAdi" autocomplete="off" id="name" type="text"
                                    class="form-control" placeholder="Şablon Adı" />
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label>Şablon türü *</label>
                                <select v-model="yeniSablon.tur" class="form-control">
                                    <option v-for="tur in sablonTurleri"> @{{tur.adi}}</option>

                                </select>
                            </div>
                            <div class="col-12">
                                <label>Şablon İçeriği</label>

                                <button class="btn btn-warning" @click="sablonSayfaEkle()">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <div class="col-12">
                                <label>Kullanılabilir Öğeler :</label>
                                <span class="badge badge-pill bg-primary" style="font-size: 90%; margin: 3px;" v-for="ogeler in sablonTur.ogeler">@{{ogeler}}</span>
                            </div>
                            <div class="col-12">
                                <label>Kullanılan Öğeler :</label>
                                <span class="badge badge-pill bg-success" style="font-size: 90%; margin: 3px;" v-for="ogeler in yeniSablon.kullanilabilirOgeler">@{{ogeler}}</span>
                            </div>
                            <div v-show="!yukleniyorObjesi.sablonGoster" class="col-12 col-sm-12 col-md-12" v-for="(sayi, index) in sablonSayfaSayisi" :key="index">
                                <label>Sayfa @{{index+1}}</label>
                                <div class="card" >
                                    <div class="card-body p-0">
                                        <div :id="'editor-' + index" ></div>
                                    </div>
                                </div>
                            </div>
                            <div v-show="yukleniyorObjesi.sablonGoster" class="col-12 col-sm-12 col-md-12">
                                <label>Şablon Önizlemesi</label>
                                <div class="card" v-for="(sayi, index) in sablonSayfaSayisi">
                                    <div class="card-body ql-editor a4 p-0">
                                        <div v-html="yeniSablon.icerik_html[index]"></div>
                                    </div>
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
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script>

        let mixinApp = {

            data() {
                return {
                    aktifSayfa: {
                        kod: "ANASAYFA",
                        baslik: "Şablon Yönetimi",
                    },
                    sayfalar: [{
                            kod: "ANASAYFA",
                            baslik: "Şablon Yönetimi",
                        },
                        {
                            kod: "YENI_SABLON",
                            baslik: "Şablon Düzenleme",
                            geriFonksiyon: () => this.geriAnasayfa(),
                        },
                    ],
                    yukleniyorObjesi: {
                        sablonlariGetir: false,
                        sablonSil: false,
                        sablonEkle: false,
                        sablonCogalt: false,
                        sablonGoster: false,
                    },
                    sablonTurleri: [{"adi":"","ogeler":[]},{"adi":"TEKLIF","ogeler":["[baslik]","[tarih]","[firmaAdi]","[sorumluKisi]","[telefon]","[adres]","[eposta]","[teklifIcerikleri]","[gecerlilikTarihi]"]}, {"adi":"ISLEM","ogeler":["[tarih]","[firmaAdi]","[sorumluKisi]","[gecerlilikTarihi]"]},{"adi":"MAIL","ogeler":["[firmaAdi]","[sorumluKisi]","[tur]","[eposta]"]}],
                    sablonlar: [],
                    sablonSayfaSayisi: 0,
                    yeniSablon: {
                        sablonAdi: "",
                        tur: "",
                        icerik: [],
                        icerik_html: [],
                    },
                    customToolbar: [
                        [{ header: [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        ["bold", "italic", "underline","strike"],
                        ['blockquote', 'code-block'],
                        [{align:""},{align:"center"},{align:"right"},{align:"justify"},],
                        [{ list: "ordered" }, { list: "bullet" }],
                        [{ color: [] }, { background: [] }],
                        ["link","image"],
                        ["clean"],
                    ],
                    editor: [],
                    icerikler: []
                };
            },
            mounted() {
                this.sablonlariGetir();
            },
            methods: {
                sablonlariGetir(url = "{{ route('sablonlariGetir') }}") {
                    this.yukleniyorObjesi.sablonlariGetir = true;
                    axios.get(url)
                        .then(response => {
                            this.yukleniyorObjesi.sablonlariGetir = false;

                            if (!response.data.durum) {
                                return this.uyariAc({
                                    baslik: 'Hata',
                                    mesaj: response.data.mesaj,
                                    tur: "error"
                                });
                            }

                            this.sablonlar = response.data.sablonlar;

                        })
                        .catch(error => {
                            this.yukleniyorObjesi.sablonlariGetir = false;
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data
                                    .hataKodu,
                                tur: "error"
                            });
                            console.log(error);
                        });
                },
                geriAnasayfa() {
                    this.aktifSayfa = _.find(this.sayfalar, {
                        kod: "ANASAYFA"
                    });

                    this.yeniSablon = {
                        sablonAdi: "",
                        tur: "",
                        icerik: [],
                        icerik_html: [],
                        kullanilabilirOgeler: [],
                    };
                    this.sablonSayfaSayisi = 0;
                    this.editor = [];

                    this.aktifSayfa = _.cloneDeep(this.aktifSayfa);
                },
                onizle() {
                    this.yukleniyorObjesi.sablonGoster = !this.yukleniyorObjesi.sablonGoster;
                },
                async sablonDuzenle(sablon) {
                    this.yeniSablon = _.cloneDeep(sablon);
                    this.sablonEkleAc(true);

                    for (let index = 0; index < this.yeniSablon.icerik.length; index++) {
                        await this.sablonSayfaEkle(index);
                        let iceriklerHtml = "<div class='table-rep-plugin'><div class='table-responsive mb-0'><table class='table table-striped'><thead><tr><th>İşlem</th><th>Fiyat</th><th>Ölçü</th><th>Birim</th></tr></thead><tbody></tbody></table></div></div>";
                        this.yeniSablon.icerik_html[index] = this.yeniSablon.icerik_html[index].replaceAll('[teklifIcerikleri]', iceriklerHtml);
                    }
                },
                sablonSil(sablon) {
                    const islem = (cikisDurum) => {
                        this.yukleniyorObjesi.sablonSil = true;

                        axios.post("{{ route('sablonSil') }}", {
                                id: sablon.id,
                            })
                            .then(response => {
                                this.yukleniyorObjesi.sablonSil = false;

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

                                this.sablonlariGetir();
                                this.geriAnasayfa();
                            })
                            .catch(error => {
                                this.yukleniyorObjesi.sablonSil = false;
                                this.uyariAc({
                                    baslik: 'Hata',
                                    mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response
                                        .data.hataKodu,
                                    tur: "error"
                                });
                                console.log(error);
                            });
                    };

                    Swal.fire({
                        title: "Uyarı",
                        text: `"${ sablon.sablonAdi }" adlı Şablonı silmek istediğinize emin misiniz?`,
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
                sablonEkleAc(duzenle = false) {
                    this.aktifSayfa = _.find(this.sayfalar, {
                        kod: "YENI_SABLON"
                    });

                    if (!duzenle) {
                        this.sablonSayfaEkle(0);
                    }
                },
                sablonSayfaEkle(i = this.editor.length){
                    return new Promise(async (resolve) => {
                        const editorId = `#editor-${i}`;
                        this.sablonSayfaSayisi++;

                        this.$nextTick(() => {
                            this.editor.push(
                                new Quill(editorId, {
                                    modules: {
                                        toolbar: this.customToolbar,
                                    },
                                    placeholder: 'Lütfen bir şablon oluşturunuz...',
                                    theme: 'snow'
                                })
                            );

                            if (this.yeniSablon.icerik[i] && this.yeniSablon.icerik[i] != "{}"){
                                this.editor[i].setContents(this.yeniSablon.icerik[i]);
                            }

                            this.editor[i].on('text-change', () => {
                                this.yeniSablon.icerik[i] = this.editor[i].getContents();
                                this.yeniSablon.icerik_html[i] = this.editor[i].root.innerHTML;
                            });

                            resolve();
                        });
                    });
                },
                sablonEkle() {
                    if (!this.yeniSablon.sablonAdi) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: 'Şablon adı boş olamaz!',
                            tur: "error"
                        });
                    }

                    if (!this.yeniSablon.tur) {
                        return this.uyariAc({
                            baslik: 'Hata',
                            mesaj: 'Şablon türü boş olamaz!',
                            tur: "error"
                        });
                    }


                    this.yukleniyorObjesi.sablonEkle = true;

                    const html = this.yeniSablon.icerik_html;
                    let kullanilabilirOgelerDizesi = html.reduce(function(prev, curr) {
                        return prev.concat(curr.match(/\[(.*?)\]/g));
                    }, []);
                    let kullanilabilirOgeler = [...new Set(kullanilabilirOgelerDizesi)];
                    const sablon = _.assignIn({}, this.yeniSablon);
                    sablon.icerik = JSON.stringify(sablon.icerik);
                    sablon.icerik_html = JSON.stringify(sablon.icerik_html);
                    sablon.kullanilabilirOgeler = JSON.stringify(kullanilabilirOgeler);

                    axios.post('/sablonEkle', {
                            sablon,
                        })
                        .then(response => {
                            this.yukleniyorObjesi.sablonEkle = false;

                            if (!response.data.durum) {
                                return this.uyariAc({
                                    baslik: 'Hata',
                                    mesaj: response.data.mesaj,
                                    tur: "error"
                                });
                            }

                            const sablon = response.data.sablon;

                            this.uyariAc({
                                toast: {
                                    status: true,
                                    message: response.data.mesaj,
                                },
                            });

                            this.sablonlariGetir();
                            this.geriAnasayfa();
                        })
                        .catch(error => {
                            this.yukleniyorObjesi.sablonEkle = false;
                            this.uyariAc({
                                baslik: 'Hata',
                                mesaj: error.response.data.mesaj + " - Hata Kodu: " + error.response.data
                                    .hataKodu,
                                tur: "error"
                            });
                            console.log(error);
                        });
                },
                sablonCogalt(sablon){
                    this.yeniSablon = _.cloneDeep(sablon);
                    delete this.yeniSablon.id;
                    this.yeniSablon.sablonAdi = this.yeniSablon.sablonAdi + " - Kopya";
                    this.sablonEkle();
                },
                turkceKarakterCevir(str) {
                    return str
                        .replace(/[Çç]/g, "C")
                        .replace(/[Ğğ]/g, "G")
                        .replace(/[İı]/g, "I")
                        .replace(/[Öö]/g, "O")
                        .replace(/[Şş]/g, "S")
                        .replace(/[Üü]/g, "U");
                }
            },
            computed:{
                sablonTur() {
                    return this.sablonTurleri.find(sablon => sablon.adi === this.yeniSablon.tur);
                }
            }
        };
    </script>
@endsection
