@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-end">
        <a class="btn btn-danger mx-2 px-2" href="./user_info/list/48H">休薬危険薬剤処方患者</a>
        <a class="btn btn-warning mx-2 px-2" href="./user_info/list/1W">準休薬危険薬剤処方患者</a>
        <a class="btn btn-sm btn-outline-info mx-2 my-auto px-2" href="{{route('distribution')}}">分布地図</a>
        <a class="btn btn-sm btn-outline-info mx-2 my-auto px-2" href="{{route('home')}}">MENUへ戻る</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-12">
            <h1>アプリ利用者一覧</h1>
            
            @if(Auth::user()->isadmin===1)
            
            {{$users->links()}}
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>版</th>
                        <th class="text-center">氏名</th>
                        <th class="text-center">生年月日・性別</th>
                        <th class="text-center">住所</th>
                        <th class="text-center">位置（最終更新）</th>
                        <th class="text-center">最終更新日時</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="text-center">
                            @if($user->v0 === "1")
                            旧
                            @endif
                        </td>
                        <td class="text-nowrap">{{$user->user_name}}</td>
                        <td class="text-nowrap">
                            {{$user->user_birth}}
                            @isset(config('const.sexes')[$user->user_sex])
                            ・{{config('const.sexes')[$user->user_sex]}}
                            @endisset
                        </td>
                        <td>
                            {{$user->user_postal}}　{{$user->user_address}}
                            @if($user->user_tel !== "")
                            ({{$user->user_tel}})
                            @endif
                        </td>
                        <td class="text-nowrap">
                            @php
                            if(is_float($user->user_lat)){
                            if(is_float($user->user_lng)){
                            $latlng = sprintf("(%s, %s)", $user->user_lat, $user->user_lng);
                            } else {
                            $latlng = "×";
                            }
                            } else {
                            $latlng = "×";
                            }
                            @endphp
                            {{$latlng}}
                            @if($latlng!=="×")
                            <a class="btn btn-sm btn-outline-info px-2" href="./user_info/show/{{$user->user_id}}"><i class="fas fa-globe-asia"></i>利用者サマリ</a>
                            @endif
                        </td>
                        <td>{{$user->latest_time}}</td>
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
