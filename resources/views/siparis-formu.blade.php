@extends('layout') 
@section('content')
<div class="row doruk-content">
    <h4 style="color:#999"><i class="fab fa-wpforms"> </i> SİPARİŞ FORMU</h4>
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">SİPARİŞ TAKİP FORMU</h4>
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
                            <th colspan="2">No</th>
                            <th>Malzeme</th>
                            <th>Miktar KG</th>
                            <th>Adet</th>
                            <th>Kalite</th>
                            <th>Yapılacak İşlem</th>
                            <th>İstenilen Sertlik</th>
                        </thead>
                        <tbody id="urun-satir-ekle">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6">
                                    <a class="btn btn-secondary text-white" id="urun-ekle">+
                                        Ekle</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
            </div>
        </div>
    </div>
    <!-- end col -->
</div>
@endsection
