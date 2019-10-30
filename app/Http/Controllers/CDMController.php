<?php

namespace App\Http\Controllers;

use App\CDM;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\UserInfo;
use Carbon\Carbon;
use App\Medication;

class CDMController extends Controller
{
    public $cdm_hostname = "http://cdm.srsphere.jp/";
    
    public $basicQA_fields = array(
        'birthDay' => "生年月日",
        'gender' => "性別",
        'height' => "身長",
        'weight' => "体重"
    );
    
    public $hintsections = array(
        'hospitalCode' => '医療機関コード',
        'patientCode' => '患者ID(CDM)'
    );

    
    public $cdm_sexes = array(
        'MALE', 'FEMALE', 'UNKNOWN'
    );
    
    public $cdm_hasMedicines = array(
        'hasMedicine_0',
        'hasMedicine_1'
    );
    
    public $cdm_withDrawalLevel = array(
        '1W' => 'withDrawalLevel_1',
        '48H' => 'withDrawalLevel_2'
    );
    
    public $cdm_freeText_fields = array(
        'dosage',
        'medicineName',
        'quantity',
        'quantityUnit',
        'yjCode'
    );
    
    public function setFreeTextToAnswer($groupCode, $questionCodePath, $value){
        $data = sprintf("\"groupCode\": \"%s\"", $groupCode);
        $data .= sprintf(", \"questionCodePath\": [ %s ]", $questionCodePath);
        $data .= ", \"selectedCode\": \"\"";
        $data .= sprintf(", \"freeText\": \"%s\"", $value);
        $data .= ", \"isEmphatic\": false";
        $textdata = sprintf("{ %s }", $data);

        return $textdata;
    }
    
    public function setSelectedCodeToAnswer($groupCode, $questionCodePath, $value){
        $data = sprintf("\"groupCode\": \"%s\"", $groupCode);
        $data .= sprintf(", \"questionCodePath\": [ %s ]", $questionCodePath);
        $data .= sprintf(", \"selectedCode\": \"%s\"", $value);
        $data .= ", \"isEmphatic\": false";
        $codedata = sprintf("{ %s }", $data);
        
        return $codedata;
    }
    
    
//    public function setFreeTextFeverQA($itemName, $value){
//        $data = "\"groupCode\": \"feverQa\"";
//        $data .= sprintf(", \"questionCodePath\": [ \"%s\" ]", $itemName);
//        $data .= ", \"selectedCode\": \"\"";
//        $data .= sprintf(", \"freeText\": \"%s\"", $value);
//        $data .= ", \"isEmphatic\": false";
//        $textdata = sprintf("{ %s }", $data);
//        
//        return $textdata;
//    }
//    
//    public function setSelectedCodeToFeverQA($itemName, $value){
//        $data = "\"groupCode\": \"feverQa\"";
//        $data .= sprintf(", \"questionCodePath\": [ \"%s\" ]", $itemName);
//        $data .= sprintf(", \"selectedCode\": \"%s\"", $value);
//        $data .= ", \"isEmphatic\": false";
//        $codedata = sprintf("{ %s }", $data);
//        
//        return $codedata;
//    }
    
    public function getNewCDMTicket() { // 初めてCDMを利用するときのチケット入手 manual section 1-1
        $cdm_jobapi = sprintf("%s/karte/restservice/trusted/PhrRequestTickets", $this->cdm_hostname);
        $ticketCode = $this->getTicket($cdm_jobapi);
        return $ticketCode;
    }

    public function getCDMTicket($patientNo) { // CDM利用経験がある場合のチケット入手 manual section 1-2
        $cdm_jobapi = sprintf("%s/karte/restservice/trusted/PhrRequestTickets/%s", $this->cdm_hostname, $patientNo);
        $ticketCode = $this->getTicket($cdm_jobapi);
        return $ticketCode;
    }

    public function getTicket($cdm_jobapi) { // hiket 入手（共通）
        $identifier = array(
            'detailInformation' => 'mode=Phr;system=SagaDM'
        );
        
        $headers = array(
            'Content-Type: application/json',
        );
        
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => json_encode($identifier)
                )
        );

        $response = @file_get_contents($cdm_jobapi, false, stream_context_create($options));

        $contents = json_decode($response);

        $ticketCode = $contents->t; // チケットコード取得完了
        
        printf("<h2>%s</h2>", $ticketCode);

        return $ticketCode;
    }
    
    public function authIDP($ticketCode){
        // マニュアル 2. Login IDP （認証）

        $cdm_jobapi = sprintf("%s/welcome/login?ticket=%s", $this->cdm_hostname, $ticketCode); // urlにgetと同じようにパラメータを付ける場合

        $headers = array(
            'Content-Type: application/x-www-form-urlencoded'
        );
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
            )
        );
        $content = file_get_contents($cdm_jobapi, false, stream_context_create($options));

        foreach ($http_response_header as $val) {
            if (preg_match("/Set-Cookie:(.*)/u", $val, $match)) {
                list($cookie_keyval, $path) = explode(";", $match[1]);
                $cookie[] = $cookie_keyval;
            }
        }
        
        return $cookie;
    }
    
    public function loginCDM($ticketCode, $cookie){
        // マニュアル 3. CDM Login
        $cdm_jobapi = sprintf("%s/karte/restservice/UserSessions", $this->cdm_hostname);

        $headers = array(
            'Content-Type: application/json',
            'Cookie: ' . implode('; ', $cookie)
        );

        $reqs = array(
            'user' => "",
            'actionType' => "GET_ON_WELCOME_SESSION",
            'hash' => "",
            'ticket' => $ticketCode
        );

        $request_body_json = json_encode($reqs);

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $request_body_json,
            )
        );

        $response = @file_get_contents($cdm_jobapi, false, stream_context_create($options));

// 上記により、クッキーcdmsidがセットされる。このクッキー値は今後すべての処理で必要
        foreach ($http_response_header as $val) {
            if (preg_match("/Set-Cookie:(.*)/u", $val, $match)) {
                list($cookie_keyval, $path) = explode(";", $match[1]);
//      list($key, $val) = explode("=", $cookie_keyval);
//      $cookies[$key] = $val;
                $cookie[] = $cookie_keyval;
            }
        }


        $results = json_decode($response, true);
        return array($results, $cookie);
    }
    
    public function deleteWorkSpace($cdm_ptid, $cookie){
        // マニュアル 4.1 データ領域削除
        $cdm_jobapi = sprintf("%s/karte/restservice/Inquiries/work/%s", $this->cdm_hostname, $cdm_ptid);

        $headers = array(
            'Content-Type: application/json',
            'Cookie: ' . implode('; ', $cookie)
        );

        $options = array(
            'http' => array(
                'method' => 'DELETE',
                'header' => implode("\r\n", $headers),
                'content' => "",
            )
        );

        $response = @file_get_contents($cdm_jobapi, false, stream_context_create($options));
        return $response;
    }
    
    public function getWorkSpace($cdm_ptid, $cookie){
        $cdm_jobapi = sprintf("%s/karte/restservice/Inquiries/work/%s", $this->cdm_hostname, $cdm_ptid);

        $headers = array(
            'Content-Type: application/json',
            'Cookie: ' . implode('; ', $cookie)
        );

        $options = array(
            'http' => array(
                'method' => 'GET',
                'header' => implode("\r\n", $headers),
                'content' => "",
            )
        );

        $response = @file_get_contents($cdm_jobapi, false, stream_context_create($options));
        
        return $response;
    }
    
    public function getBasicQA($cdm_ptid, $cookie){
        $cdm_jobapi = sprintf("%s/karte/restservice/Inquiries/work/BasicQA/%s", $this->cdm_hostname, $cdm_ptid);

        $headers = array(
            'Content-Type: application/json',
            'Cookie: ' . implode('; ', $cookie)
        );

        $options = array(
            'http' => array(
                'method' => 'GET',
                'header' => implode("\r\n", $headers),
                'content' => "",
            )
        );

        $response = @file_get_contents($cdm_jobapi, false, stream_context_create($options));
        
        return $response;
    }
    
    public function quote($text){
        $quated_text = sprintf("\"%s\"", $text);
        return $quated_text;
    }
    
    public function setAnswersToBasicQA($user_id){
        // 糖尿病サーバに保存されたprofileデータを取得
        $users = DB::connection('mysql_2')->table('user_info')->where('user_id', $user_id)->get();
        foreach ($users as $user) {
            $birthDay = $user->user_birth;
            $sex = $user->user_sex;
        }
        
        $groupCode = "basicQa";

        // 生年月日データをセット
        $questionCodePath = "birthDay";
        $questionCodePath = $this->quote($questionCodePath);
        $value = $birthDay;
        
        $answers = $this->setFreeTextToAnswer($groupCode, $questionCodePath, $value); 
        
        // 性別データをセット
        $gender = is_null($sex) ? "UNKNOWN" : $this->cdm_sexes[$sex];
        $questionCodePath = "gender";
        $questionCodePath = $this->quote($questionCodePath);
        $value = $gender;

        $answers .= sprintf(",\n %s", $this->setSelectedCodeToAnswer($groupCode, $questionCodePath, $value));
        
        $questionCodePath = "answerer";
        $questionCodePath = $this->quote($questionCodePath);
        $value = "SELF";
        
        $answers .= sprintf(",\n %s", $this->setSelectedCodeToAnswer($groupCode, $questionCodePath, $value));
        
        $questionCodePath = "height";
        $questionCodePath = $this->quote($questionCodePath);
        $value = "0.0";
        
        $answers .= sprintf(",\n %s", $this->setFreeTextToAnswer($groupCode, $questionCodePath, $value));
        
        $questionCodePath = "weight";
        $questionCodePath = $this->quote($questionCodePath);
        $value = "0.0";
        
        $answers .= sprintf(",\n %s", $this->setFreeTextToAnswer($groupCode, $questionCodePath, $value));
        
        $groupCode = "feverQa";
        $questionCodePath = "tempNow";
        $questionCodePath = $this->quote($questionCodePath);
        $value = "0.0";
        
        $answers .= sprintf(",\n %s", $this->setFreeTextToAnswer($groupCode, $questionCodePath, $value));
        
//        $itemName = "querist";
//        $value = $cdm_ptid;
//        
//        $basicQA .= sprintf(",\n %s", $this->setFreeTextToBasicQA($itemName, $value));
        
        
        return $answers;
    }
    
    public function setAnswersToAnpiQA($user_id){
        // 糖尿病サーバに保存されたprofileデータを取得
        $users = DB::connection('mysql_2')->table('user_info')->where('user_id', $user_id)->get();
        foreach ($users as $user) {
            $latitude = $user->user_lat;
            $longitude = $user->user_lng;
            $drug_possesion = $user->drug_possesion;
        }
        
        $hasMedicine = $this->cdm_hasMedicines[$drug_possesion];
        
        $groupCode ="sagDm_anpi";
        
        // 経度基準（東経、西経）をセット
        $questionCodePath = "EW";
        $questionCodePath = $this->quote($questionCodePath);
        $value = "E";
        $answers = $this->setSelectedCodeToAnswer($groupCode, $questionCodePath, $value);
        
        // 緯度基準（北緯、南緯）をセット
        $questionCodePath = "NS";
        $questionCodePath = $this->quote($questionCodePath);
        $value = "N";
        $answers .= sprintf(",\n %s", $this->setSelectedCodeToAnswer($groupCode, $questionCodePath, $value));
        
        // 緯度（利用者申告）をセット
        $questionCodePath = "latitude";
        $questionCodePath = $this->quote($questionCodePath);
        $value = $latitude;
        $answers .= sprintf(",\n %s", $this->setFreeTextToAnswer($groupCode, $questionCodePath, $value));
        
        // 経度（利用者申告）をセット
        $questionCodePath = "longitude";
        $questionCodePath = $this->quote($questionCodePath);
        $value = $longitude;
        $answers .= sprintf(",\n %s", $this->setFreeTextToAnswer($groupCode, $questionCodePath, $value));
        
        // 薬剤有無（利用者申告）をセット
        $questionCodePath = "hasMedicine";
        $questionCodePath = $this->quote($questionCodePath);
        $value = $hasMedicine;
        $answers .= sprintf(",\n %s", $this->setSelectedCodeToAnswer($groupCode, $questionCodePath, $value));
        
        return $answers;
    }
    
    public function setAnswersToSevereMedicinesQA($user_id){
        $medications = DB::connection('mysql_2')->table('user_drug_info')->where('user_id', $user_id)
                ->where(function ($query){
                    $query->where('em_rank', '48H')
                            ->orWhere('em_rank', '1W');
                })
                ->get();
        $recNo = 0;    
        $groupCode = "sagDm_severeMedicine";
        
        foreach($medications as $medication){
            ++$recNo;
            $severeMedicines[$recNo]['medicineName'] = $medication->drug_name;
            $severeMedicines[$recNo]['quantity'] = $medication->drug_dose;
            $severeMedicines[$recNo]['quantityUnit'] = $medication->drug_dose_unit;
            $severeMedicines[$recNo]['withDrawalLevel'] = $this->cdm_withDrawalLevel[$medication->em_rank];
            $severeMedicines[$recNo]['yjCode'] = $medication->drug_id;
        }
        
        foreach($severeMedicines as $recNo=>$medicines){
            $recNovalue = sprintf("record%02d", $recNo);
            $questionCodePath = "recordCode";
            $questionCodePath = $this->quote($questionCodePath);
            if(!isset($answers)) {
                $answers = $this->setSelectedCodeToAnswer($groupCode, $questionCodePath, $recNovalue);
            } else {
                $answers .= sprintf(",\n %s\n", $this->setSelectedCodeToAnswer($groupCode, $questionCodePath, $recNovalue));
            }
            
            foreach($medicines as $key=>$value){
                $p1 = "recordCode";
                $questionCodePath = sprintf("\n%s", $this->quote($p1));
                $questionCodePath .= sprintf(",\n%s", $this->quote($recNovalue));
                $questionCodePath .= sprintf(",\n%s\n", $this->quote($key));
                
                if(in_array($key, $this->cdm_freeText_fields)){ // freeText項目の処理
                    $answers .= sprintf(",\n %s\n", $this->setFreeTextToAnswer($groupCode, $questionCodePath, $value)); 
                } else { // selectedCode項目
                    $answers .= sprintf(",\n %s\n", $this->setSelectedCodeToAnswer($groupCode, $questionCodePath, $value));
                }
            }
        }
        
        return $answers;
    }
    
    public function prepareCurrentBasicQA($user_id, $cdm_ptid){
        $answers = $this->setAnswersToBasicQA($user_id);
        $basicQA = sprintf("{ \n\"patientCode\": \"%s\", \n\"answers\": [ %s ]\n }", $cdm_ptid, $answers);
        return $basicQA;
    }
    
    public function prepareCurrentAnpiQA($user_id, $cdm_ptid){
        $answers = $this->setAnswersToAnpiQA($user_id);
        $anpiQA = sprintf("{ \n\"patientCode\": \"%s\", \n\"answers\": [ %s ]\n }", $cdm_ptid, $answers);
        return $anpiQA;
    }
    
    public function prepareSevereMedicineQA($user_id, $cdm_ptid){
        $answers = $this->setAnswersToSevereMedicinesQA($user_id);
        $severeMedQA = sprintf("{ \n\"patientCode\": \"%s\", \n\"answers\": [ %s ]\n }", $cdm_ptid, $answers);
        return $severeMedQA;
    }
    
    public function saveBasicQA($cdm_ptid, $cookie, $basicQA){
        $cdm_jobapi = sprintf("%s/karte/restservice/Inquiries/work/BasicQA", $this->cdm_hostname);

        $headers = array(
            'Content-Type: application/json',
            'Cookie: ' . implode('; ', $cookie)
        );

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $basicQA,
            )
        );

        $response = @file_get_contents($cdm_jobapi, false, stream_context_create($options));
        
        return $response;
    }
    
    public function saveDataViaContents($cdm_saveapi, $cdm_ptid, $cookie, $contents){
        $headers = array(
            'Content-Type: application/json',
            'Cookie: ' . implode('; ', $cookie)
        );

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $contents,
            )
        );

        $response = @file_get_contents($cdm_saveapi, false, stream_context_create($options));
        
        return $response;
    }
    
    public function commit($cdm_ptid, $cookie){
        $cdm_jobapi = sprintf("%s/karte/restservice/Inquiries", $this->cdm_hostname);

        $headers = array(
            'Content-Type: application/json',
            'Cookie: ' . implode('; ', $cookie)
        );

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $cdm_ptid,
            )
        );

        $response = @file_get_contents($cdm_jobapi, false, stream_context_create($options));
        
        return $response;
    }
    
    public function initiateCDMRelation($user_id){ // CDMと連携し、患者基本情報（生年月日、性別等）を登録する一連の操作
        $ticketCode = $this->getCDMTicket($user_id);
        $cookie = $this->authIDP($ticketCode); // IDPに認証要求
        
        list($results, $cookie) = $this->loginCDM($ticketCode, $cookie); // CDM Login
        $cdm_ptid = $results['requestPatientId'];
        $response = $this->deleteWorkSpace($cdm_ptid, $cookie); // ワークエリア削除
        $response = $this->getWorkSpace($cdm_ptid, $cookie); // 前回データ取得（初期登録時はNULL）
        $basicQAs = $this->getBasicQA($cdm_ptid, $cookie);
        // 現在値をセット
        $basicQA = $this->prepareCurrentBasicQA($user_id, $cdm_ptid);
        
        $response = $this->saveBasicQA($cdm_ptid, $cookie, $basicQA);
        
        $response = $this->commit($cdm_ptid, $cookie);
        $data = sprintf("USER_ID=%s CDM=%s\n", $user_id, $cdm_ptid);
        file_put_contents('taka.txt', $data);
        // cdm_ptidをuser_infoテーブルに書き込む
        DB::connection('mysql_2')->table('user_info')->where('user_id', $user_id)->update(['cdm_ptid' => $cdm_ptid]);

        return redirect('./cdm');
    }
    
    public function api_newCDMRelation($user_id){
        $ticketCode = $this->getCDMTicket($user_id);
        $cookie = $this->authIDP($ticketCode); // IDPに認証要求
        
        list($results, $cookie) = $this->loginCDM($ticketCode, $cookie); // CDM Login
        $cdm_ptid = $results['requestPatientId'];
        $response = $this->deleteWorkSpace($cdm_ptid, $cookie); // ワークエリア削除
        $response = $this->getWorkSpace($cdm_ptid, $cookie); // 前回データ取得（初期登録時はNULL）
        $basicQAs = $this->getBasicQA($cdm_ptid, $cookie);
        // 現在値をセット
        $basicQA = $this->prepareCurrentBasicQA($user_id, $cdm_ptid);
        
        $response = $this->saveBasicQA($cdm_ptid, $cookie, $basicQA);
        
        $response = $this->commit($cdm_ptid, $cookie);
        $data = sprintf("USER_ID=%s CDM=%s\n", $user_id, $cdm_ptid);
        file_put_contents('taka.txt', $data);
        // cdm_ptidをuser_infoテーブルに書き込む
        DB::connection('mysql_2')->table('user_info')->where('user_id', $user_id)->update(['cdm_ptid' => $cdm_ptid]);
    }
    
    public function comitAnpi($user_id){

        $ticketCode = $this->getCDMTicket($user_id);
        $cookie = $this->authIDP($ticketCode); // IDPに認証要求

        list($results, $cookie) = $this->loginCDM($ticketCode, $cookie); // CDM Login
        $cdm_ptid = $results['requestPatientId'];
        $response = $this->deleteWorkSpace($cdm_ptid, $cookie); // ワークエリア削除
        $response = $this->getWorkSpace($cdm_ptid, $cookie); // 前回データ取得（初期登録時はNULL）
        $basicQA = $this->getBasicQA($cdm_ptid, $cookie);
        $basicQA = $this->prepareCurrentBasicQA($user_id, $cdm_ptid);
        $response = $this->saveBasicQA($cdm_ptid, $cookie, $basicQA); 
        $anpiQA = $this->prepareCurrentAnpiQA($user_id, $cdm_ptid);
        $cdm_saveapi = sprintf("%s/karte/restservice/Inquiries/work/extra/SagDM_AnpiQA", $this->cdm_hostname);
        $response = $this->saveDataViaContents($cdm_saveapi, $cdm_ptid, $cookie, $anpiQA);
        $response = $this->commit($cdm_ptid, $cookie);
        return $response;
    }
        
    public function comitSevereMedicine($user_id){
        $ticketCode = $this->getCDMTicket($user_id);
        $cookie = $this->authIDP($ticketCode); // IDPに認証要求

        list($results, $cookie) = $this->loginCDM($ticketCode, $cookie); // CDM Login
        $cdm_ptid = $results['requestPatientId'];
        $response = $this->deleteWorkSpace($cdm_ptid, $cookie); // ワークエリア削除
        $response = $this->getWorkSpace($cdm_ptid, $cookie); // 前回データ取得（初期登録時はNULL）
        $basicQA = $this->getBasicQA($cdm_ptid, $cookie);
        $basicQA = $this->prepareCurrentBasicQA($user_id, $cdm_ptid);
        $response = $this->saveBasicQA($cdm_ptid, $cookie, $basicQA); 
        $severeMedQA = $this->prepareSevereMedicineQA($user_id, $cdm_ptid);
        $cdm_saveapi = sprintf("%s/karte/restservice/Inquiries/work/extra/SagDM_SevereMedicineQA", $this->cdm_hostname);
        $response = $this->saveDataViaContents($cdm_saveapi, $cdm_ptid, $cookie, $severeMedQA);
        $response = $this->commit($cdm_ptid, $cookie);
        var_dump($response);
    }
    
    public function getLastBasicQA($user_id){
        $ticketCode = $this->getCDMTicket($user_id);
        $cookie = $this->authIDP($ticketCode); // IDPに認証要求

        list($results, $cookie) = $this->loginCDM($ticketCode, $cookie); // CDM Login
        $cdm_ptid = $results['requestPatientId'];
        $response = $this->deleteWorkSpace($cdm_ptid, $cookie); // ワークエリア削除
        $response = $this->getWorkSpace($cdm_ptid, $cookie); // 前回データ取得（初期登録時はNULL）
        $basicQAs = $this->getBasicQA($cdm_ptid, $cookie);
        
        $json_basicQAs = json_decode($basicQAs, true);
        $hintSection = $json_basicQAs['hintSection'];
        $QGroups = $json_basicQAs['QGroup'];
        $nQ = count($QGroups);
        
        foreach($this->hintsections as $key=>$label){
            printf("%s:%s<br>", $label, $hintSection[$key]);
        }
        
        foreach($QGroups as $QGroup){
            $keys = array_keys($QGroup);
            $Qs = $QGroup['Q'];
            foreach($Qs as $Q){
                $answerType = $Q['@answerType'];
                switch($answerType){
                    case 'free':
                        if(isset($Q['freeText'])){
                            printf("%s:%s<br>", $Q['sentence']['code'],$Q['freeText']);
                        }
                        break;
                    case 'single':
                        if(isset($Q['selectedAnswer'])){
                            printf("%s:%s<br>", $Q['sentence']['code'], $Q['selectedAnswer']['sentence']['code']);
                        }
                        break;
                }
                
            }
        }
    }
    
    
    public function member_index(){
        $cdm_members = DB::connection('mysql_2')->table('user_info')->whereNotNull('cdm_ptid')->Where('cdm_ptid', '!=',"")->paginate(10);
        return view('CDM.member_index', compact('cdm_members'));
    }
    
    public function index(){
        $medications = DB::connection('mysql_2')->table('user_drug_info')->distinct('user_id')->pluck('user_id');

        foreach ($medications as $medication) {
            $hasMedication[] = $medication;
        }
        
        $noids = DB::connection('mysql_2')->table('user_info')->whereNull('cdm_ptid')->orWhere('cdm_ptid',"")->paginate(10);
        return view('CDM.index', compact('noids', 'hasMedication'));
    }
}
