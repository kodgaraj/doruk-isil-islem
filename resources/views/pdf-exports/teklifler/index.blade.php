@extends('layout-bos')

@section('content')
    <div ref="siparisRaporlama" style="background: white; color: black;">
        <!-- 1. sayfa -->
        <div class="printable-page" id="page-1">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-4">
                    <div>
                        <img style="width: 100%" src="/img/doruk-logo.png" />
                    </div>
                </div>
                <div class="col-4 text-center">
                    <b><h2>DORUK ISIL İŞLEM FİYAT TEKLİFİ</h2></b>
                </div>
                <div class="col-4">
                    <div>
                        <img style="width: 100%" src="/img/doruk-sertifika.png" />
                    </div>
                </div>
                <div class="col-12 text-end">
                    <div>
                        <h6><b>TARİH:</b> ${ rapor.tarih }</h6>
                    </div>
                </div>

                <!-- Firma -->
                {{-- <div class="col-2 text-start">
                    <h6><b>Firma:</b></h6>
                </div>
                <div class="col-10 text-start">
                    <h6>${ rapor.firma }</h6>
                </div> --}}
                <div class="col-12 text-start">
                    <h6><b>Firma:</b> ${ rapor.firma }</h6>
                </div>

                <!-- Yetkili -->
                <div class="col-6 text-start">
                    <h6><b>Yetkili:</b> ${ rapor.yetkili }</h6>
                </div>

                <!-- Telefon -->
                <div class="col-6 text-start">
                    <h6><b>Telefon:</b> ${ rapor.telefon }</h6>
                </div>

                <!-- Adres -->
                <div class="col-12 text-start">
                    <h6><b>Adres:</b> ${ rapor.adres }</h6>
                </div>

                <!-- E-posta -->
                <div class="col-12 text-start">
                    <h6><b>E-posta:</b> ${ rapor.eposta }</h6>
                </div>

                <hr />

                <div class="px-5">
                    <div class="col-12" style="border-bottom: 1px solid #dddddd;">
                        <div class="row d-flex align-items-center">
                            <div class="col-8 text-start">
                                <b>GELİŞ TARİHİ:</b>
                            </div>
                            <div class="col-8 text-start">
                                <span>${ rapor.gelisTarihi }</span>
                            </div>
                            <div class="col-8 text-start">
                                <span>${ rapor.gelisTarihi }</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. sayfa -->
        <div class="printable-page" id="page-2">
            <div class="col-12 text-center mt-4">
                <b>ISIL İŞLEM SONRASI ÖLÇÜLEN SERTLİK DEĞERLERİ</b>
            </div>
            <hr />
            <div class="px-5 my-3">
                <div class="col-12">
                    <div class="row d-flex align-items-center">
                        <div class="col-6 text-center">
                            <b>Ünal SANDAL</b>
                            <br />
                            <b>Metalurji ve Malzeme Mühendisi</b>
                        </div>
                        <div class="col-6 text-center">
                            <img height="250" width="250" style="object-fit: contain;" src="/img/doruk-unal-imza.jpg" />
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
@endsection

@section("script")
<script>
  let mixinApp = {};
</script>
@endsection