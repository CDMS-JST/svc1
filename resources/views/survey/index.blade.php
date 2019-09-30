@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4 my-2 px-1">
            <h1>アンケート(ID:{{$user_id}})</h1>
            <p>
                糖尿病患者支援アプリをお試しいただきありがとうございます。<br>
                より実用性のある便利なツールとなるようにアンケートにご協力ください。<br>
                アンケートの内容は本アプリ及び関連する患者支援サービスの改善のために
                利用いたします。アンケート結果は個人が特定されない集計値として学会発表等で
                公表させていただく場合があります。アンケートに回答いただくことを以て
                アンケート集計情報の公表に同意いただいたものとさせていただきます。
            </p>
        </div>
        
    </div>
    <form action="./survey/answer" method="post">
        @csrf
        <div class="row mt-2">
            <div class="col-md-4 my-2 px-1">
                <h2 class="alert alert-block alert-primary px-2">Q1. このアプリの操作性について最もあてはまるものを1つ選択してください。</h2>
                @error('a1')
                <p class="alert alert-danger px-1 py-2">
                    {{$message}}
                </p>
                @enderror
                <div class="form-control-lg form-check">
                    <input class="form-check-input" type="radio" name="a1" id="choice1_a" value="a">
                    <label class="form-check-label" for="choce1_a">
                        とても簡単だった
                    </label>
                </div>
                <div class="form-control-lg form-check">
                    <input class="form-check-input" type="radio" name="a1" id="choice1_b" value="b">
                    <label class="form-check-label" for="choce1_b">
                        どちらかといえば簡単だった
                    </label>
                </div>
                <div class="form-control-lg form-check">
                    <input class="form-check-input" type="radio" name="a1" id="choice1_c" value="c">
                    <label class="form-check-label" for="choce1_c">
                        どちらかといえば難しかった
                    </label>
                </div>
                <div class="form-control-lg form-check">
                    <input class="form-check-input" type="radio" name="a1" id="choice1_d" value="d">
                    <label class="form-check-label" for="choce1_d">
                        とても難しかった
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-4 my-2 px-1">
                <h2 class="alert alert-block alert-primary px-2">Q2. このアプリが普及した場合、役に立つと思いますか？最もあてはまるものを1つ選択してください。</h2>
                @error('a2')
                <p class="alert alert-danger px-1 py-2">
                    {{$message}}
                </p>
                @enderror
                <div class="form-control-lg form-check">
                    <input class="form-check-input" type="radio" name="a2" id="choice2_a" value="a">
                    <label class="form-check-label" for="choce2_a">
                        とても役に立つ
                    </label>
                </div>
                <div class="form-control-lg form-check">
                    <input class="form-check-input" type="radio" name="a2" id="choice2_b" value="b">
                    <label class="form-check-label" for="choce2_b">
                        どちらかといえば役に立つ
                    </label>
                </div>
                <div class="form-control-lg form-check">
                    <input class="form-check-input" type="radio" name="a2" id="choice2_c" value="c">
                    <label class="form-check-label" for="choce2_c">
                        どちらかといえば役に立たない
                    </label>
                </div>
                <div class="form-control-lg form-check">
                    <input class="form-check-input" type="radio" name="a2" id="choice2_d" value="d">
                    <label class="form-check-label" for="choce2_d">
                        まったく役に立たない
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            
            <div class="col-md-4 my-2 px-1">
                <h2 class="alert alert-primary px-2">Q3. このアプリはどのように利用すると効果が高いと思いますか？最もあてはまるものを1つ選択してください。</h2>
                @error('a3')
                <p class="alert alert-danger px-1 py-2">
                    {{$message}}
                </p>
                @enderror
                <div class="form-control-lg form-check">
                    <input class="form-check-input" type="radio" name="a3" id="choice3_a" value="a">
                    <label class="form-check-label" for="choce3_a">
                        災害時や緊急時だけ利用する
                    </label>
                </div>
                <div class="form-control-lg form-check mb-2">
                    <input class="form-check-input" type="radio" name="a3" id="choice3_b" value="b">
                    <label class="form-check-label" for="choce3_b">
                        災害時や緊急時だけでなく、日常の情報の記録にも利用する
                    </label>
                </div>
                <div class="form-control-lg form-check mt-2">
                    <input class="form-check-input" type="radio" name="a3" id="choice3_c" value="c">
                    <label class="form-check-label" for="choce3_c">
                        日常の情報の記録のためだけに利用する
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-4 my-2 px-1">
                <h2 class="alert alert-primary px-2">このアプリを試用して便利だ、役に立つなど肯定的に感じたことがあれば記入してください。複数ある場合は箇条書きにしてください。</h2>
                <textarea cols="40" rows="5" name="a4"></textarea>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-4 my-2 px-1">
                <h2 class="alert alert-primary px-2">このアプリを試用して操作が難しい、○○について入力項目を増やすべきだなど否定的または改善を要すると感じたことがあれば記入してください。複数ある場合は箇条書きにしてください。</h2>
                <textarea cols="40" rows="5" name="a5"></textarea>
            </div>
        </div>
        <div class="row mt-2 mx-auto">
            <button type="submit" class="btn btn-lg btn-primary py-2 px-2" name="user_id" value="{{$user_id}}">
                {{ __('SendAnswer') }}
            </button>
        </div>
    </form>
    
</div>
@endsection
