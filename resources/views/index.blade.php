@extends('layout') 
@section('content')
<div class="row doruk-content">
    <h4 style="color:#999"><i class="fa fa-home"></i> ANASAYFA</h4>
    <div class="card p-3" style="border-radius: 20px;">
        <div class="card-title">
            <h5><i class="fa fa-cogs"></i> Isıl İşlemler</h5>
        </div>
        <div class="card-body">
            <table id="tablo-isil-islem" class="table w-100 nowrap">
                <tbody>
                    <tr>
                        <td style="vertical-align: middle;">
                            <i class="mdi mdi-bell-outline" style="font-size: 40px;">
                            </i>
                        </td>
                        <td style="vertical-align: middle;" class="p-2">
                            <strong>A FİRMASI A.Ş.</strong>
                            <div class="float-end">
                                <span class="bg-pink badge p-1">KAMARALI FIRIN</span>
                                <span class="bg-danger badge p-1">1.ŞARJ</span>
                            </div>
                            <table class="table table-bordered table-sm table-hover table-striped table-active mt-2">
                                <thead>
                                    <tr>
                                        <th>Malzeme</th>
                                        <th>İst. Sertlik</th>
                                        <th>Kalite</th>
                                        <th>Sıcaklık</th>
                                        <th>Carbon</th>
                                        <th>Süre</th>
                                        <th>Ç.Sertliği</th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td>Karışık</td>
                                    <td>38-42</td>
                                    <td>41-40</td>
                                    <td>840</td>
                                    <td>40</td>
                                    <td>1.30</td>
                                    <td>45-50</td>
                                </tr>
                            </table>
                        </td>
                        <td style="vertical-align: middle;">
                            <i class="fa fa-toggle-off" aria-hidden="true" style="font-size:40px;"></i>
                        </td>
                        <td style="vertical-align: middle;">
                            <i class="fa fa-spinner" aria-hidden="true" style="font-size:32px;"></i>
                            <span style="font-size: 22px;margin-left: 20px;"> İşlemde</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle;">
                            <i class="mdi mdi-bell-outline" style="font-size: 40px;">
                            </i>
                        </td>
                        <td style="vertical-align: middle;" class="p-2">
                            <strong>A FİRMASI A.Ş.</strong>
                            <div class="float-end">
                                <span class="bg-pink badge p-1">KAMARALI FIRIN</span>
                                <span class="bg-danger badge p-1">1.ŞARJ</span>
                            </div>
                            <table class="table table-bordered table-sm table-hover table-striped table-active mt-2">
                                <thead>
                                    <tr>
                                        <th>Malzeme</th>
                                        <th>İst. Sertlik</th>
                                        <th>Kalite</th>
                                        <th>Sıcaklık</th>
                                        <th>Carbon</th>
                                        <th>Süre</th>
                                        <th>Ç.Sertliği</th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td>Karışık</td>
                                    <td>38-42</td>
                                    <td>41-40</td>
                                    <td>840</td>
                                    <td>40</td>
                                    <td>1.30</td>
                                    <td>45-50</td>
                                </tr>
                            </table>
                        </td>
                        <td style="vertical-align: middle;">
                            <i class="fa fa-toggle-on" aria-hidden="true" style="font-size:40px;"></i>
                        </td>
                        <td style="vertical-align: middle;">
                            <i class="fa fa-check-circle" aria-hidden="true" style="font-size:32px;"></i>
                            <span style="font-size: 22px;margin-left: 20px;"> Hazır</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection