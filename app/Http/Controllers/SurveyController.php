<?php

namespace App\Http\Controllers;

use App\Survey;
use Illuminate\Http\Request;
use App\Crypt;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // user_idをjsonで受け取る場合
//        $json = json_decode($str, true);
//        $c = new Crypt($json['user_id']);
//        $id = $c->getID();
        // user_idをformパラメータとして受け取る場合
        $user_id = $request->input('user_id');

        
//        $user_id = time();

        
        return view('survey.index', compact('user_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'a1' => 'required',
            'a2' => 'required',
            'a3' => 'required',
        ]);
        
                // user_idをjsonで受け取る場合
//        $json = json_decode($str, true);
//        $c = new Crypt($json['user_id']);
//        $id = $c->getID();
        // user_idをformパラメータとして受け取る場合
//        $user_id = $request->input('user_id');

        $user_id = $request->input('user_id');
        $c = new Crypt($user_id);
        $user_id = $c->getID();

        $answers['user_id'] = $user_id;
        
        for($q=1;$q<=5;++$q){
            $answers['qno'] = $q;
            $ano = sprintf("a%s", $q);
            $answers['answer'] = mb_convert_kana($request->input($ano), "KVnra");
            Survey::create($answers);
        }
        
        return view('survey.thankyou');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function show(Survey $survey)
    {
        
    }
    
    public function stat(){
        $results = Survey::all();
        
        $stat['1']['a'] = 0;
        $stat['1']['b'] = 0;
        $stat['1']['c'] = 0;
        $stat['1']['d'] = 0;
        
        $stat['2']['a'] = 0;
        $stat['2']['b'] = 0;
        $stat['2']['c'] = 0;
        $stat['2']['d'] = 0;
        
        $stat['3']['a'] = 0;
        $stat['3']['b'] = 0;
        $stat['3']['c'] = 0;
        
        $stat['4'] = "";
        $stat['5'] = "";
        
        foreach($results as $result){
            $qno = $result->qno;
            $answer = trim($result->answer);
            switch($qno){
                case '1':
                    
//                    break;
                case '2':
                    
                case '3':
                    ++$stat[$qno][$answer];
                    break;
                default :
                    $stat[$qno] .= sprintf("%s\n", $answer);
                    break;
            }
            
        }
        
        $chartdata['1'] = sprintf("%s, %s, %s, %s", $stat['1']['a'], $stat['1']['b'], $stat['1']['c'], $stat['1']['d']);
        $chartdata['2'] = sprintf("%s, %s, %s, %s", $stat['2']['a'], $stat['2']['b'], $stat['2']['c'], $stat['2']['d']);
        $chartdata['3'] = sprintf("%s, %s, %s", $stat['3']['a'], $stat['3']['b'], $stat['3']['c']);
        
        return view('survey.stat', compact('stat', 'chartdata'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function edit(Survey $survey)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Survey $survey)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Survey $survey)
    {
        //
    }
}
