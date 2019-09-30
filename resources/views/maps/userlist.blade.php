@extends('layouts.common_init')

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
            <h2 class="alert alert-heading py-4">
                【{{$em_rank_jp['name']}}】処方患者分布
                
            </h2>
            <span class="alert alert-warning mx-2">地図は位置情報が送信されたものだけ、処方情報は全員分表示されています。</span>
        </div>
        <div class="col-md-2 text-right">
            <a class="btn btn-sm btn-outline-info mx-2 px-2" href="{{route('home')}}">MENUへ戻る</a>
        </div>
    </div>
    <div class="row maparea">
        <div class="col-md-7" id="map">

        </div>
        <div class="col-md-5 presctiption_list">
            <h3>患者一覧</h3>
            @foreach($userinfo as $user_id => $profile)
            <p class="alert alert-heading my-2">{{$profile['user_name']}} {{$profile['user_age']}}歳（{{$profile['user_sex']}} ） </p>
            <div class="my-2">
                <table class="table table-sm table-striped　table-border">
                    <thead>
                        <tr>
                            @foreach(config('const.druginfo_fields_v0') as $key=>$label)
                            <th>{{$label}}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @for($p=0;$p<count($prescriptions[$user_id]);++$p)
                        @switch($prescriptions[$user_id][$p]['em_rank'])
                        @case('48H')
                        <tr class="bg-danger text-light">
                        @break
                        @case('1W')
                        <tr class="bg-warning">
                        @break
                        @default
                        <tr>
                        @break
                        @endswitch
                            @foreach(config('const.druginfo_fields_v0') as $key=>$label)
                            <td>{{$prescriptions[$user_id][$p][$key]}}</td>
                            @endforeach
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
@section('pageJs')
@endsection

@include('layouts.footer')
@section('js_config')
<script>
    //マーカーに表示したい対象の緯度経度とポップアップする名称を設定
    var markerList = [
        <?php echo $markerList;?>
    ];
    
    function init() {
        var map = L.map('map');
        L.tileLayer('https://cyberjapandata.gsi.go.jp/xyz/std/{z}/{x}/{y}.png', {
            attribution: "<a href='https://maps.gsi.go.jp/development/ichiran.html' target='_blank'>地理院タイル</a>"
        }).addTo(map);
        
        //マーカー全体が入るボックスを作る
        var bound = L.latLngBounds(markerList[0].pos, markerList[0].pos);
        
        //markerListの設定でマーカーを追加
        for (var num in markerList) {
            var mk = markerList[num];
            var popup = L.popup().setContent(mk.name);
            L.marker(mk.pos, { title: mk.name }).bindPopup(popup).addTo(map);
            //マーカー全体が入るボックスを広げる
            bound.extend(mk.pos);
        }
        //マーカー全体が入るように地図範囲を設定する
        map.fitBounds(bound);
    }
</script>
@endsection