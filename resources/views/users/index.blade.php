@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-end">
        <a class="btn btn-sm btn-outline-info mx-2 px-2" href="{{route('home')}}">MENUへ戻る</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-12">
            <h1>管理ツール登録利用者一覧</h1>
            @if(Auth::user()->isadmin===1)
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">氏名</th>
                        <th colspan="2" class="text-center">よみがな</th>
                        <th>メールアドレス</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{$user->lastname}}</td>
                        <td>{{$user->givnname}}</td>
                        <td>{{$user->lastkana}}</td>
                        <td>{{$user->givnkana}}</td>
                        <td>{{$user->email}}</td>
                        <td>
                            @if($user->isadmin !== 1)
                            <a class="btn btn-sm btn-warning px-2" href="./users/set_to_admin/{{$user->id}}">管理者にする</a>
                            @else
                            @if(Auth::user()->email !== $user->email)
                            <a class="btn btn-sm btn-primary px-2" href="./users/set_to_normal/{{$user->id}}">一般利用者にする</a>
                            @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="alert alert-warning">登録ユーザー一覧は管理者のみ表示できます。</p>
            @endif
        </div>
    </div>
</div>
@endsection
