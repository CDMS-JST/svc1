@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-end">
        <a class="btn btn-sm btn-outline-info mx-2 px-2" href="{{route('home')}}">MENUへ戻る</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-12">
            <h1>スマホアプリ　利用ログ</h1>

            @if(Auth::user()->isadmin===1)
            <ul>
                @foreach($logs_desc as $log)
                <li>{{$log}}</li>
                @endforeach
            </ul>
            @else
            <p class="alert alert-warning">登録ユーザー一覧は管理者のみ表示できます。</p>
            @endif
        </div>
    </div>
</div>
@endsection
