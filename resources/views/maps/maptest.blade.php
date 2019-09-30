@extends('layouts.common')

@section('title', '佐賀県交通情報オープンデータ利用ツール')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
<link href="/css/page.css" rel="stylesheet">
@endsection

@include('layouts.head_config')

@section('content')
<div class="container-fluid maparea">
    <div id="map"></div>
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
    map.setView([33.284282, 130.266916], 12);
    
    //マーカーに表示したい対象の緯度経度とポップアップする名称を設定
    var markerList = [
        <?php echo $markerList; ?>
    ];
    
    //マーカー全体が入るボックスを作る
    var bound = L.latLngBounds(markerList[0].pos, markerList[0].pos);
    
    // markerlistの全マーカーを表示
    for (var num in markerList) {
        var mk = markerList[num];
        var popup = L.popup().setContent(mk.name);
        L.marker(mk.pos, { title: mk.name }).bindPopup(popup).addTo(map);
        //マーカー全体が入るボックスを広げる
        bound.extend(mk.pos);
    }
    
    //マーカー全体が入るように地図範囲を設定する
    map.fitBounds(bound);
    

</script>
@endsection
