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
            <canvas id="myChart" class="chartarea"></canvas>
        </div>
    </div>
</div>
@endsection
@section('pageJs')
@endsection

@include('layouts.footer')
@section('js_config')
<script>
    var ctx = document.getElementById('myChart').getContext('2d');
var chart = new Chart(ctx, {
    // 作成したいチャートのタイプ
    type: 'line',

    // データセットのデータ
    data: {
        labels: ["1月", "2月", "3月", "4月", "5月", "6月", "7月"],
        datasets: [{
            label: "初めてのデータセット",
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
            data: [0, 10, 5, 2, 20, 30, 45],
        }]
    },

    // ここに設定オプションを書きます
    options: {}
});
</script>
@endsection

