@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h3>休薬危険薬剤等ディクショナリ</h3>
    </div>
    <div class="row">
        <h1 class="alert alert-warning px-2">間違ったデータを取り込もうとしました。</h1>
        <a class="btn btn-block btn-outline-info px-2" href="{{route('dictionary_menu')}}">戻る</a>
    </div>
</div>
@endsection