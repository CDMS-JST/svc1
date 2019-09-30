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
            <h2 class="alert alert-heading py-4">分布</h2>
        </div>
        <div class="col-md-2 text-right">
            <a class="btn btn-sm btn-outline-info" href="{{route('user_info')}}">戻る</a>
        </div>
    </div>
    <div class="row maparea">
        <div class="col-12" id="map">

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