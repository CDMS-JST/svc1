@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-end">
        <a class="btn btn-sm btn-outline-info mx-2 px-2" href="{{route('get_kisyo')}}">気象情報一覧へ戻る</a>
        <a class="btn btn-sm btn-outline-info mx-2 px-2" href="{{route('home')}}">MENUへ戻る</a>
    </div>
    <div class="row bg-alert justify-content-center my-4">
        <h2>{{$detail_arr['Control']['Title']}}（{{$detail_arr['Control']['DateTime']}}）</h2>
        {{$detail_arr['Head']['Title']}}
        {{$detail_arr['Head']['TargetDateTime']}}
        {{$detail_arr['Head']['InfoType']}}<br>
        {{$detail_arr['Head']['Headline']['Text']}}<br>
    </div>
</div>
@endsection
