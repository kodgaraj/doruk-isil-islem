@extends('layout') 
@section('content')
<div class="row doruk-content">
    <h4 style="color:#999"><i class="fa fa-home"></i> ANASAYFA</h4>
    <div class="col-12 col-sm-4">
        <div class="card" @click="siparisSayfasiAc()">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="avatar-sm font-size-20 me-3">
                        <span class="avatar-title bg-soft-primary text-primary rounded">
                            <i class="mdi mdi-tag-plus-outline"></i>
                        </span>
                    </div>
                    <div class="flex-1">
                        <div class="font-size-16 mt-2">Siparişler</div>
                    </div>
                </div>
                <h4 class="mt-4">1,368</h4>
                <div class="row">
                    <div class="col-7">
                        <p class="mb-0"><span class="text-success me-2"> 0.28% <i
                                    class="mdi mdi-arrow-up"></i> </span></p>
                    </div>
                    <div class="col-5 align-self-center">
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 62%"
                                aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card" @click="kullanicilarSayfasiAc()">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="avatar-sm font-size-20 me-3">
                        <span class="avatar-title bg-soft-primary text-primary rounded">
                            <i class="mdi mdi-account-multiple-outline"></i>
                        </span>
                    </div>
                    <div class="flex-1">
                        <div class="font-size-16 mt-2">Kullanıcılar</div>

                    </div>
                </div>
                <h4 class="mt-4">2,456</h4>
                <div class="row">
                    <div class="col-7">
                        <p class="mb-0"><span class="text-success me-2"> 0.16% <i
                                    class="mdi mdi-arrow-up"></i> </span></p>
                    </div>
                    <div class="col-5 align-self-center">
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 62%"
                                aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card" @click="isilIslemSayfasiAc()">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="avatar-sm font-size-20 me-3">
                        <span class="avatar-title bg-soft-primary text-primary rounded">
                            <i class="mdi mdi-stove"></i>
                        </span>
                    </div>
                    <div class="flex-1">
                        <div class="font-size-16 mt-2">İşlemler</div>

                    </div>
                </div>
                <h4 class="mt-4">2,456</h4>
                <div class="row">
                    <div class="col-7">
                        <p class="mb-0"><span class="text-success me-2"> 0.16% <i
                                    class="mdi mdi-arrow-up"></i> </span></p>
                    </div>
                    <div class="col-5 align-self-center">
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 62%"
                                aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h4 class="card-title mb-4">Son Isıl İşlemler</h4>
                    </div>
                    <div class="col-4 text-end ">
                        <button @click="isilIslemSayfasiAc()" class="btn btn-primary btn-sm">Tümünü Gör</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-centered">
                        <thead>
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Id no.</th>
                                <th scope="col">Billing Name</th>
                                <th scope="col">Amount</th>
                                <th scope="col" colspan="2">Payment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>15/01/2020</td>
                                <td>
                                    <a href="#" class="text-body fw-medium">#SK1235</a>
                                </td>
                                <td>Werner Berlin</td>
                                <td>$ 125</td>
                                <td><span class="badge badge-soft-success font-size-12">Paid</span>
                                </td>
                                <td><a href="#" class="btn btn-primary btn-sm">View</a></td>
                            </tr>
                            <tr>
                                <td>16/01/2020</td>
                                <td>
                                    <a href="#" class="text-body fw-medium">#SK1236</a>
                                </td>
                                <td>Robert Jordan</td>
                                <td>$ 118</td>
                                <td><span class="badge badge-soft-danger font-size-12">Chargeback</span>
                                </td>
                                <td><a href="#" class="btn btn-primary btn-sm">View</a></td>
                            </tr>
                            <tr>
                                <td>17/01/2020</td>
                                <td>
                                    <a href="#" class="text-body fw-medium">#SK1237</a>
                                </td>
                                <td>Daniel Finch</td>
                                <td>$ 115</td>
                                <td><span class="badge badge-soft-success font-size-12">Paid</span>
                                </td>
                                <td><a href="#" class="btn btn-primary btn-sm">View</a></td>
                            </tr>
                            <tr>
                                <td>18/01/2020</td>
                                <td>
                                    <a href="#" class="text-body fw-medium">#SK1238</a>
                                </td>
                                <td>James Hawkins</td>
                                <td>$ 121</td>
                                <td><span class="badge badge-soft-warning font-size-12">Refund</span>
                                </td>
                                <td><a href="#" class="btn btn-primary btn-sm">View</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <ul class="pagination pagination-rounded justify-content-center mb-0">
                        <li class="page-item">
                            <a class="page-link" href="#">Previous</a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item active"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        let mixinApp = {
            methods: {
                siparisSayfasiAc: function () {
                    window.location.href = "{{ route('siparis-formu') }}";
                },
                kullanicilarSayfasiAc: function () {
                    console.log('kullanicilarSayfasiAc');
                },
                isilIslemSayfasiAc: function () {
                    window.location.href = "{{ route('isil-islemler') }}";
                }
            }
        };
    </script>
@endsection