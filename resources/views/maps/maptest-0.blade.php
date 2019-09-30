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
map.setView([35.3622222, 138.7313889], 10);
</script>
@endsection
