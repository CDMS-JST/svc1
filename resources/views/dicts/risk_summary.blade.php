@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <a class="btn btn-block btn-outline-info px-2" href="{{route('dictionary_menu')}}">戻る</a>
        <h1>{{$searched}}の休薬リスク検索結果</h1>
    </div>
    @if(count($drug_48H)>0)
    <div class="row alert alert-danger my-4">
        <h2 class="py-2">概ね48時間以内に服薬再開が必要な薬剤が{{count($drug_48H)}}件あります。</h2>
        <table class="table table-sm table-striped table-bordered">
            <thead>
                <tr>
                    <th>告示名称</th>
                    <th>規格単位</th>
                    <th>販売会社名</th>
                </tr>
            </thead>
            <tbody>
                @for($c=1;$c<count($drug_48H);++$c)
                <tr>
                    <td>{{$drug_48H[$c]['name_notified']}}</td>
                    <td>{{$drug_48H[$c]['unit']}}</td>
                    <td>{{$drug_48H[$c]['company']}}</td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
    @endif
    @if(count($drug_1W)>0)
    <div class="row alert alert-warning my-4">
        <h2 class="py-2">概ね1週間以内に服薬再開が必要な薬剤が{{count($drug_1W)}}件あります。</h2>
        <table class="table table-sm table-striped table-bordered">
            <thead>
                <tr>
                    <th>告示名称</th>
                    <th>規格単位</th>
                    <th>販売会社名</th>
                </tr>
            </thead>
            <tbody>
                @for($c=1;$c<count($drug_1W);++$c)
                <tr>
                    <td>{{$drug_1W[$c]['name_notified']}}</td>
                    <td>{{$drug_1W[$c]['unit']}}</td>
                    <td>{{$drug_1W[$c]['company']}}</td>
                </tr>
                @endfor
                </tbody>
        </table>
    </div>
    @endif
</div>
@endsection