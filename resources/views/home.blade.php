@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ config('app.name', 'Laravel') }} ダッシュボード</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <a class="btn btn-block btn-danger px-2" href="./user_info/list/48H">休薬危険薬剤処方患者一覧</a>
                    <a class="btn btn-block btn-warning px-2" href="./user_info/list/1W">準休薬危険薬剤処方患者一覧</a>
                    <a class="btn btn-block btn-outline-info px-2" href="{{route('user_info')}}">アプリ利用者一覧</a>
                    <a class="btn btn-block btn-outline-info px-2" href="{{route('user_info_summary')}}">アプリ利用者 サマリ</a>
                    <a class="btn btn-block btn-outline-info px-2" href="{{route('applog')}}">アプリ利用 ログ</a>
                    <a class="btn btn-block btn-outline-info px-2" href="{{route('survey_stat')}}">アンケート集計</a>
                    <a class="btn btn-block btn-outline-info px-2" href="{{route('dictionary_menu')}}">休薬危険薬剤等ディクショナリ</a>
                    <a class="btn btn-block btn-outline-info px-2" href="{{route('show_drug_list')}}">医薬品コードマスター　参照</a>
                    <a class="btn btn-block btn-outline-info px-2" href="{{route('show_registered_users')}}">登録利用者一覧</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
