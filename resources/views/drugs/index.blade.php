@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-end">
        <a class="btn btn-sm btn-outline-info mx-2 px-2" href="{{route('home')}}">MENUへ戻る</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-3">
            <h3>一覧表示条件設定</h3>
            <form action="" method="post">
                @csrf
                <input type="text" name="name_kwd" placeholder="薬剤名の一部を入力" value="{{old('name_kwd')}}" />
                <div class="form-group">
                    <label class="badge badge-pill badge-primary">分類</label><br>
                    <input class="form-control-inline" type="radio" name="drug_special" value="0" />全種
                    <input class="form-control-inline" type="radio" name="drug_special" value="1" />麻薬
                    <input class="form-control-inline" type="radio" name="drug_special" value="2" />毒薬
                    <input class="form-control-inline" type="radio" name="drug_special" value="3" />覚醒剤原料
                    <input class="form-control-inline" type="radio" name="drug_special" value="5" />向精神薬
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Update_list') }}</button>
            </form>
        </div>
        <div class="col-md-9">
            <h1>薬剤一覧</h1>
            {{ $drugs->links() }}
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>医薬品コード</th>
                        <th>名称</th>
                        <th>分類</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($drugs as $drug)
                    <tr>
                        <td>{{$drug->drug_code}}</td>
                        <td>{{$drug->drug_name}}</td>
                        <td>{{config('const.drug_specials')[$drug->drug_special]}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
