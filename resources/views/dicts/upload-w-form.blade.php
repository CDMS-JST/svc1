@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header alert-danger">{{ __('Import Data') }}：休薬危険薬剤</div>

                <div class="card-body">
                    <h3>操作方法</h3>
                    <ol>
                        <li>休薬.xlsxファイルを開き、データ範囲を選択、コピーし、下のフォームに貼り付けてください。</li>
                        <li>分類項目を1つ選択してください。</li>
                        <li><a class="btn btn-sm btn-primary mx-2 px-2" href="#">{{ __('Import Data') }}</a>ボタンをクリックしてください。</li>
                    </ol>
                    <p class="alert alert-warning">
                        各データファイルの1行目（薬価基準収載など、データ項目名が入力されている行）はコピーするデータ範囲に含めないでください。
                    </p>
                    <form method="POST" action="{{ route('store_dictionary') }}">
                        @csrf
                        <div class="form-group">
                            <label>登録する薬剤情報（Excelファイルからコピーして貼り付け）</label>
                            <textarea class="form-control" name="drug_informations" rows="5"></textarea>
                        </div>
                        <div class="mb-4">
                            <p>分類</p>
                            <div class="form-check form-check-inline">
                                @for($em_category = 1; $em_category < count(config('const.em_categories_w')); ++$em_category)
                                <input class="form-check-input" type="radio" name="em_category" value="{{config('const.em_categories_w')[$em_category]}}">
                                <label class="form-check-label pr-2 mr-2">{{config('const.em_categories_w')[$em_category]}}</label>
                                @endfor
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            @if ($errors->any())
                            <p class="alert alert-danger px-2">入力・選択内容を確認してください。</p>
                            @endif
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary" name="em_rank" value="W">
                                    {{ __('Import Data') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
