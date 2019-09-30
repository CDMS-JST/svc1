@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-end">
        <a class="btn btn-sm btn-outline-info mx-2 px-2" href="{{route('dictionary_menu')}}">戻る</a>
    </div>
    <div class="row justify-content-center">
        <h1 class="my-4">{{$em_rank_description['name']}}（{{$em_rank_description['description']}}）</h1>
        <table class="table table-sm table-striped table-bordered">
            <thead>
                <tr>
                    <th>分類</th>
                    <th>告示名称</th>
                    <th>規格単位</th>
                    <th>販売会社名</th>
                </tr>
            </thead>
            <tbody>
                @foreach($drugs as $drug)
                <tr>
                    <td>{{$drug->em_category}}</td>
                    <td>{{$drug->name_notified}}</td>
                    <td>{{$drug->unit}}</td>
                    <td>{{$drug->company}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
