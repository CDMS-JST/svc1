@extends('layouts.common')

@section('title', '災害および症状急変時医療関連情報提供サービス')
@section('keywords', '災害支援,患者支援,薬剤情報')
@section('description', '災害および症状急変時医療関連情報提供サービス')
@section('pageCss')
<link href="/css/page.css" rel="stylesheet">
@endsection

@include('layouts.head_config')

@section('content')
<div class="container">
    <div class="row justify-content-end">
        <a class="btn btn-sm btn-outline-info mx-2 px-2" href="{{route('home')}}">MENUへ戻る</a>
    </div>
    <div class="row">
        <h1>アンケート集計結果</h1>
    </div>
    <div class="row">
        <div class="col-md-4 my-2 px-1">
            <h2 class="my-2">Q1</h2>
            <table class="table table-sm table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">a</th>
                        <th class="text-center">b</th>
                        <th class="text-center">c</th>
                        <th class="text-center">d</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">{{$stat['1']['a']}}</td>
                        <td class="text-center">{{$stat['1']['b']}}</td>
                        <td class="text-center">{{$stat['1']['c']}}</td>
                        <td class="text-center">{{$stat['1']['d']}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6  mx-auto my-2 px-1">
            <canvas id="chart1" class="chartarea"></canvas>
        </div>
        
        <div class="col-md-4 my-2 px-1">
            <h2 class="my-2">Q2</h2>
            <table class="table table-sm table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">a</th>
                        <th class="text-center">b</th>
                        <th class="text-center">c</th>
                        <th class="text-center">d</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">{{$stat['2']['a']}}</td>
                        <td class="text-center">{{$stat['2']['b']}}</td>
                        <td class="text-center">{{$stat['2']['c']}}</td>
                        <td class="text-center">{{$stat['2']['d']}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6  mx-auto my-2 px-1">
            <canvas id="chart2" class="chartarea"></canvas>
        </div>
        
        <div class="col-md-4 my-2 px-1">
            <h2 class="my-2">Q3</h2>
            <table class="table table-sm table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">a</th>
                        <th class="text-center">b</th>
                        <th class="text-center">c</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">{{$stat['3']['a']}}</td>
                        <td class="text-center">{{$stat['3']['b']}}</td>
                        <td class="text-center">{{$stat['3']['c']}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6  mx-auto my-2 px-1">
            <canvas id="chart3" class="chartarea"></canvas>
        </div>
        
        
        <div class="col-md-10  mx-auto my-2 px-1">
            
            <h2 class="my-2">Q4</h2>
            {!! nl2br(e($stat['4'])) !!}
            <h2 class="my-2">Q5</h2>
            {!! nl2br(e($stat['5'])) !!}
        </div>
    </div>
</div>
@endsection
@section('pageJs')
@endsection

@include('layouts.footer')
@section('js_config')
<script>
    var ctx1 = document.getElementById('chart1').getContext('2d');
    var chart1 = new Chart(ctx1, {
        // 作成したいチャートのタイプ
        type: 'pie',

        // データセットのデータ
        data: {
            labels: ["とても簡単", "どちらかといえば簡単", "どちらかといえば難しい", "とても難しい"],
            datasets: [{
                    backgroundColor: ['rgb(0, 0, 255)', 'rgb(255, 0, 0)', 'rgb(0, 255, 0)', 'rgb(0, 0, 0)'],
                    borderColor: 'rgb(224, 228, 273)',
                    data: [<?php echo $chartdata['1'];?>],
                }]
        },

        // ここに設定オプションを書きます
        options: {}
    });
    
    var ctx2 = document.getElementById('chart2').getContext('2d');
    var chart2 = new Chart(ctx2, {
        // 作成したいチャートのタイプ
        type: 'pie',

        // データセットのデータ
        data: {
            labels: ["とても役に立つ", "どちらかといえば役に立つ", "どちらかといえば難しい", "とても難しい"],
            datasets: [{
                    backgroundColor: ['rgb(0, 0, 255)', 'rgb(255, 0, 0)', 'rgb(0, 255, 0)', 'rgb(0, 0, 0)'],
                    borderColor: 'rgb(224, 228, 273)',
                    data: [<?php echo $chartdata['2'];?>],
                }]
        },

        // ここに設定オプションを書きます
        options: {}
    });
    
    var ctx3 = document.getElementById('chart3').getContext('2d');
    var chart3 = new Chart(ctx3, {
        // 作成したいチャートのタイプ
        type: 'pie',

        // データセットのデータ
        data: {
            labels: ["災害、緊急時だけ利用", "日常の情報の記録にも利用", "日常の情報の記録だけに利用"],
            datasets: [{
                    backgroundColor: ['rgb(0, 0, 255)', 'rgb(255, 0, 0)', 'rgb(0, 255, 0)'],
                    borderColor: 'rgb(224, 228, 273)',
                    data: [<?php echo $chartdata['3'];?>],
                }]
        },

        // ここに設定オプションを書きます
        options: {}
    });
</script>
@endsection