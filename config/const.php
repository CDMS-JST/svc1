<?php

return [
    'PREKEY' => 'SuH3160',
    'sexes' => [
        '男',
        '女',
        'その他（未入力を含む）',
    ],
//    'em_ranks' => [
//        '48H' => '休止危険薬剤',
//        '1W' => '準休止危険薬',
//    ],
    'uris' => [
        '01' => '', // 北海道
        '02' => '', // 青森県
        '03' => '', // 岩手
        '04' => '', // 宮城
        '05' => '', // 秋田
        '06' => '', // 山形
        '07' => '', // 福島
        '08' => '', // 茨城
        '09' => '', // 栃木
        '10' => '', // 群馬
        '11' => '', // 埼玉
        '12' => '', // 千葉
        '13' => 'https://kouseikyoku.mhlw.go.jp/kantoshinetsu/chousa/000106052.zip|https://kouseikyoku.mhlw.go.jp/kantoshinetsu/chousa/shitei_shikaheisetsu_r0108.zip|https://kouseikyoku.mhlw.go.jp/kantoshinetsu/chousa/shitei_shika_r0108.zip|https://kouseikyoku.mhlw.go.jp/kantoshinetsu/chousa/shitei_ikaheisetsu_r0108.zip|https://kouseikyoku.mhlw.go.jp/kantoshinetsu/chousa/shitei_yakkyoku_r0108.zip', // 東京（関東信越の医科|歯科併設医科|歯科|医科併設歯科|薬局）
        '14' => '', // 神奈川
        '15' => '', // 新潟
        '16' => 'https://kouseikyoku.mhlw.go.jp/tokaihokuriku/gyomu/gyomu/hoken_kikan/000105677.zip|https://kouseikyoku.mhlw.go.jp/tokaihokuriku/gyomu/gyomu/hoken_kikan/000105678.zip|https://kouseikyoku.mhlw.go.jp/tokaihokuriku/gyomu/gyomu/hoken_kikan/000105679.zip', // 富山（東海北陸：富山、石川、岐阜、静岡、愛知、三重の医科|歯科|薬局）
        '17' => '', // 石川
        '18' => 'https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8fukui-kikanzentai-ika.xlsx|https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8fukui-kikanzentai-sika.xlsx|https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8fukui-kikanzentai-yakkyoku.xlsx', // 福井（医科|歯科|薬局）
        '19' => '', // 山梨
        '20' => '', // 長野
        '21' => '', // 岐阜
        '22' => '', // 静岡
        '23' => '', // 愛知
        '24' => '', // 三重
        '25' => 'https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8shiga-kikanzentai-ika.xlsx|https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8shiga-kikanzentai-sika.xlsx|https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8shiga-kikanzentai-yakkyoku.xlsx', // 滋賀
        '26' => 'https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8kyoto-kikanzentai-ika.xlsx|https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8kyoto-kikanzentai-sika.xlsx|https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8kyoto-kikanzentai-yakkyoku.xlsx', // 京都
        '27' => 'https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8osaka-kikanzentai-ika.xlsx|https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8osaka-kikanzentai-sika.xlsx|https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8osaka-kikanzentai-yakkyoku.xlsx', // 大阪
        '28' => 'https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8hyogo-kikanzentai-ika.xlsx|https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8hyogo-kikanzentai-sika.xlsx|https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8hyogo-kikanzentai-yakkyoku.xlsx', // 兵庫
        '29' => 'https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8nara-kikanzentai-ika.xlsx|https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8nara-kikanzentai-sika.xlsx|https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8nara-kikanzentai-yakkyoku.xlsx', // 奈良
        '30' => 'https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8wakayama-kikanzentai-ika.xlsx|https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8wakayama-kikanzentai-sika.xlsx|https://kouseikyoku.mhlw.go.jp/kinki/tyousa/2019.8wakayama-kikanzentai-yakkyoku.xlsx', // 和歌山
        '31' => '', // 鳥取
        '32' => '', // 島根
        '33' => '', // 岡山
        '34' => '', // 広島
        '35' => '', // 山口
        '36' => '', // 徳島
        '37' => '', // 香川
        '38' => '', // 愛媛
        '39' => '', // 高知
        '40' => 'https://kouseikyoku.mhlw.go.jp/kyushu/gyomu/gyomu/hoken_kikan/r1_08_fukuoka.zip', // 福岡
        '41' => 'https://kouseikyoku.mhlw.go.jp/kyushu/gyomu/gyomu/hoken_kikan/r1_08_saga.zip', // 佐賀
        '42' => 'https://kouseikyoku.mhlw.go.jp/kyushu/gyomu/gyomu/hoken_kikan/r1_08_nagasaki.zip', // 長崎
        '43' => 'https://kouseikyoku.mhlw.go.jp/kyushu/gyomu/gyomu/hoken_kikan/r1_08_kumamoto.zip', // 熊本
        '44' => 'https://kouseikyoku.mhlw.go.jp/kyushu/gyomu/gyomu/hoken_kikan/r1_08_ooita.zip', // 大分
        '45' => 'https://kouseikyoku.mhlw.go.jp/kyushu/gyomu/gyomu/hoken_kikan/r1_08_miyazaki.zip', // 宮崎
        '46' => 'https://kouseikyoku.mhlw.go.jp/kyushu/gyomu/gyomu/hoken_kikan/r1_08_kagoshima.zip', // 鹿児島
        '47' => 'https://kouseikyoku.mhlw.go.jp/kyushu/gyomu/gyomu/hoken_kikan/r1_08_okinawa.zip', // 沖縄
    ],
    'mainhosps' => [
        '01' => [
            'name' => '佐賀大学医学部附属病院',
            'lat' => '33.284282',
            'lng' => '130.266916',
        ],
        '02' => [
            'name' => '佐賀県医療センター好生館',
            'lat' => '33.241611',
            'lng' => '130.268341',
        ],
        '03' => [
            'name' => '国立病院機構佐賀病院',
            'lat' => '33.276208',
            'lng' => '130.293535',
        ],
        '04' => [
            'name' => 'JCHO佐賀中部病院',
            'lat' => '33.264568',
            'lng' => '130.318626',
        ],
        '05' => [
            'name' => '唐津赤十字病院',
            'lat' => '33.434397',
            'lng' => '129.974069',
        ],
        '06' => [
            'name' => '国立病院機構嬉野医療センター',
            'lat' => '33.104821',
            'lng' => '129.997161',
        ],
    ],
    'drug_specials' => [
        '',
        '麻薬',
        '毒薬',
        '覚醒剤原料',
        '4',
        '向精神薬'
    ],
    'user_info_fields' => [
        'user_id' => 'ユーザーID',
        'user_name' => '氏名',
        'user_sex' => '性別',
        'user_postal' => '〒',
        'user_tel' => '電話',
//        'user_mynumber' => 'マイナンバー',
        'user_address' => '住所',
        'user_lat' => '緯度',
        'user_lng' => '経度',
        'user_birth' => '生年月日',
        'latest_time' => '最終更新日時',
    ],
    'user_drugs_fields_v0' => [
        'drug_id' => '医薬品コード',
        'day_usage' => '用法',
        'drug_num' => '用量',
        'last_day' => '最終日',
        'day_left' => '残日数',
        'em_rank' => '休薬×',
    ],
    'user_drugs_fields' => [
        'drug_id' => '薬剤コード',
        'date_prepare' => '調剤等年月日',
        'drug_name' => '薬品名称',
        'drug_dose' => '用量',
        'drug_dose_unit' => '単位',
        'drug_dosage' => '用法',
        'drug_dispense_amount' => '調剤数量',
        'drug_dispense_unit' => '調剤単位',
        'drug_form_code' => '剤型コード',
        'em_rank' => '休薬危険薬剤',
    ],
    'list_drugs_fields' => [
        'date_prepare' => '調剤等年月日',
        'drug_name' => '薬品名称',
        'drug_dose' => '用量',
        'drug_dose_unit' => '単位',
        'drug_dosage' => '用法',
        'drug_dispense_amount' => '調剤数量',
        'drug_dispense_unit' => '単位',
        'em_rank' => '休薬×',
    ],
    'druginfo_fields_v0' => [
        'drug_name' => '名称',
        'day_usage' => '用法',
        'drug_num' => '用量',
        'last_day' => '最終日',
        'day_left' => '残日数',
        'em_rank' => '休薬×',
    ],
    'druginfo_fields' => [
        'drug_id' => '薬剤コード',
        'drug_id_type' => '薬剤コード種別',
        'date_prepare' => '調剤等年月日',
        'pharmacy_name' => '調剤_医療機関等名称',
        'pharmacy_prefecture' => '調剤_医療機関等_都道府県',
        'pharmacy_table' => '調剤_点数表',
        'pharmacy_instcode' => '調剤_医療機関等コード',
        'inst_prescript_name' => '処方_医療機関名称',
        'inst_prescript_prefecture' => '処方_医療機関都道府県',
        'inst_prescript_table' => '処方_点数表',
        'inst_prescript_code' => '処方_医療機関コード',
        'prescript_dr_name' => '処方_医師_氏名',
        'prescript_dr_clinic' => '処方_医師_診療科',
        'drug_name' => '薬品名称',
        'drug_dose' => '用量',
        'drug_dose_unit' => '単位',
        'drug_dosage' => '用法',
        'drug_dispense_amount' => '調剤数量',
        'drug_dispense_unit' => '調剤単位',
        'drug_form_code' => '剤型コード',
        'drug_usage_type' => '用法コード種別',
        'drug_usage_code' => '用法コード',
        'em_rank' => '休薬危険薬剤',
    ],
    'em_ranks' => [
        '48H' => [
            'name' => '休薬危険薬剤',
            'description' => '概ね48時間以内に服薬再開が必要な薬剤',
        ],
        '1W' => [
            'name' => '準休薬危険薬剤',
            'description' => '概ね1週間以内に服薬再開が必要な薬剤',
        ],
    ],
    'em_categories_w' => [
        '', // 配列の添え字を1からにするためのダミー
        'ADH',
        'インスリン',
        'ステロイド',
        '抗凝固',
        '抗不整脈',
        '抗痙攣',
        '甲状腺',
    ],
    'em_categories_m' => [
        '', // 配列の添え字を1からにするためのダミー
        '肝炎',
        '抗血栓',
        '抗不整脈',
        '糖尿病2類',
    ],
    'em_dict_fields' => [
        'code_yk' => '薬価基準収載',
        'code_hot7' => 'HOT7',
        'code_hot9' => 'HOT9',
        'name_notified' => '告示名称',
        'unit' => '規格単位',
        'company' => '販売会社名',
    ],
    'drug_codes' => [
        '1' => 'コードなし',
        '2' => 'レセプト電算コード',
        '3' => '厚生省コード',
        '4' => 'YJコード',
        '6' => 'HOTコード',
    ],
    'hoken_tables' => [
        '1' => '医科',
        '3' => '歯科',
        '4' => '調剤',
    ],
];

