@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h1>休薬危険薬剤等ディクショナリに{{count($druginfo)}}件の薬剤情報を取り込みました。</h1>
    </div>
    <div class="row">
        <table class="table table-sm table-striped table-bordered">
            <thead>
                <tr>
                    @foreach(config('const.em_dict_fields') as $label)
                    <th class="text-center">{{$label}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @for($d=0;$d<count($druginfo);++$d)
                <tr>
                    @foreach(config('const.em_dict_fields') as $key=>$label)
                    <td>{{$druginfo[$d][$key]}}</td>
                    @endforeach
                </tr>
                @endfor
            </tbody>
        </table>
        <a class="btn btn-block btn-outline-info px-2" href="{{route('dictionary_menu')}}">戻る</a>
    </div>
</div>
@endsection