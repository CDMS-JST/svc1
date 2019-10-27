# svc1
糖尿病アプリ管理ツール
糖尿病アプリ（doctor_api）の機能不足を補うツール群

[ベースURL https://suhtar.hospital.med.saga-u.ac.jp/svc1/](https://suhtar.hospital.med.saga-u.ac.jp/svc1/)

コントローラ|機能
---|---
UserController.php|この管理ツールを利用するためのユーザ管理（登録、管理者権限付与）を行う。
UserInfoController.php|アプリユーザの情報管理。
UserConditionController.php|アプリ利用者の位置情報（位置情報の取得に同意した利用者のみ）を地図上に表示する。
MedicationController.php|初期バージョンでは、薬剤QRコードに含まれているのに登録されていない項目があったり、YJコードをわざわざ保険収載薬剤DB内の薬剤コードに置き換えて保存していたため、QRコードを読み取っても画面に表示されない薬剤が発生していた。これらの不具合を解決するため、薬剤情報登録機能全体をここに集約した。
DrugDictionaryController.php|休薬危険薬剤データベース。糖尿病アプリから登録されたQRコードに基づき、休薬危険薬剤、準危険薬剤等を判定、YJコードを受け取って（GET）48H/1W/nullを返すAPI、管理者によるデータ更新。
CDMController.php|CDM連携処理を行う。
SurveyController.php|アンケートアプリ利用者による評価アンケート実施と集計機能。
DrugController.php|あまり意味がないもの。保険収載薬剤データベース。本来は休薬危険薬剤データベースを実装すべきだったが、保険収載薬剤データベースとなっていた。