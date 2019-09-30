@extends('layouts.common')

@section('title', '災害および症状急変時医療関連情報提供サービス')
@section('keywords', '災害支援,患者支援,薬剤情報')
@section('description', '災害および症状急変時医療関連情報提供サービス')
@section('pageCss')
<link href="/css/page.css" rel="stylesheet">
@endsection

@include('layouts.head_config')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-end">
        <a class="btn btn-sm btn-outline-info mx-2 px-2" href="{{route('home')}}">MENUへ戻る</a>
    </div>
    <div class="row justify-content-center py-4">
        <h1>アプリ利用者 サマリ</h1>
    </div>
    <div class="row justify-content-center">
        @if(Auth::user()->isadmin===1)
        <div class="col-md-4">
            <h3>性別</h3>
            <table class="table table-sm table-striped　table-border">
                <thead>
                    <tr>
                        <th class="text-center">性別</th><th class="text-center">利用者数</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sexes as $sex)
                    @isset(config('const.sexes')[$sex->user_sex])
                    <tr>
                        <td class="text-center">{{config('const.sexes')[$sex->user_sex]}}</td>
                        <td class="text-center">{{$sex->count_by_sex}}</td>
                    </tr>
                    @endisset
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-md-8 my-4">
            <canvas id="myChart" class="chartarea"></canvas>
        </div>
        <div class="col-md-2">
            <h3>年齢</h3>
            <table class="table table-sm table-striped　table-border">
                <thead>
                    <tr>
                        <th class="text-center">年齢</th><th class="text-center">利用者数</th>
                    </tr>
                </thead>
                <tbody>
                    @for($age=0;$age<=100;++$age)
                    @isset($age_counts[$age])
                    <tr>
                        <td class="text-center">{{$age}}</td>
                        <td class="text-center">{{$age_counts[$age]}}</td>
                    </tr>
                    @endisset
                    @endfor
                </tbody>
            </table>
        </div>
        <div class="col-md-2">
            <h3>年齢階級別分布</h3>
            <table class="table table-sm table-striped　table-border">
                <thead>
                    <tr>
                        <th>年齢階級</th>
                        <th>度数</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($label as $key=>$value)
                    <tr>
                        <td>{{$value}}</td>
                        <td>{{$count[$key]}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-md-8 my-4">
            <canvas id="histgram" class="chartarea"></canvas>
        </div>
        @else
        <p class="alert alert-warning">アプリ利用者 サマリは管理者のみ表示できます。</p>
        @endif
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
        type: 'pie',

        // データセットのデータ
        data: {
            labels: ["男", "女", "それ以外"],
            datasets: [{
                    backgroundColor: ['rgb(0, 0, 255)', 'rgb(255, 0, 0)', 'rgb(0, 255, 0)'],
                    borderColor: 'rgb(224, 228, 273)',
                    data: [<?php echo $chartdata['sex'];?>],
                }]
        },

        // ここに設定オプションを書きます
        options: {}
    });
    
    var hist = document.getElementById('histgram').getContext('2d');
    var chart = new Chart(hist, {
        // 作成したいチャートのタイプ
        type: 'bar',

        // データセットのデータ
        data: {
            labels: <?php echo $histlabels; ?>,
            datasets: [{
                    label: "年齢分布",
//                    backgroundColor: ['rgb(0, 0, 255)', 'rgb(255, 0, 0)', 'rgb(0, 255, 0)'],
//                    borderColor: 'rgb(224, 228, 273)',
                    data: <?php echo $histcounts; ?>,
                }]
        },

        // ここに設定オプションを書きます
        options: {}
    });
</script>
@endsection

