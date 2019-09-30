@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-end">
        <a class="btn btn-sm btn-outline-info mx-2 px-2" href="{{route('home')}}">MENUへ戻る</a>
    </div>
    <div class="row justify-content-center my-4 py-4">
        <h3>休薬危険薬剤等ディクショナリ</h3>
    </div>
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">休薬リスク薬剤の簡易検索</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('search_dictionary') }}">
                        @csrf
                        <div class="form-group row mb-2">
                            <input class="form-control form-control-lg" type="text" name="search_name" placeholder="薬剤名の一部を入力してください">
                            <span class="help-block text-danger">{{$errors->first('search_name')}}</span>
                        </div>
                        <div class="form-group row">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="search_mode" id="inlineRadio1" value="START" checked="checked">
                                <label class="form-check-label" for="inlineRadio1">入力した語で始まる</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="search_mode" id="inlineRadio2" value="CONTAIN">
                                <label class="form-check-label" for="inlineRadio2">入力した語をどこかに含む</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary"">
                                    薬剤の休薬危険リスクを調べる
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <a href="./dictionary/list/48H" class="btn btn-block btn-danger px-2 my-2">休薬危険薬剤一覧</a>
            <p class="py-2 px-4">概ね４８時間以内に服薬を再開しなければならない薬剤です。<br>災害等に伴う避難等でこれに該当する薬を避難場所へ携行できなかった場合は速やかに医療従事者へ伝えてください。</p>
            <a href="./dictionary/list/1W" class="btn btn-block btn-warning px-2 my-2">準休止危険薬剤一覧</a>
            <p class="py-2 px-4">概ね１週間以内に服薬を再開しなければならない薬剤です。</p>
        </div>
    </div>
    @if(Auth::user()->isadmin===1)
    <div class="row bg-dark py-2">
        <div class="col-md-6 mx-auto">
            <a href="./dictionary/upload?em_rank=W" class="btn btn-block btn-danger px-2">休薬危険薬剤データ取り込み</a>
            <a href="./dictionary/upload?em_rank=M" class="btn btn-block btn-warning px-2">準休薬危険薬剤データ取り込み</a>
        </div>
    </div>
    @endif
</div>
@endsection
