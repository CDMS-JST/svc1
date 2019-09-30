<?php

namespace App\Http\Controllers;

use App\DrugDictionary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DrugDictionaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update_userdrug_emrank(){
        $prescriptions = DB::connection('mysql_2')->table('user_drug')->orderBy('user_id')->get();
        foreach($prescriptions as $prescription){
            $user_id = $prescription->user_id;
            $drug_id = $prescription->drug_id;
            $yj9 = substr($drug_id, 0, 9);
            $em_rank = $this->check_kk($yj9);
            printf("ID=%s|%s:%s<br>",$user_id,$drug_id,$em_rank);
            $update = DB::connection('mysql_2')->table('user_drug')->where('user_id', $user_id)->where('drug_id', $drug_id)->update(['em_rank'=>$em_rank]);
        }
    }
    
    public function check_kk($yj9){
        $yj9 .= "%";
        $em_rank = DB::table('drug_dictionaries')->where('code_yj', 'like', $yj9)->value('em_rank');
        return $em_rank;
    }
    
    public function search(Request $request){
        $validatedData = $request->validate([
            'search_name' => 'required',
        ]);
        
        $search_name = mb_convert_kana($request->input('search_name'), "KVNRAC");
        $search_mode = $request->input('search_mode');
        
        switch($search_mode){
            case 'START':
                $name_key = mb_convert_kana($request->input('search_name'), "KVNRAC") . "%";
                $searched = sprintf("名称が「%s」で始まる薬品", $search_name);
                $risks = DB::table('drug_dictionaries')->where('name_notified', 'like', $name_key)->orderBy('em_rank', 'desc')->orderBy('em_category')->get();
                break;
            case 'CONTAIN':
                $name_key = "%" . mb_convert_kana($request->input('search_name'), "KVNRAC") . "%";
                $searched = sprintf("名称のどこかに「%s」が含まれる薬品", $search_name);
                $risks = DB::table('drug_dictionaries')->where('name_notified', 'like', $name_key)->orderBy('em_rank', 'desc')->orderBy('em_category')->get();
                break;
        }
        $drug_48H = array();
        $drug_1W = array();
        $c48H = 0;
        $c1W = 0;
        foreach($risks as $risk){
            switch($risk->em_rank){
                case '48H':
                    ++$c48H;
                    $drug_48H[$c48H]['name_notified'] = $risk->name_notified;
                    $drug_48H[$c48H]['unit'] = $risk->unit;
                    $drug_48H[$c48H]['company'] = $risk->company;
                    break;
                case '1W':
                    ++$c1W;
                    $drug_1W[$c1W]['name_notified'] = $risk->name_notified;
                    $drug_1W[$c1W]['unit'] = $risk->unit;
                    $drug_1W[$c1W]['company'] = $risk->company;
                    break;
            }
        }
        
        return view('dicts.risk_summary', compact('searched', 'drug_48H', 'drug_1W'));
        
    }
    
    public function index()
    {
        return view('dicts.index');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $em_rank = $request->input('em_rank');
        
        switch($em_rank){
            case 'W':
                return view('dicts.upload-w-form');
                break;
            case 'M':
                return view('dicts.upload-m-form');
                break;
        }
        
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
            'em_category' => 'required',
            'drug_informations' => 'required'
        ]);
        
        $drug_informations = explode("\n", preg_replace("/\r\n|\r|\n/", "\n", $request->input('drug_informations')));
        $valid = $this->check_valid($drug_informations);
        
        if(!$valid){
            return view('dicts.abort');
            exit;
        }
        
        $n_regs = count($drug_informations);
        for($d=0;$d<$n_regs;++$d){
            list($code_yj,$code_hot7,$code_hot9,$name_notified,$unit,$company) = explode("\t", $drug_informations[$d]);
            $druginfo[$d]['code_yj'] = $code_yj;
            $druginfo[$d]['code_hot7'] = $code_hot7;
            $druginfo[$d]['code_hot9'] = $code_hot9;
            $druginfo[$d]['name_notified'] = $name_notified;
            $druginfo[$d]['unit'] = $unit;
            $druginfo[$d]['company'] = $company;
            
            $druginfo[$d]['em_rank'] = $request->input('em_rank');
            $druginfo[$d]['em_category'] = $request->input('em_category');
            
            $dictionary = DB::table('drug_dictionaries')
                    ->updateOrInsert(
                            ['code_hot9' => $druginfo[$d]['code_hot9']],
                            $druginfo[$d]
                            );
        }
        
        return view('dicts.success', compact('druginfo'));
    }
    
    public function check_valid($drug_informations){
        $n_regs = count($drug_informations);
        $valid = true;
        for($d=0;$d<$n_regs;++$d){
            $items = explode("\t", $drug_informations[$d]);
            if(count($items)!=6){
                $valid = false;
            }
        }
        
        return $valid;
    }
    
    public function list($em_rank){
        $drugs = DB::table('drug_dictionaries')->where('em_rank', $em_rank)->orderBy('em_category')->orderBy('code_yj')->get();
        $em_rank_description = config('const.em_ranks')[$em_rank];
        
        return view('dicts.list', compact('em_rank_description', 'drugs'));
    }
    

    /**
     * Display the specified resource.
     *
     * @param  \App\DrugDictionary  $drugDictionary
     * @return \Illuminate\Http\Response
     */
    public function show(DrugDictionary $drugDictionary)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DrugDictionary  $drugDictionary
     * @return \Illuminate\Http\Response
     */
    public function edit(DrugDictionary $drugDictionary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DrugDictionary  $drugDictionary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DrugDictionary $drugDictionary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DrugDictionary  $drugDictionary
     * @return \Illuminate\Http\Response
     */
    public function destroy(DrugDictionary $drugDictionary)
    {
        //
    }
}
