@extends('layout-bos')

@section('content')
    <div v-html="html" class="p-0"></div>

    <div ref="teklif" style="display: none; background: white; color: black; font-size: 12px;">
        <!-- 1. sayfa -->
        <div class="printable-page" id="page-1">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-4">
                    <div>
                        <img style="width: 100%" src="/img/doruk-logo.png" />
                    </div>
                </div>
                <div class="col-4 text-center">
                    <b><h3>DORUK ISIL İŞLEM FİYAT TEKLİFİ</h3></b>
                </div>
                <div class="col-4">
                    <div>
                        <img style="width: 100%" src="/img/doruk-sertifika.png" />
                    </div>
                </div>
                <div class="col-12 text-end">
                    <div>
                        <h6><b>TARİH:</b> ${ data.tarih }</h6>
                    </div>
                </div>

                <!-- Firma -->
                {{-- <div class="col-2 text-start">
                    <h6><b>Firma:</b></h6>
                </div>
                <div class="col-10 text-start">
                    <h6>${ data.firma }</h6>
                </div> --}}
                <div class="col-12 text-start">
                    <h6><b>Firma:</b> ${ data.firma }</h6>
                </div>

                <!-- Yetkili -->
                <div class="col-6 text-start">
                    <h6><b>Yetkili:</b> ${ data.yetkili }</h6>
                </div>

                <!-- Telefon -->
                <div class="col-6 text-start">
                    <h6><b>Telefon:</b> ${ data.telefon }</h6>
                </div>

                <!-- Adres -->
                <div class="col-12 text-start">
                    <h6><b>Adres:</b> ${ data.adres }</h6>
                </div>

                <!-- E-posta -->
                <div class="col-12 text-start">
                    <h6><b>E-posta:</b> ${ data.eposta }</h6>
                </div>

                <hr class="m-0" />

                <div class="col-12 printable-area-content">
                    <div class="row d-flex align-items-center">
                        <% _.forEach(data.icerikler, function (icerik) { %>
                            <div class="col-12 my-1">
                                <div class="row d-flex align-items-center">
                                    <div class="col-8 text-start">
                                        <span><%- icerik.ad %></span>
                                    </div>
                                    <div class="col-2 text-end">
                                        <span><%- icerik.fiyat %></span>
                                    </div>
                                    <div class="col-1 text-end pe-0">
                                        <span><%- icerik.paraBirimi %></span>
                                    </div>
                                    <div class="col-1 text-start ps-1">
                                        <% if (icerik.olcumTuru) { %>
                                            /
                                            <span><%- icerik.olcumTuru %></span>
                                        <% } %>
                                    </div>
                                </div>
                            </div>
                            <hr class="m-0" />
                        <% }); %>
                    </div>
                </div>

                <hr class="m-0" />

                <div class="col-12">
                    <b>* Fiyat teklifimizi onaylamanız durumunda kaşe/imza yapmanızı önemle rica ederiz.</b>
                </div>

                <div class="col-12 mt-2">
                    <div class="row text-center">
                        <div class="col-4">
                            Müşteri Onay Kaşe/İmza
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <div class="col-12">
                                    <b>Hazırlayan</b>
                                </div>
                                <div class="col-12">
                                    Aziz KALEM
                                </div>
                                <div class="col-12 text-muted small">
                                    Metalurji ve Malzeme Mühendisi
                                </div>
                                <div class="col-12">
                                    <img style="width: 100%" src="/img/doruk-aziz-imza.png" />
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <div class="col-12">
                                    <b>Onaylayan</b>
                                </div>
                                <div class="col-12">
                                    Gökhan ÇELİK
                                </div>
                                <div class="col-12 text-muted small">
                                    İnşaat Mühendisi
                                    <br />
                                    Genel Müdür
                                </div>
                                <div class="col-12">
                                    <img style="width: 100%" src="/img/doruk-gokhan-imza.png" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div>
                        <img style="width: 100%" src="/img/doruk-belge-alt-bilgi.jpg" />
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. sayfa -->
        <div class="printable-page" id="page-2">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-4">
                    <div>
                        <img style="width: 100%" src="/img/doruk-logo.png" />
                    </div>
                </div>
                <div class="col-4 text-center">
                    <b><h3>DORUK ISIL İŞLEM FİYAT TEKLİFİ</h3></b>
                </div>
                <div class="col-4">
                    <div>
                        <img style="width: 100%" src="/img/doruk-sertifika.png" />
                    </div>
                </div>
                <div class="col-12 text-end">
                    <div>
                        <h6><b>TARİH:</b> ${ data.tarih }</h6>
                    </div>
                </div>

                <!-- Firma -->
                {{-- <div class="col-2 text-start">
                    <h6><b>Firma:</b></h6>
                </div>
                <div class="col-10 text-start">
                    <h6>${ data.firma }</h6>
                </div> --}}
                <div class="col-12 text-start">
                    <h6><b>Firma:</b> ${ data.firma }</h6>
                </div>

                <!-- Yetkili -->
                <div class="col-6 text-start">
                    <h6><b>Yetkili:</b> ${ data.yetkili }</h6>
                </div>

                <!-- Telefon -->
                <div class="col-6 text-start">
                    <h6><b>Telefon:</b> ${ data.telefon }</h6>
                </div>

                <!-- Adres -->
                <div class="col-12 text-start">
                    <h6><b>Adres:</b> ${ data.adres }</h6>
                </div>

                <!-- E-posta -->
                <div class="col-12 text-start">
                    <h6><b>E-posta:</b> ${ data.eposta }</h6>
                </div>

                <hr class="m-0" />

                <div class="col-12 printable-area-content">
                    <div class="row">
                        <div class="col-12">
                            1 - Fiyatlarımıza KDV dahil değildir.
                        </div>
                        <div class="col-12">
                            2 - Tek seferde gelen ürün/işlem fatura tutarının <strong class="text-danger"><u>600,00 $</u></strong> altında kalması halinde <strong class="text-danger"><u>şarj bedeli</u></strong> uygulanır.
                        </div>
                        <div class="col-12">
                            3 - Gelen malzemelerde kalite veya istenen sertlik değerleri farklı olması halinde her ürün grubu için minimum iş bedeli uygulanır.
                        </div>
                        <div class="col-12">
                            4 - Özel ürünlerde ve / veya özel proses gerektiren ürünlerde fiyatlar ayrıca görüşülecektir.
                        </div>
                        <div class="col-12">
                            5 - Ödeme: Ürün tesliminde <strong class="text-danger"><u>nakit, maksimum 30 günlük çek</u></strong> ile ya da <strong class="text-danger"><u>30 gün</u></strong> içinde banka havalesi şeklinde yapılacaktır.
                        </div>
                        <div class="col-12">
                            6 - Teklifin geçerlilik süresi <strong class="text-danger"><u>15 gün</u></strong>dür. <strong class="text-danger"><u>15 gün</u></strong> içinde teklifin onaylanması durumunda; yukarıdaki fiyatlar <strong class="text-danger"><u>${ data.gecerlilikTarihi }</u></strong> tarihine kadar geçerli olacaktır. <strong class="text-danger"><u>15 gün</u></strong> içinde teklifin onaylanmaması durumunda ise tekrar fiyat teklifi istemenizi rica ederiz.
                        </div>
                        <div class="col-12">
                            7 - Ürün teslim (termin) süresi, ürünün fimamıza tesliminden itibaren <strong class="text-danger"><u>5+1</u></strong> iş günüdür
                        </div>
                        <div class="col-12">
                            8 - Firmamızın hazırlamış olduğu fiyat teklifi tarafınızdan imza edilmemiş / onaylanmamış olsa dahi ısıl işleme tabi tutulacak ürünün / ürünlerin tesisimize gönderilmesi durumunda, fiyat teklifi ile sözleşme şartları kabul edilmiş sayılır.
                        </div>
                        <div class="col-12">
                            9 - Isıl İşleme tabi tutulan ürünlerinizi teslim etmeyi öngördüğümüz sürelere, enerji kesintisi, arıza ve benzeri sorunlar dışında, uyacağımızı taahüt ederiz. Teknik sorunlar da mücbir sebep olarak gecikmeye neden olabilecektir. Bu durumlarda tarafınıza bilgilendirme yapılacaktır.
                        </div>
                        <div class="col-12">
                            10 - Ürünlere uygulanması istenilen ısıl işlem operasyonu ve özellikleri ile ürün / ürünlere ait teknik resmi, var ise ısıl işlem şartnamesini, ürünün teslimi ile birlikte tarafımıza iletilmelidir. Bu bilgi ve belgelerin teslim edilmemesinden meydana gelecek her türlü zarardan firmamız sorumlu tutulamayacaktır.
                        </div>
                        <div class="col-12">
                            11 - Malzeme bilgilerinin sertifikasının ürünle beraber gönderilmediği durumda spesifikasyon limitleri dışında elde edilen sertlik ve sertlik derinliğinden ve ölçülerden firmamız sorumlu değildir.
                        </div>
                        <div class="col-12">
                            12 - İletilen bilgiler hatalı, yanlış ve eksik olması nedenleriyle ısıl işlem başarısız olsa dahi, ısıl işlem ücretinin tamamı ödenecektir.
                        </div>
                        <div class="col-12">
                            13 - Firmamız bünyesinde Isıl işleme tabi tutulan ürünlerin sertlik değerleri, tarafınızdan iletilen bilgilerde belirttiğiniz sertlik değer aralığı dışında kaldığında hiçbir bedel gözetmeksizin ısıl işlemin tekrarını gerçekleştirerek belirtilen sertlik değer aralığına getireleceğini kabul ve taahhüt ederiz.
                        </div>
                        <div class="col-12">
                            14 - Boyutsal kararlılık veya yüzey durumuna ilişkin talepler irsaliyelerde belirtilmelidir veya irsaliye ile ulaştırılmalıdır. Özellikle kaynaklanmış veya lehimlenmiş ve içinde boşluklar olan materyallere ilişkin bilgi vermelidir. Teslim edilen ürünleri ebat, ağırlık ve miktar yönünden kontrole tabi tutabiliriz. Ancak, teslim edilen ürünlerin kalitesi açısından kontrol görevimiz bulunmamaktadır. Bu kapsamda teslim edilen ürünlerin doğru ve elverişli olduğu kabul edilir. Bu kapsamda yükümlülüklerinizi yerine getirmemeniz veya eksik bırakmanızdan meydana gelecek zararlarda sorumluk size aittir. Açık talebiniz olması halinde ve masrafını karşılamanız şartıyla kontrol işlemini sizin adınıza yaptırabiliriz. Teslim edilen ürünlerdeki gizli kusurlardan kaynaklanan zararlardan firmamız sorumlu değildir.
                        </div>
                        <div class="col-12">
                            15 - Ürün üzerinde bulunan köşeler, kesit farklılıkları, kenara yakın ve/veya kör delikler, kama kanalları, gerekli şekilde verilmemiş radyuslar, damgalar ve benzeri noktalardan kaynaklanan çatlamalar ve yine hammaddede bulunan hatalardan kaynaklanan çatlama veya deformasyonlardan firmamız sorumlu tutulmayacaktır.
                        </div>
                    </div>
                </div>

                <hr class="m-0" />

                <div class="col-12">
                    <b>* Fiyat teklifimizi onaylamanız durumunda kaşe/imza yapmanızı önemle rica ederiz.</b>
                </div>

                <div class="col-12 mt-2">
                    <div class="row text-center">
                        <div class="col-4">
                            Müşteri Onay Kaşe/İmza
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <div class="col-12">
                                    <b>Hazırlayan</b>
                                </div>
                                <div class="col-12">
                                    Aziz KALEM
                                </div>
                                <div class="col-12 text-muted small">
                                    Metalurji ve Malzeme Mühendisi
                                </div>
                                <div class="col-12">
                                    <img style="width: 100%" src="/img/doruk-aziz-imza.png" />
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <div class="col-12">
                                    <b>Onaylayan</b>
                                </div>
                                <div class="col-12">
                                    Gökhan ÇELİK
                                </div>
                                <div class="col-12 text-muted small">
                                    İnşaat Mühendisi
                                    <br />
                                    Genel Müdür
                                </div>
                                <div class="col-12">
                                    <img style="width: 100%" src="/img/doruk-gokhan-imza.png" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div>
                        <img style="width: 100%" src="/img/doruk-belge-alt-bilgi.jpg" />
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. sayfa -->
        <div class="printable-page" id="page-2">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-4">
                    <div>
                        <img style="width: 100%" src="/img/doruk-logo.png" />
                    </div>
                </div>
                <div class="col-4 text-center">
                    <b><h3>DORUK ISIL İŞLEM FİYAT TEKLİFİ</h3></b>
                </div>
                <div class="col-4">
                    <div>
                        <img style="width: 100%" src="/img/doruk-sertifika.png" />
                    </div>
                </div>
                <div class="col-12 text-end">
                    <div>
                        <h6><b>TARİH:</b> ${ data.tarih }</h6>
                    </div>
                </div>

                <!-- Firma -->
                {{-- <div class="col-2 text-start">
                    <h6><b>Firma:</b></h6>
                </div>
                <div class="col-10 text-start">
                    <h6>${ data.firma }</h6>
                </div> --}}
                <div class="col-12 text-start">
                    <h6><b>Firma:</b> ${ data.firma }</h6>
                </div>

                <!-- Yetkili -->
                <div class="col-6 text-start">
                    <h6><b>Yetkili:</b> ${ data.yetkili }</h6>
                </div>

                <!-- Telefon -->
                <div class="col-6 text-start">
                    <h6><b>Telefon:</b> ${ data.telefon }</h6>
                </div>

                <!-- Adres -->
                <div class="col-12 text-start">
                    <h6><b>Adres:</b> ${ data.adres }</h6>
                </div>

                <!-- E-posta -->
                <div class="col-12 text-start">
                    <h6><b>E-posta:</b> ${ data.eposta }</h6>
                </div>

                <hr class="m-0" />

                <div class="col-12 printable-area-content">
                    <div class="row">
                        <div class="col-12">
                            16 - Isıl işlem uygulanan ürünlerde asgari dahi olsa distorsiyon - boyutsal ölçü değişikliği olabilir. Bu yüzden ısıl işleme tabi tutulacak ürünler nihai ölçüye getirilmeden, taşlama öncesi ölçüleriyle teslim edilmelidir. Ürünlerin çapı ve boyuna göre taşlama payı değişebilir. Bu nedenle ürünler teslim edilirken taşlama payı belirtilmeli veya taşlama payı ne kadar bırakalım diye sorulmalıdır. <strong>AKSİ DURUMDA SORUMLULUK FİRMAMIZDA OLMAYACAKTIR.</strong>
                        </div>
                        <div class="col-12">
                            17 - Firmamız bünyesinde ısıl işleme tabi tutulan ürünlerin takribi %10 oranında sertlik kontrolü yapılacağını belirtiriz. Ancak çok küçük ve yüksek adetli ürünlerde ise her bir kilogram ürün için 1 adet numuneye sertlik kontrolü yapılacaktır.
                        </div>
                        <div class="col-12">
                            18 - Isıl işlem sonrasında ürünlere uygulatılacak ek işlemler (taşlama, kaynak, tornalama, ısıl işlem kaplama vb.) sonrası parçada ortaya çıkabilecek hasarlardan firmamız sorumlu tutulmayacaktır.
                        </div>
                        <div class="col-12">
                            19 - Isıl işleme tabi tutulan üründen dolayı, üçüncü kişilerin herhangi bir şekilde zarar görmesi halinde bu zararlardan firmamız sorumlu tutulamaz. Tarafınızdan üçüncü kişilere yapılan ödemeler, firmamıza rücu edilemez.
                        </div>
                        <div class="col-12">
                            20 - Isıl işlemi tamamlanan ürünler <strong class="text-danger"><u>7 (yedi) gün</u></strong> içerisinde teslim alınmalıdır.
                        </div>
                        <div class="col-12">
                            21 - Ürünlerinize ısıl işlemi uygulayabilmemiz için özel olarak imal edilmesi ve / veya satın alınması gereken aparatların olması durumunda; talep ettiğiniz takdirde, bu özel aparatların bedeli tarafınıza ait olacaktır.
                        </div>
                        <div class="col-12">
                            22 - Teslime hazır ürünlerin yazılı ya da e-posta ile bildiriminden sonra <strong class="text-danger"><u>15 gün</u></strong> içerisinde teslim alınmamasından dolayı oluşabilecek zararlardan firmamız sorumlu tutulamaz. <strong class="text-danger"><u>30 gün</u></strong>den daha uzun teslim alınmayan ürünlere depolama ücreti uygulanacaktır.
                        </div>
                        <div class="col-12">
                            23 - Ödemeler, faturaları alınır alınmaz herhangi bir (EK) indirime tabi olmaksızın (anlaşılan şekilde / vadede) ödenecektir. Ödemelerinizde gecikme olur ise, yıllık olarak Türkiye Merkez Bankası reeskont faizi uygulanır.
                        </div>
                        <div class="col-12">
                            24 - Ödemelerinizi, ürün tesliminde nakit olarak veya banka havalesi ile yapabilirsiniz. Vadeli anlaşmalarda vade tarihi en son ödeme günüdür.
                        </div>
                        <div class="col-12">
                            25 - Anlaşılan şekilde / vadede ödeme yapılmaması durumunda, herhangi bir görüşmeye gerek olmadan, yine teklifinde belirttiği faiz oranları üzerinden vade farkı faturası kesilecektir.
                        </div>
                        <div class="col-12">
                            26 - Müşteri tarafından verilen çek, vadeli anlaşmalarda vade tarihini geçemez. Vade tarihinden ileri tarihli çek verilmesi durumunda müşteri, çek miktarının <strong class="text-danger"><u>%8</u></strong> kadarının (çek işletme maliyeti olarak) eksik tahsil edilmiş olduğunu kabul ve taahhüt eder.
                        </div>
                        <div class="col-12">
                            27 - Tesisimize gönderilen ürünlere ait tarafımıza iletilen tüm bilgiler ve dokümantasyon eksiksiz ve doğru olmasına rağmen tesisimiz bünyesindeki ekipmanlardan ve / veya firmamız çalışanlarından kaynaklı hatalardan ve ısıl işlemin yanlış yapılmasından dolayı ürünlerde oluşabilecek zararlarda ürün hammadde bedelini karşılayacağımızı kabul ve taahhüt ederiz. Ürün işleme ve işçilik ücretlerinin sorumluluğu ve taahhüdü tarafınıza aittir.
                        </div>
                    </div>
                </div>

                <hr class="m-0" />

                <div class="col-12">
                    <b>* Fiyat teklifimizi onaylamanız durumunda kaşe/imza yapmanızı önemle rica ederiz.</b>
                </div>

                <div class="col-12 mt-2">
                    <div class="row text-center">
                        <div class="col-4">
                            Müşteri Onay Kaşe/İmza
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <div class="col-12">
                                    <b>Hazırlayan</b>
                                </div>
                                <div class="col-12">
                                    Aziz KALEM
                                </div>
                                <div class="col-12 text-muted small">
                                    Metalurji ve Malzeme Mühendisi
                                </div>
                                <div class="col-12">
                                    <img style="width: 100%" src="/img/doruk-aziz-imza.png" />
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <div class="col-12">
                                    <b>Onaylayan</b>
                                </div>
                                <div class="col-12">
                                    Gökhan ÇELİK
                                </div>
                                <div class="col-12 text-muted small">
                                    İnşaat Mühendisi
                                    <br />
                                    Genel Müdür
                                </div>
                                <div class="col-12">
                                    <img style="width: 100%" src="/img/doruk-gokhan-imza.png" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div>
                        <img style="width: 100%" src="/img/doruk-belge-alt-bilgi.jpg" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("script")
    <script>
        let mixinApp = {
            data() {
                return {
                    data: null,
                    html: "",
                };
            },
            beforeMount() {
                const url = new URL(location.href);
                const searchParams3 = new URLSearchParams(url.search);
                const encodedData = searchParams3.get('q');

                if (encodedData) {
                    this.data = JSON.parse(decodeURIComponent(encodedData));
                }

                console.log(this.data);
            },
            mounted() {
                if (this.data) {
                    this.teklifAlanlariDoldur();
                }
            },
            methods: {
                teklifAlanlariDoldur() {
                    console.log("ALANLAR DOLDURULUYOR");

                    const cloneRaporArea = this.$refs.teklif.cloneNode(true);
                    cloneRaporArea.style.display = "block";
                    cloneRaporArea.style.background = "white";

                    const compiled = _.template(
                        this.decodeHTMLEntities(
                            cloneRaporArea.outerHTML
                        )
                    );
                    this.html = compiled({
                        data: this.data,
                    });
                },
                decodeHTMLEntities(text) {
                    let textArea = document.createElement('textarea');
                    textArea.innerHTML = text;
                    return textArea.value;
                },
            }
        };
    </script>
@endsection

@section('style')
    <link rel="stylesheet" href="/css/print.css">

    <style>
        body {

        }

        .printable-page {
            margin: 0 !important;
            padding: 10px !important;
        }

        .printable-area-content {
            min-height: 580px;
            width: 100% !important;
        }
    </style>
@endsection