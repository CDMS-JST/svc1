@extends('layouts.common')

@section('title', '災害および症状急変時医療関連情報提供サービス')
@section('keywords', '災害支援,患者支援,薬剤情報')
@section('description', '災害および症状急変時医療関連情報提供サービス')
@section('pageCss')
<link href="/css/page.css" rel="stylesheet">
@endsection

@include('layouts.head_config')

@section('content')
<div class="container-fluid maparea">
    <div class="row">
        <div class="col-md-10">
            <h2 class="alert alert-heading py-4">{{$userinfo['user_name']}}さん
                （
                {{$userinfo['age']}}歳
                @isset(config('const.sexes')[$userinfo['user_sex']])
                ・{{config('const.sexes')[$userinfo['user_sex']]}}
                @endisset
                ）の情報</h2>
        </div>
        <div class="col-md-2 text-right">
            <a class="btn btn-sm btn-outline-info" href="{{route('get_kisyo')}}">気象情報確認</a>
            <a class="btn btn-sm btn-outline-info" href="{{route('user_info')}}">戻る</a>
        </div>
    </div>
    <div class="row maparea">
        <div class="col-md-7" id="map">

        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><h3>薬剤情報</h3></div>
                <div class="card-body">
                    @if(count($drugs)<1)
                    薬剤情報は登録されていません。
                    @else
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                @foreach(config('const.list_drugs_fields') as $label)
                                <th class="text-center text-nowrap">{{$label}}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @for($d=1;$d<=count($drugs);++$d)
                            @switch($drugs[$d]['em_rank'])
                            @case('48H')
                            <tr class="bg-alert">
                                @break
                                @case('1W')
                            <tr class="bg-warning">
                                @break
                                @default
                            <tr>
                                @break
                                @endswitch

                                @foreach(config('const.list_drugs_fields') as $key=>$label)
                                @switch($key)
                                @case('drug_name')
                                <td>
                                    {{$drugs[$d][$key]}}
                                </td>
                                @break
                                @default
                                <td class="text-center">
                                    {{$drugs[$d][$key]}}
                                </td>
                                @break
                                @endswitch

                                @endforeach
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header"><h3>中核医療機関までの距離</h3></div>
                <div class="card-body">
                    <table class="table table-sm table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>医療機関名称</th>
                                <th class="text-center">最終確認位置からの距離(km)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(config('const.mainhosps') as $hospkey=>$hospinfo)
                            <tr>
                                <td>{{$hospinfo['name']}}</td>
                                <td class="text-center">{{$userinfo['distance'][$hospkey]}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('pageJs')
@endsection

@include('layouts.footer')
@section('js_config')
<script>
    var map = L.map('map');
    L.tileLayer('https://cyberjapandata.gsi.go.jp/xyz/std/{z}/{x}/{y}.png', {
        attribution: "<a href='https://maps.gsi.go.jp/development/ichiran.html' target='_blank'>地理院タイル</a>"
    }).addTo(map);
    map.setView(<?php echo $userinfo['latlng']; ?>, 16);
    //マーカー追加
    var bsIcon = L.icon({
        iconUrl: '/svc1/img/pinw48h99.png',
        iconRetinaUrl: '/svc1/img/pinw48h99.png',
        iconSize: [48, 99],
        iconAnchor: [24, 53],
        popupAnchor: [0, -53],
    });
    var mapMarker = L.marker(<?php echo $userinfo['latlng']; ?>, {icon: bsIcon}).addTo(map);
</script>
@endsection