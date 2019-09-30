<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Drug;
use Illuminate\Http\Request;

class DrugController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function show_specified(Request $request){
        $name_kwd = trim($request->input('name_kwd'));
        $drug_special = $request->input('drug_special');
        if($drug_special=="" || $drug_special =="0"){ // 分類による絞り込み条件なし
            $name_kwd = "%" . $name_kwd . "%";
            $drugs = DB::connection('mysql_2')->table('drugs')->where('drug_name', 'LIKE', $name_kwd)->orderBy('drug_kana', 'asc')->paginate(10);
            return view('drugs.index', compact('drugs'));
        } else { // 分類指定あり
            $drugs = DB::connection('mysql_2')->table('drugs')->where('drug_special', $drug_special)->orderBy('drug_kana', 'asc')->paginate(10);
            return view('drugs.index', compact('drugs'));
        }
        
    }
    
    public function index(Request $request)
    {
        ini_set("display_errors",1);
        printf("%s",$request->input('name_kwd'));
        $drugs = DB::connection('mysql_2')->table('drugs')->orderBy('drug_kana', 'asc')->paginate(10);

        return view('drugs.index', compact('drugs'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Drug  $drug
     * @return \Illuminate\Http\Response
     */
    public function show(Drug $drug)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Drug  $drug
     * @return \Illuminate\Http\Response
     */
    public function edit(Drug $drug)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Drug  $drug
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Drug $drug)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Drug  $drug
     * @return \Illuminate\Http\Response
     */
    public function destroy(Drug $drug)
    {
        //
    }
}
