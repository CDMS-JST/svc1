@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-end">
        <a class="btn btn-sm btn-outline-info mx-2 px-2" href="{{route('home')}}">MENUへ戻る</a>
    </div>
    <div class="row bg-alert justify-content-center my-4">
        <h2>気象情報（随時発表：警報・注意報など）</h2>
        <table class="table table-sm table-striped table-bordered">
            <thead>
                <tr>
                    <th>タイトル</th>
                    <th>発信元</th>
                    <th>詳細情報</th>
                </tr>
            </thead>
            <tbody>
                @for($r=0;$r<count($kisyo_extras);++$r)
                <tr>
                    <td>{{$kisyo_extras[$r]['title']}}</td>
                    <td>{{$kisyo_extras[$r]['author_name']}}</td>
                    <td>
                        <form action="./kisyo/detail" method="post">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary px-2" name="detail_url" value="{{$kisyo_extras[$r]['detail_url']}}">
                                確認する
                            </button>
                        </form>
                    </td>
                </tr>
                @endfor
                </tbody>
        </table>
    </div>
    <div class="row justify-content-center">
        <h2>気象情報（定時発表）</h2>
        <table class="table table-sm table-striped table-bordered">
            <thead>
                <tr>
                    <th>タイトル</th>
                    <th>発信元</th>
                    <th>詳細情報</th>
                </tr>
            </thead>
            <tbody>
                @for($r=0;$r<count($kisyo_regulars);++$r)
                <tr>
                    <td>{{$kisyo_regulars[$r]['title']}}</td>
                    <td>{{$kisyo_regulars[$r]['author_name']}}</td>
                    <td>
                        <form action="./kisyo/detail" method="post">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary px-2" name="detail_url" value="{{$kisyo_regulars[$r]['detail_url']}}">
                                確認する
                            </button>
                        </form>
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>
@endsection
