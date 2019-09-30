<?php

namespace App\Http\Controllers;

use App\Medication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function convert(){
        $prescriptions = DB::connection('mysql_2')->table('user_drug')->orderBy('user_id')->get();
        foreach($prescriptions as $prescription){
            if(strlen($prescription->drug_id)<10){
                $user_id = $prescription->user_id;
                $drug_id = $prescription->drug_id;
                // drugsテーブルでレセプト電算コード→YJコード変換を行う
                $drug_id_yj = DB::connection('mysql_2')->table('drugs')->where('drug_code', $prescription->drug_id)->value('drug_basecode');
                $drug_id_yj9 = substr($drug_id_yj, 0, 9);
                $drug_name = DB::connection('mysql_2')->table('drugs')->where('drug_code', $prescription->drug_id)->value('drug_name');
                // user_drugデーブルの薬剤コードをYJコードに書き換える
                $update = DB::connection('mysql_2')->table('user_drug')->where('user_id', $user_id)->where('drug_id', $drug_id)->update(['drug_id' => $drug_id_yj]);
                
                printf("user:%s　の　薬剤コード %s を %s に変更しました。<br>", $user_id, $drug_id, $drug_id_yj);
                // medicationsテーブルに薬剤情報を書き込む(upsert)
                
                Medication::updateOrCreate(['drug_id_qr' => $drug_id_yj], [
                    'drug_id_qr' => $drug_id_yj,
                    'drug_id_yj9' => $drug_id_yj9,
                    'drug_name_qr' => $drug_name
                ]);
                printf("%s (%s) %s を登録しました。<br>", $drug_id_yj, $drug_id_yj9, $drug_name);
            }             
        }
    }
    
    public function index()
    {
        //
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
    public function store($drug_id_qr, $drug_name_qr)
    {
        $medication['drug_id_qr'] = $drug_id_qr;
        $medication['drug_id_yj9'] = substr($drug_id_qr, 0, 9);
        $medication['drug_name_qr'] = $drug_name_qr;
        $result = DB::connection('mysql_2')->table('medications')->updateOrCreate(['drug_id_qr', $drug_id_qr], [$medication]);
        return $result;
    }
    
    public function maint_master(Request $request){
        $result = sprintf("ID=%s(%s):%s<br>", $request['drug_id_qr'], $request['drug_id_yj9'], $request['drug_name_qr']);
        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Medication  $medication
     * @return \Illuminate\Http\Response
     */
    public function show(Medication $medication)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Medication  $medication
     * @return \Illuminate\Http\Response
     */
    public function edit(Medication $medication)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Medication  $medication
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Medication $medication)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Medication  $medication
     * @return \Illuminate\Http\Response
     */
    public function destroy(Medication $medication)
    {
        //
    }
}
