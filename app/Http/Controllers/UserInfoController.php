<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\UserInfo;
use Carbon\Carbon;
use App\Medication;

class UserInfoController extends Controller
{
    public function applog(){
        $logs = file('/home/httpd/html/doctor_api/api/logs/logs.txt');
        $n_logs = count($logs);
        for($l=0;$l<$n_logs;++$l){
            $key = $n_logs - $l - 1 ;
            $logs_desc[$l] = $logs[$key];
        }

        return view('app_users.applog', compact('logs_desc'));
    }
    
    public function phone(Request $request){
        return view('app_users.phone.index');
    }
    
    public function index(){
        $users = DB::connection('mysql_2')->table('user_info')->orderBy('latest_time', 'desc')->paginate(10);
//        $users = DB::connection('mysql_2')->table('user_info')->get();
        return view('app_users.index', compact('users'));
    }
    
    public function summary(){
        $sexes = DB::connection('mysql_2')->table('user_info')
                ->select(DB::raw('user_sex,count(user_sex) as count_by_sex'))
                ->groupBy('user_sex')
                ->get();
        
        $birthdates = DB::connection('mysql_2')->table('user_info')
                ->pluck('user_birth');
        
        foreach($birthdates as $birthdate){
            if(strtotime($birthdate) === false){
//                printf("%s:*** NO AGE ***<br>", $birthdate);
            } else {
                $dob = Carbon::parse($birthdate);
                $age = $dob->age;
                if(isset($age_counts[$age])){
                    $age_counts[$age] += 1;
                } else {
                    $age_counts[$age] = 1;
                }
            }
        }
        
        list($label,$count) = $this->agerange_count($age_counts);
        $histlabels = json_encode($label);
        $histcounts = json_encode($count);
        
        
        foreach($sexes as $sex){
            if(isset(config('const.sexes')[$sex->user_sex])){
                if(isset($chartdata['sex'])){
                    $chartdata['sex'] .= sprintf(",%s", $sex->count_by_sex);
                } else {
                    $chartdata['sex'] = sprintf("%s", $sex->count_by_sex);
                }
            }
        }
        
        
        return view('app_users.summary', compact('sexes', 'age_counts','chartdata', 'label', 'count', 'histlabels', 'histcounts'));
    }
    
    public function agerange_count($age_counts, $range=5){
        $n_ranks = intdiv(100, $range);
        for($rank = 0; $rank < $n_ranks;++$rank){
            $start_age = $rank * $range;
            $end_age = $start_age + $range;
            $label[$rank] = sprintf("%s - %s", $start_age, $end_age - 1);
            $count[$rank] = 0;
            for($age = $start_age; $age<$end_age; ++$age){
                if(isset($age_counts[$age])) {
                    $count[$rank] += $age_counts[$age];
                }
            }
        }
        $label[$rank] = "100以上";
        $count[$rank] = 0;
        foreach($age_counts as $key=> $val){
            if($key>99) {
                $count[$rank] += $val;
            }
        }
        
        return array($label, $count);
    }
    
    public function show($user_id){

        $userinfo = $this->get_profile($user_id);
        // 主要医療機関への距離（国土地理院API利用）
        $baseurl = 'http://vldb.gsi.go.jp/sokuchi/surveycalc/surveycalc/bl2st_calc.pl?';
        $baseurl .= 'outputType=json&ellipsoid=GRS80';
        $baseurl .= sprintf("&latitude1=%s&longitude1=%s", $userinfo['user_lat'], $userinfo['user_lng']);
        
        foreach(config('const.mainhosps') as $hospkey=>$hospinfo){
            $apiurl = $baseurl;
            $apiurl .= sprintf("&latitude2=%s&longitude2=%s", $hospinfo['lat'], $hospinfo['lng']);
            $json = file_get_contents($apiurl);
            $result = json_decode($json,true);

            if(isset($result['OutputData'])){
                $distance = $result['OutputData']['geoLength']/1000; // m単位のレスポンスなのでkmに変換
                $userinfo['distance'][$hospkey] = number_format($distance, 2);
            } else {
                $userinfo['distance'][$hospkey] = $result['ExportData']['ErrMsg'];
            }
            
            
        }
        
        if($userinfo['v0']!=="1"){
            $drugs = $this->get_drugtaking($user_id);
            return view('maps.userinfo', compact('userinfo', 'drugs'));
        } else {
            $drugs = $this->get_drugtaling_v0($user_id);
            return view('maps.userinfo_v0', compact('userinfo', 'drugs'));
        }
        
        
        
        
        // 自動車通行実績api
//        $traffic_moving_api = sprintf("http://disaster-system.its-jp.org/map4/map/#map=9/%s/%s&layer=gsi", $userinfo['user_lat'], $userinfo['user_lng']);
//        return view('maps.userinfo', compact('userinfo', 'drugs', 'traffic_moving_api'));
        
        
    }
    
    public function get_profile($user_id){
        $users = DB::connection('mysql_2')->table('user_info')->where('user_id', $user_id)->get();
        foreach ($users as $user) {
            $userinfo['user_name'] = $user->user_name;
            $userinfo['user_sex'] = $user->user_sex;
            $userinfo['user_birth'] = $user->user_birth;
            $user_address = $user->user_address;
            $userinfo['user_lat'] = $user->user_lat;
            $userinfo['user_lng'] = $user->user_lng;
            $userinfo['v0'] = $user->v0;
        }

        if (($userinfo['user_lat'] != 0) || ($userinfo['user_lng'] != 0)) {
            $userinfo['latlng'] = sprintf("[%s,%s]", $userinfo['user_lat'], $userinfo['user_lng']);
        } else {
            $userinfo['latlng'] = $this->get_public_geocoding($user_address);
        }

        $dob = Carbon::parse($userinfo['user_birth']);
        $userinfo['age'] = $dob->age;
        
        return $userinfo;
    }
    
    public function get_drugtaling_v0($user_id){
        $prescriptions = DB::connection('mysql_2')->table('user_drug')->where('user_id', $user_id)->get();
        $dno = 0; // 薬剤カウント　初期化
        $drugs = array();
        foreach ($prescriptions as $prescription) {
            ++$dno;
            foreach (config('const.user_drugs_fields_v0') as $key => $label) {
                $drugs[$dno][$key] = $prescription->$key;
            }
            // drugsテーブルから薬剤名を取得
//            $drug_name = DB::connection('mysql_2')->table('drugs')->where('drug_code', $drugs[$dno]['drug_id'])->value('drug_name');
            $drug_name = Medication::where('drug_id_qr', $drugs[$dno]['drug_id'])->value('drug_name_qr');
            $drugs[$dno]['drug_name'] = $drug_name;
        }
        
        return $drugs;
    }
    
    public function get_drugtaking($user_id){
        $prescriptions = DB::connection('mysql_2')->table('user_drug_info')->where('user_id', $user_id)->get();
        $dno = 0; // 薬剤カウント　初期化
        $drugs = array();
        foreach ($prescriptions as $prescription) {
            ++$dno;
            foreach (config('const.user_drugs_fields') as $key => $label) {
                $drugs[$dno][$key] = $prescription->$key;
            }
            // drugsテーブルから薬剤名を取得
//            $drug_name = DB::connection('mysql_2')->table('drugs')->where('drug_code', $drugs[$dno]['drug_id'])->value('drug_name');
//            $drug_name = Medication::where('drug_id_qr', $drugs[$dno]['drug_id'])->value('drug_name_qr');
//            $drugs[$dno]['drug_name'] = $drug_name;
        }

        return $drugs;
    }
    
    public function get_public_geocoding($user_address){
        /* 位置情報が保存されていないとき、住所に都道府県名が記載されていれば
         * 県庁（都庁、府庁、道庁）の位置を表示する。
         * 都道府県名が記載されていないときは、佐賀大学医学部附属病院の位置を表示。
         */
        if(mb_strpos($user_address, "東京都") === false) { // 東京都以外
            if(mb_strpos($user_address, "大阪府") === false){ // 大阪府以外
                if (mb_strpos($user_address, "京都府") === false) { // 京都府以外
                    if (mb_strpos($user_address, "北海道") === false) { // 北海道以外
                        mb_regex_encoding("UTF-8");
                        if (preg_match("/県/", $user_address)) {
                            $p = mb_strpos($user_address, "県");
                            $landmark = sprintf("%s県庁", mb_substr($user_address, 0, $p));
                        } else {
                            $landmark = "佐賀大学医学部附属病院";
                        }
                    } else {
                        $landmark = "北海道庁";
                    }
                } else { // 京都府
                    $landmark = "京都府庁";
                }
            } else { // 大阪府
                $landmark = "大阪府庁";
            }
        } else {
            $landmark = "東京都庁";
        }
        
        
        // https://www.geocoding.jp/api/?v=1.1&q=%E6%9D%B1%E4%BA%AC%E9%83%BD%E5%BA%81
        $apiurl = "https://www.geocoding.jp/api/?v=1.1";
        $apiurl .= sprintf("&q=%s", $landmark);
        $result_xml = simplexml_load_file($apiurl);
        $result_arr = $this->xml2arr($result_xml);
        //        var_dump($result_arr);
        // [corrdinate][lat], [corrdinate][lng]
        $latlng = sprintf("[%s,%s]", $result_arr['coordinate']['lat'], $result_arr['coordinate']['lng']);
        return $latlng;
    }
    
    public function show_kisyo_detail(Request $request){
        $detail_url = $request->input('detail_url');
        $detail_xml = simplexml_load_file($detail_url);
        $detail_arr = $this->xml2arr($detail_xml);
        
        return view('kisyo.detail', compact('detail_arr'));
        // Control
        
        // Head
        // Body
//        $item = 1;
//        foreach($detail_arr as $key=>$body){
//            printf("<h3>%s[%s]</h3>",$item,$key);
//            echo "<br><pre>";
//            var_dump($body);
//            echo "</pre><br><br>";
//            ++$item;
//        }
        
        exit;
        printf("%s<br>",$detail_arr['Head']['Title']);
        printf("%s<br>",$detail_arr['Head']['ReportDateTime']);
//        printf("%s<br>",$detail_arr['Head']['TargetDateTime"']);
////        printf("%s<br>",$detail_arr['Head']['EventID']); // 配列なので省略
        printf("%s<br>",$detail_arr['Head']['InfoType']);
////        printf("%s<br>",$detail_arr['Head']['Serial']); // 配列なので省略
        printf("%s<br>",$detail_arr['Head']['InfoKind']);
//        printf("%s<br>",$detail_arr['Head']['InfoKindVersion']);
        printf("%s<br>",$detail_arr['Head']['Headline']['Text']);
//        echo "<br><br><br>";
////        var_dump($detail_arr['Information']);
        echo "<br><br><br>";
        var_dump($detail_arr);
    }
    
    public function get_kisyo(){
        $kisyo_regulars = $this->get_kisyo_regular();
        $kisyo_extras = $this->get_kisyo_extra();
        return view('kisyo.index', compact('kisyo_regulars', 'kisyo_extras'));
    }
    
    public function get_kisyo_regular(){
        $regular_url = "http://www.data.jma.go.jp/developer/xml/feed/regular.xml";

        $regular_xml = simplexml_load_file($regular_url);
        $regular_arr = $this->xml2arr($regular_xml);

        $regulars = $regular_arr['entry'];

        for ($r = 0; $r < count($regulars);  ++$r) {
            $kisyo_regulars[$r]['title'] = $regulars[$r]['title'];
            $kisyo_regulars[$r]['author_name'] = $regulars[$r]['author']['name'];
            $kisyo_regulars[$r]['detail_url'] = $regulars[$r]['link']['@attributes']['href'];
//            printf("<h3>%s（%s）%s</h3>", $regulars[$r]['title'],$regulars[$r]['author']['name'],$regulars[$r]['link']['@attributes']['href']);
//            var_dump($regulars[$r]);
////            foreach($regulars[$r] as $key2=>$val2){
////                printf("key2=[%s]<br>", $key2);
////            }
//            $detail_url = $regulars[$r]['link']['@attributes']['href'];
////            $detail_xml = simplexml_load_file($detail_url);
////            $detail_arr = $this->xml2arr($detail_xml);
////            var_dump($detail_arr);
//            echo "<hr>";
        }
        
        return $kisyo_regulars;
    }
    
    public function get_kisyo_extra(){
        $extra_url = "http://www.data.jma.go.jp/developer/xml/feed/extra.xml";

        $extra_xml = simplexml_load_file($extra_url);
        $extra_arr = $this->xml2arr($extra_xml);

        $extras = $extra_arr['entry'];

        for ($r = 0; $r < count($extras); ++$r) {
            $kisyo_extras[$r]['title'] = $extras[$r]['title'];
            $kisyo_extras[$r]['author_name'] = $extras[$r]['author']['name'];
            $kisyo_extras[$r]['detail_url'] = $extras[$r]['link']['@attributes']['href'];
//            printf("<h3>%s（%s）%s</h3>", $regulars[$r]['title'],$regulars[$r]['author']['name'],$regulars[$r]['link']['@attributes']['href']);
//            var_dump($regulars[$r]);
////            foreach($regulars[$r] as $key2=>$val2){
////                printf("key2=[%s]<br>", $key2);
////            }
//            $detail_url = $regulars[$r]['link']['@attributes']['href'];
////            $detail_xml = simplexml_load_file($detail_url);
////            $detail_arr = $this->xml2arr($detail_xml);
////            var_dump($detail_arr);
//            echo "<hr>";
        }

        return $kisyo_extras;
    }
    
    public static function xml2arr($xmlobj){
        $arr = array();
        if (is_object($xmlobj)) {
            $xmlobj = get_object_vars($xmlobj);
        } else {
            $xmlobj = $xmlobj;
        }
        foreach ($xmlobj as $key => $val) {
            if (is_object($xmlobj[$key])) {
                $arr[$key] = self::xml2arr($val);
            } else if (is_array($val)) {
                foreach ($val as $k => $v) {
                    if (is_object($v) || is_array($v)) {
                        $arr[$key][$k] = self::xml2arr($v);
                    } else {
                        $arr[$key][$k] = $v;
                    }
                }
            } else {
                $arr[$key] = $val;
            }
        }

        return $arr;
    }
    
    public function list_by_emrank($em_rank){
        $em_rank_jp = config('const.em_ranks')[$em_rank];
        // gai該当する処方を取得
        $checks = DB::connection('mysql_2')->table('user_drug')->where('em_rank', $em_rank)->orderBy('user_id')->distinct('user_id')->get();
        
        foreach($checks as $check){
            $userIDs[] = $check->user_id;
        }
        
        $userIDs = array_unique($userIDs);
        
        foreach($userIDs as $user_id){
            $em_prescriptions[$user_id] = DB::connection('mysql_2')->table('user_drug')->where('user_id', $user_id)->get();
            $em_profiles[$user_id] = DB::connection('mysql_2')->table('user_info')->where('user_id', $user_id)->get();
        }
        
        // 処方データをuser_id毎に整理
        foreach($em_prescriptions as $user_id => $vars){
            for($p=0;$p<count($vars);++$p){
                $prescriptions[$user_id][$p]['drug_id'] = $vars[$p]->drug_id;
                $drug_name = DB::connection('mysql_2')->table('medications')->where('drug_id_qr', $vars[$p]->drug_id)->value('drug_name_qr');
                $prescriptions[$user_id][$p]['drug_name'] = $drug_name;
                $prescriptions[$user_id][$p]['day_usage'] = $vars[$p]->day_usage;
                $prescriptions[$user_id][$p]['drug_num'] = $vars[$p]->drug_num;
                $prescriptions[$user_id][$p]['last_day'] = $vars[$p]->last_day;
                $prescriptions[$user_id][$p]['day_left'] = $vars[$p]->day_left;
                $prescriptions[$user_id][$p]['em_rank'] = $vars[$p]->em_rank;
            }
        }
        
        // 緯度、経度の範囲を日本国内に限定する。
        $lat_min = 20.0;
        $lat_max = 46.0;
        $lng_min = 122.0;
        $lng_max = 154.0;
        
        foreach($em_profiles as $user_id => $vars){
            $sexes = config('const.sexes');
            $profile = $vars[0];
            $userinfo[$user_id]['user_name'] = $profile->user_name;
            if(isset($sexes[$profile->user_sex])){
                $userinfo[$user_id]['user_sex'] = $sexes[$profile->user_sex];
            } else {
                $userinfo[$user_id]['user_sex'] = $profile->user_sex;
            }
            $userinfo[$user_id]['user_birth'] = $profile->user_birth;
            $dob = Carbon::parse($userinfo[$user_id]['user_birth']);
            $userinfo[$user_id]['user_age'] = $dob->age;
            $userinfo[$user_id]['user_postal'] = $profile->user_postal;
            $userinfo[$user_id]['user_address'] = $profile->user_address;
            $userinfo[$user_id]['user_tel'] = $profile->user_tel;
            $user_lat = $profile->user_lat;
            $user_lng = $profile->user_lng;
            $userinfo[$user_id]['user_lat'] = $profile->user_lat;
            $userinfo[$user_id]['user_lng'] = $profile->user_lng;
            $userinfo[$user_id]['latest_time'] = $profile->latest_time;
//            printf("%s %s歳 (%s)<br>",$userinfo[$user_id]['user_name'],$userinfo[$user_id]['age'],$userinfo[$user_id]['user_sex']);
            
            // マーカーに各患者の緯度・経度をセット
            if(($user_lat > $lat_min && $user_lat < $lat_max) && ($user_lng > $lng_min && $user_lng < $lng_max)) {
                if (!isset($markerList)) {
                    $markerList = sprintf("{ pos: [%s, %s], name: \"%s(%s %s)\" }", $userinfo[$user_id]['user_lat'], $userinfo[$user_id]['user_lng'], $userinfo[$user_id]['user_name'], $userinfo[$user_id]['user_age'], $userinfo[$user_id]['user_sex']);
                } else {
                    $markerList .= sprintf(", \n{ pos: [%s, %s], name: \"%s(%s %s)\" }", $userinfo[$user_id]['user_lat'], $userinfo[$user_id]['user_lng'], $userinfo[$user_id]['user_name'], $userinfo[$user_id]['user_age'], $userinfo[$user_id]['user_sex']);
                }
            }
            
        }
        
        return view('maps.userlist', compact('em_rank', 'em_rank_jp', 'prescriptions', 'userinfo', 'markerList'));
    }
    
    public function distribution(){
        $users = DB::connection('mysql_2')->table('user_info')->get();
        $lat_min = 20.0;
        $lat_max = 46.0;
        $lng_min = 122.0;
        $lng_max = 154.0;
        $p = 0;
        foreach($users as $user){
            $user_lat = $user->user_lat;
            $user_lng = $user->user_lng;
            
            if(($user_lat > $lat_min && $user_lat < $lat_max) && ($user_lng > $lng_min && $user_lng < $lng_max)) {
                ++$p;
                foreach(config('const.user_info_fields') as $key=>$label){
                    $userinfo[$p][$key] = $user->$key;
                }
                
                $userinfo[$p]['latlng'] = sprintf("[%s,%s]", $userinfo[$p]['user_lat'], $user_lng[$p]['user_lng']);
                $dob = Carbon::parse($userinfo[$p]['user_birth']);
                $userinfo[$p]['age'] = $dob->age;
                
//                $userinfo[$p]['sexjp'] = (null !== config('const.sexes')[$userinfo[$p]['user_sex']])? config('const.sexes')[$userinfo[$p]['user_sex']] : "";
                
                if(!isset($markerList)){
                    $markerList = sprintf("{ pos: [%s, %s], name: \"%s(%s %s)\" }", $userinfo[$p]['user_lat'], $userinfo[$p]['user_lng'], $userinfo[$p]['user_name'], $userinfo[$p]['age'], $userinfo[$p]['user_sex']);
                } else {
                    $markerList .= sprintf(", \n{ pos: [%s, %s], name: \"%s(%s %s)\" }", $userinfo[$p]['user_lat'], $userinfo[$p]['user_lng'], $userinfo[$p]['user_name'], $userinfo[$p]['age'], $userinfo[$p]['user_sex']);
                }

                
                // 薬剤情報取得
                $prescriptions = DB::connection('mysql_2')->table('user_drug')->where('user_id', $userinfo[$p]['user_id'])->get();
                
                $dno = 0; // 薬剤カウント　初期化
                $drugs[$p] = array();
                
                foreach ($prescriptions as $prescription) {
                    ++$dno;
                    foreach (config('const.user_drugs_fields') as $key => $label) {
                        $drugs[$p][$dno][$key] = $prescription->$key;
                    }
                    // drugsテーブルから薬剤名を取得
                    $drug_name = DB::connection('mysql_2')->table('drugs')->where('drug_code', $drugs[$p][$dno]['drug_id'])->value('drug_name');
                    $drugs[$p][$dno]['drug_name'] = $drug_name;
                }
            } else {
//                printf("（除外）%s: %s, %s<br>", $user->user_name, $user->user_lat, $user->user_lng);
            }
        }
        
        return view('maps.distribution', compact('markerList', 'userinfo'));
        
    }
}
