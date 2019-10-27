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
    
    public $cdm_sexes = array(
        'MALE', 'FEMALE', 'UNKNOWN'
    );
    
    public function setFreeTextToBasicQA($itemName, $value){
        // { "groupCode": "basicQa", "questionCodePath": [ "birthDay" ], "selectedCode": "", "freeText": "2015-01-01", "isEmphatic": false }
        $data = "\"groupCode\": \"basicQa\"";
        $data .= sprintf(", \"questionCodePath\": [ \"%s\" ]", $itemName);
        $data .= ", \"selectedCode\": \"\"";
        $data .= sprintf(", \"freeText\": \"%s\"", $value);
        $data .= ", \"isEmphatic\": false";
        $textdata = sprintf("{ %s }", $data);
        
        return $textdata;
    }
    
    public function setSelectedCodeToBasicQA($itemName, $value){
        // { "groupCode": "basicQa", "questionCodePath": [ "gender" ], "selectedCode": "FEMALE", "isEmphatic": false }
        $data = "\"groupCode\": \"basicQa\"";
        $data .= sprintf(", \"questionCodePath\": [ \"%s\" ]", $itemName);
        $data .= sprintf(", \"selectedCode\": \"%s\"", $value);
        $data .= ", \"isEmphatic\": false";
        $codedata = sprintf("{ %s }", $data);
        
        return $codedata;
    }
    
    public function getNewTicket() { // 初めてCDMを利用するときのチケット入手 manual section 1-1
        $cdm_jobapi = sprintf("%s/karte/restservice/trusted/PhrRequestTickets", $this->cdm_hostname);
        $ticketCode = $this->getTicket($cdm_jobapi);
        return $ticketCode;
    }

    public function getMemberTicket($patientNo) { // CDM利用経験がある場合のチケット入手 manual section 1-2
        $cdm_jobapi = sprintf("%s/karte/restservice/trusted/PhrRequestTickets/%s", $this->cdm_hostname, $patientNo);
        $ticketCode = $this->getTicket($cdm_jobapi);
        return $ticketCode;
    }

    public function getTicket($cdm_jobapi) { // hiket 入手（共通）
        $options = array(
            'http' => array('method' => 'POST')
        );

        $response = @file_get_contents($cdm_jobapi, false, stream_context_create($options));

        $contents = json_decode($response);

        $ticketCode = $contents->t; // チケットコード取得完了

        return $ticketCode;
    }
    
    public function authIDP($ticketCode){
        // マニュアル 2. Login IDP （認証）

        $cdm_jobapi = sprintf("http://cdm.srsphere.jp/welcome/login?ticket=%s", $ticketCode); // urlにgetと同じようにパラメータを付ける場合

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
        $cdm_jobapi = "http://cdm.srsphere.jp/karte/restservice/UserSessions";

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
        $cdm_jobapi = sprintf("http://cdm.srsphere.jp/karte/restservice/Inquiries/work/%s", $cdm_ptid);

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
        $cdm_jobapi = sprintf("http://cdm.srsphere.jp/karte/restservice/Inquiries/work/%s", $cdm_ptid);

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
        $cdm_jobapi = sprintf("http://cdm.srsphere.jp/karte/restservice/Inquiries/work/BasicQA/%s", $cdm_ptid);

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
    
    public function setAnswersToBasicQA($user_id){
        // 糖尿病サーバに保存されたprofileデータを取得
        $users = DB::connection('mysql_2')->table('user_info')->where('user_id', $user_id)->get();
        foreach ($users as $user) {
            $birthDay = $user->user_birth;
            $sex = $user->user_sex;
        }

        // 生年月日データをセット
        $itemName = "birthDay";
        $value = $birthDay;
        
        $answers = $this->setFreeTextToBasicQA($itemName, $value); 
        
        // 性別データをセット
        $gender = is_null($sex) ? "UNKNOWN" : $this->cdm_sexes[$sex];
        $itemName = "gender";
        $value = $gender;

        $answers .= sprintf(",\n %s", $this->setSelectedCodeToBasicQA($itemName, $value));
        
        $itemName = "answerer";
        $value = "SELF";
        
        $answers .= sprintf(",\n %s", $this->setSelectedCodeToBasicQA($itemName, $value));
        
        $itemName = "height";
        $value = "0.0";
        
        $answers .= sprintf(",\n %s", $this->setFreeTextToBasicQA($itemName, $value));
        
        $itemName = "weight";
        $value = "0.0";
        
        $answers .= sprintf(",\n %s", $this->setFreeTextToBasicQA($itemName, $value));
        
//        $itemName = "querist";
//        $value = $cdm_ptid;
//        
//        $basicQA .= sprintf(",\n %s", $this->setFreeTextToBasicQA($itemName, $value));
        
        
        return $answers;
    }
    
    public function prepareCurrentBasicQA($user_id, $cdm_ptid){
        $answers = $this->setAnswersToBasicQA($user_id);
        $basicQA = sprintf("{ \n\"patientCode\": \"%s\", \n\"answers\": [ %s ]\n }", $cdm_ptid, $answers);
        return $basicQA;
    }
    
    public function saveBasicQA($cdm_ptid, $cookie, $basicQA){
        $cdm_jobapi = "http://cdm.srsphere.jp/karte/restservice/Inquiries/work/BasicQA";

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
    
    public function initiateCDMRelation($user_id){ // 初めてCDMと連携し、患者基本情報（生年月日、性別等）を登録する一連の操作
        $ticketCode = $this->getNewTicket(); // 新規連携用チケット払い出し
        $cookie = $this->authIDP($ticketCode); // IDPに認証要求
        
        list($results, $cookie) = $this->loginCDM($ticketCode, $cookie); // CDM Login
        $cdm_ptid = $results['requestPatientId'];
        $response = $this->deleteWorkSpace($cdm_ptid, $cookie); // ワークエリア削除
        $response = $this->getWorkSpace($cdm_ptid, $cookie); // 前回データ取得（初期登録時はNULL）
        $basicQAs = $this->getBasicQA($cdm_ptid, $cookie);
        // 現在値をセット
        $basicQA = $this->prepareCurrentBasicQA($user_id, $cdm_ptid);
        
        $response = $this->saveBasicQA($cdm_ptid, $cookie, $basicQA);
        
        // cdm_ptidをuser_infoテーブルに書き込む
        DB::connection('mysql_2')->table('user_info')->where('user_id', $user_id)->update(['cdm_ptid' => $cdm_ptid]);
        
        return redirect('./cdm');
    }
    
    public function getBasicQAOfPatient($cdm_ptid){
        $ticketCode = $this->getMemberTicket($cdm_ptid);
        $cookie = $this->authIDP($ticketCode); // IDPに認証要求
        
        list($results, $cookie) = $this->loginCDM($ticketCode, $cookie); // CDM Login
        echo $results['requestPatientId'];
        
    }


    public function member_index(){
        $cdm_members = DB::connection('mysql_2')->table('user_info')->whereNotNull('cdm_ptid')->Where('cdm_ptid', '!=',"")->paginate(10);
        return view('CDM.member_index', compact('cdm_members'));
    }
    
    public function index(){
        $noids = DB::connection('mysql_2')->table('user_info')->whereNull('cdm_ptid')->orWhere('cdm_ptid',"")->paginate(10);
        return view('CDM.index', compact('noids'));
    }
}
