<?php
//=================================
//ログ
//=================================
//ログを取るか
ini_set('log_errors','on');
ini_set('error_log','php.log');

//================================
//デバッグ
//================================
//デバッグフラグ
$debug_flg = true;
//デバッグログ関数
function debug($str){
	global $debug_flg;
	if(!empty($debug_flg)){
		error_log('デバッグ：'.$str);
	}
}

//=================================
//セッション準備・セッション有効期限を直す
//=================================
//セッションファイルの置き場を変更する（var/tmp/以下に置くと30日は削除されない）
session_save_path("/var/tmp/");

//ガーベージコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ100分の１の確率で削除）
ini_set('session.gc_maxlifetime',60*60*24*30);

//ブラウザを閉じても削除されないようにクッキー自体の有効期限を伸ばす
ini_set('session.cookie_lifetime',60*60*24*30);

//セッションを使う
session_start();

//現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティ対策）
session_regenerate_id();

//==================================
//画面表示処理開始ログ吐き出し関数
//==================================
function debugLogStart(){
	debug('>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
	debug('セッションID:'.session_id());
	debug('セッション変数の中身:'.print_r($_SESSION,true));
	debug('現在日時のタイムスタンプ:'.time());
	if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
		debug('ログイン期限日時タイムスタンプ:'. ($_SESSION['login_date'] + $_SESSION['login_limit']));
	}
}
//====================================
//定数
//====================================
define('MSG01','入力必須項目です。');
define('MSG02', 'メールアドレスの形式で入力してください。');
define('MSG03','パスワード（再入力）が合っていません。');
define('MSG04','半角英数字のみご利用いただけます。');
define('MSG05','6文字以上で入力してください。');
define('MSG06','256文字以内で入力してください');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08', 'そのメールアドレスは既に登録されています。');
define('MSG09','メールアドレスまたはパスワードが違います。');
define('MSG10','電話番号の形式が違います。');
define('MSG11','郵便番号の形式が違います。');
define('MSG12','古いパスワードが違います。');
define('MSG13','古いパスワードと同じです。');
define('MSG14','文字で入力してください。');
define('MSG15','正しくありません。');
define('MSG16','有効期限が切れています。');
define('MSG17','半角数字のみご利用いただけます。');
define('SUC01','パスワードを変更しました。');
define('SUC02','プロフィールを変更しました。');
define('SUC03','メールを送信しました。');
define('SUC04','登録が完了しました！');
define('SUC05','譲渡が成立しました。お相手と連絡をとりましょう。');
define('SUC06','プロフィールを編集しました。');

//=====================================
//バリデーション関数
//=====================================
$err_msg = array();

function validRequired($str, $key){
	if(empty($str)){
		global $err_msg;
		$err_msg[$key] = MSG01;
	}

}
function validEmail($str, $key){
	if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
		global $err_msg;
		$err_msg[$key] = MSG02;
	}
}
function validEmailDup($email){
	global $err_msg;

	try {
		$dbh = dbConnect();
		$sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
		$data = array(':email' => $email);
		$stmt = queryPost($dbh, $sql, $data);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if(!empty(array_shift($result))){
			$err_msg['email'] = MSG08;
		}
	}catch (Exception $e){
		error_log('エラー発生:' . $e->getMessage());
		$err_msg['common'] = MSG07;
	}
}
	function validMatch($str1, $str2, $key){
		if($str1 !== $str2){
			global $err_msg;
			$err_msg[$key] = MSG03;
		}
	}

	function validHalf($str, $key){
		if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
			global $err_msg;
			$err_msg[$key] = MSG04;
		}
	}

	function validMinLen($str, $key, $min = 6){
		if(mb_strlen($str) < $min){
			global $err_msg;
			$err_msg[$key] = MSG05;
		}
	}

	function validMaxLen($str, $key, $max = 255){
		if(mb_strlen($str) > $max){
			global $err_msg;
			$err_msg[$key] = MSG06;
		}
	}

	function validTel($str, $key){
		if(!preg_match("/0\d{1,4}\d{1,4}\d{4}/",$str)){
			global $err_msg;
			$err_msg[$key] = MSG10;
		}
	}

	function validZip($str, $key){
		if(!preg_match("/^\d{7}$/",$str)){
			global $err_msg;
			$err_msg[$key] = MSG11;
		}
	}

	function validNumber($str, $key){
		if(!preg_match("/^[0-9]+$/",$str)){
			global $err_msg;
			$err_msg[$key] = MSG17;
		}
	}

	function validPass($str, $key){
		//半角英数字チェック
		validHalf($str, $key);
		//最小文字数チェック
		validMinLen($str, $key);
		//最大文字数チェック
		validMaxLen($str, $key);
	}

	//エラーメッセージ表示
	function getErrMsg($key){
		global $err_msg;
		if(!empty($err_msg[$key])){
			return $err_msg[$key];
		}
	}
	//固定長チェック
	function validLength($str, $key, $len = 8){
		if(mb_strlen($str) !== $len){
			global $err_msg;
			$err_msg[$key] = $len . MSG14;
		}
	}
	// selectboxチェック
	function validSelect($str, $key){
		if(!preg_match("/^[0-9]+$/", $str)){
			global $err_msg;
			$err_nsg[$key] = MSG15;
		}
	}


//======================================
//データベース
//======================================
//DB接続関数
function dbConnect(){

		$dsn = 'mysql:dbname=nekonote;host=localhost:8888;charset=utf8';
		$user = 'root';
		$password = 'root';
		$options = array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
		);
		$dbh = new PDO($dsn, $user, $password, $options);
		return $dbh;
	}

function queryPost($dbh, $sql, $data){
	$stmt = $dbh->prepare($sql);


	if(!$stmt->execute($data)){
		debug('クエリに失敗しました。');
		debug('SQLエラー' .print_r($stmt->errorInfo(),true));
		$err_msg['common'] = MSG07;
		return 0;
	}
	debug('クエリ成功');
	return $stmt;
}
// ============================
// ログイン認証
// ============================
function isLogin(){
	// ログインしている場合
	if(!empty($_SESSION['login_date'])){
		debug('ログイン済みユーザーです。');

		if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
			debug('ログイン有効期限オーバーです。');

			// セッション削除
			session_destroy();
			header("Location:login.php");
			return false;

		}else{
			debug('ログイン有効期限内です。');
			return true;
		}
	}else{
		debug('未ログインユーザーです。');
		header("Location:login.php");
		return false;
		
	}
}
//=============================
//ユーザーデータを取得関数
//==============================
function getUser($u_id){
	debug('ユーザー情報を取得します。');
	//例外処理
	try {
		$dbh = dbConnect();
		$sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
		$data = array(':u_id' => $u_id);
		//クエリ実行
		$stmt = queryPost($dbh, $sql, $data);

		// if($stmt){
			// debug('クエリ成功です。');
		// }else{
			// debug('クエリ失敗です。');
	// }
	if($stmt){
		return $stmt->fetch(PDO::FETCH_ASSOC);

	}else{
		return false;
	}

	} catch(Exception $e){
		error_log('エラー発生：' . $e->getMessage());

	}

	//クエリ結果のデータを返却
	// return $stmt->fetch(PDO::FETCH_ASSOC);
}
// ============================
//商品取得情報関数
// ============================
function getCats($u_id, $c_id){
	debug('里親募集中の猫ちゃん情報を取得します。');
	debug('ユーザーID'.print_r($u_id, true));
	debug('猫ちゃんID'.print_r($c_id,true));

	// 例外処理
	try {
		$dbh = dbConnect();
		$sql = 'SELECT * FROM cats WHERE user_id = :u_id AND id = :c_id AND delete_flg = 0';
		$data = array(':u_id' => $u_id, ':c_id' => $c_id);

		$stmt = queryPost($dbh, $sql, $data);

		if($stmt){
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}else{
			return false;
		}
	} catch (Exception $e){
		error_log('エラー発生：'.$e->getMessage());
	}
}
// =============================
// sold情報を取ってくる
// =============================
function getSold(){
	debug('譲渡成立情報を取得します');

	try {

		$dbh = dbConnect();
		$sql = 'SELECT * FROM cats WHERE sold_flg = 1 AND delete_flg = 0';
		$data = array();

		$stmt = queryPost($dbh, $sql, $data);

		if($stmt){
			return $stmt->fetchAll();
		}else{
			return false;
		}

	} catch(Exception $e){
		error_log('エラー発生：'.$e->getMessage());
	}
}
// =============================
// 譲渡可能地域取得関数
// =============================
function getPrefecture(){
	debug('譲渡可能地域データを取得します。');

	// 例外処理
	try {
		$dbh = dbConnect();
		$sql = 'SELECT * FROM prefecture';
		$data = array();

		$stmt = queryPost($dbh, $sql, $data);

		if($stmt){
			// クエリ結果の全データを返却
			return $stmt->fetchAll();
		}else{
			return false;
		}
	} catch(Exception $e){
		error_log('エラー発生：'.$e->getMessage());
	}
}

function getAge(){
	debug('年齢情報をageテーブルから取得します。');
	try {
		$dbh = dbConnect();
		$sql = 'SELECT * FROM age ';
		$data = array();

		$stmt = queryPost($dbh, $sql, $data);

		if($stmt){
			return $stmt->fetchAll();
		}else{
			return false;
		}
	}catch (Exception $e){
		error_log('エラー発生：'.$e->getMessage());

	}
}


function getGender(){
	debug('性別を取得します。');
	try {
		$dbh = dbConnect();
		$sql = 'SELECT * FROM gender';
		$data = array();

		$stmt = queryPost($dbh, $sql, $data);

		if($stmt){
			return $stmt->fetchAll();
		}else{
			return false;
		}
	}catch (Exception $e){
		error_log('エラー発生：'.$e->getMessage());

	}
}

//=============================
//フォーム入力保持関数
//=============================
function getFormData($str, $flg = false){

	if($flg){
		$method = $_GET;
	}else{
		$method = $_POST;
	}
	global $dbFormData;
	global $err_msg;

	if(!empty($dbFormData)){

		if(!empty($err_msg[$str])){

			if(isset($method[$str])){
				return $method[$str];

			}else{
				return $dbFormData[$str];
			}


		}else{
			if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
				return $method[$str];

			}else{
				return $dbFormData[$str];
			}
		}


	}else{
		if(isset($method[$str])){
			return $method[$str];
		}
	}
}
// ページネーション
// =============================
// 商品情報
// =============================
function getCatsList($currentMinNum = 1, $gender, $prefecture, $age, $span = 20){
	debug('商品情報を取得します。');
	// 例外処理
	try {
		$dbh = dbConnect();
		// 件数用のSQL
		$sql = 'SELECT id FROM cats WHERE sold_flg = 0';

		if(!empty($gender))	$sql .= ' AND gender_id = '.$gender;
			
			if(!empty($prefecture)) $sql .= ' AND prefecture_id = '.$prefecture;
			
			if(!empty($age)) $sql .= ' AND age_id = '.$age;

		if(!empty($sql)){
			$sql .= ' ORDER BY update_date DESC ';
		}else{
			return false;
		}
		debug('SQL見せて。。。'.print_r($sql,true));
	
	

		$data = array();

		debug('SQL:'.print_r($sql,true));

		$stmt = queryPost($dbh, $sql, $data);
		$rst['total'] = $stmt->rowCount(); //総レコード数
		$rst['total_page'] = ceil($rst['total']/$span); //総ページ数

		if(!$stmt){
			return false;
		}


			// ページング用のSQL
			$sql = 'SELECT * FROM cats WHERE delete_flg = 0 AND sold_flg = 0';

			if(!empty($gender))	$sql .= ' AND gender_id = '.$gender;
			
			if(!empty($prefecture)) $sql .= ' AND prefecture_id = '.$prefecture;
			
			if(!empty($age)) $sql .= ' AND age_id = '.$age;

			if(!empty($sql)){
				$sql .= ' ORDER BY update_date DESC ';
			}else{
				return false;
			}
			debug('SQL見せて。。。'.print_r($sql,true));
		

		$sql .= ' LIMIT :span OFFSET :currentMinNum';

		
		$data = array();

		debug('SQL:'.print_r($sql,true));

		$stmt = $dbh->prepare($sql);
		$stmt->bindParam(':span' ,$span, PDO::PARAM_INT);
		$stmt->bindParam(':currentMinNum', $currentMinNum, PDO::PARAM_INT);

		if($stmt->execute()){
			$rst['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $rst;

		}else{
			return false;
		}

	} catch (Exception $e){
		error_log('エラーが発生しました。'. $e->getMessage());
	}
}

// =============================
// 商品詳細情報取得
// =============================
function getCatOne($c_id){
	debug('猫ちゃん情報を取得します。');
	debug('猫ちゃんID：'.print_r($c_id,true));

	// 例外処理
	try {
		$dbh = dbConnect();
		$sql = 'SELECT c.id, c.name, c.gender_id, c.age_id, c.comment, c.pic1, c.pic2, c.pic3, c.user_id, c.create_date, c.update_date, p.name AS prefecture, 
				a.name AS age, g.name AS gender FROM cats AS c LEFT JOIN prefecture AS p ON c.prefecture_id = p.id LEFT JOIN age AS a ON c.age_id = a.id 
				LEFT JOIN gender AS g ON c.gender_id = g.id WHERE c.id = :c_id AND c.delete_flg = 0 AND p.delete_flg = 0';

		$data = array(':c_id' => $c_id);

		$stmt = queryPost($dbh, $sql, $data);

		if($stmt){
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}else{
			return false;
		}
	}catch (Exception $e){
		error_log('エラー発生：'.$e->getMessage());
	}
}

// ============================
// マイページ用
// ============================
function getMyCats($u_id){
	debug('登録中の猫ちゃん情報を取得します');
	debug('ユーザーID:'.$u_id);

	try {

		$dbh = dbConnect();
		$sql = 'SELECT * FROM cats WHERE user_id = :u_id AND delete_flg = 0 AND sold_flg = 0';
		$data = array(':u_id' => $u_id);

		$stmt = queryPost($dbh, $sql, $data);

		if($stmt){
			return $stmt->fetchAll();

		}else{
			return false;
		}

	} catch(Exception $e){
		error_log('エラー発生：'.$e->getMessage());
	}

}
function getMyLike($u_id){
	debug('お気に入りデータを取得します。');
	debug('ユーザーID:'.$u_id);


	try {
		$dbh = dbConnect();
		$sql = 'SELECT * FROM `like` AS l LEFT JOIN cats AS c ON l.cat_id = c.id WHERE l.user_id = :u_id';
		$data = array(':u_id' => $u_id);

		$stmt = queryPost($dbh, $sql, $data);

		if($stmt){
			 return $stmt->fetchAll();
		}else{
			return false;
		}

	}catch(Exception $e){
		error_log('エラー発生：'.$e->getMessage());
	}



}
// =============================
// 画像処理
// =============================
function uploadImg($file, $key){
	debug('ファイルアップロード処理開始');
	debug('FILE情報:'.print_r($file, true));

	// エラーが入っているかまた入っているエラーが数値型かtrueかfallseで値が帰ってくる
	if(isset($file['error']) && is_int($file['error'])){

		try{
			// バリデーション
			// file['error]の値を確認。配列内には[UPLOAD_ERR_OK]などの定数が入っている。
			// 「UPLOAD_ERR_OK」などの定数はphpファイルでファイルアップロード時に自動的に定義される。定数には値として0や１などの数値が入っている
			switch ($file['error']){

				case UPLOAD_ERR_OK:
				break;

				case UPLOAD_ERR_NO_FILE:
					throw new RuntimeException('ファイルが選択されていません');

				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					throw new RuntimeException('ファイルサイズが大きすぎます');

				default:
				throw new RuntimeException('その他のエラーが発生しました');

			}

			// $file['mime']の値はブラウザ側で偽装可能なので、MIMEタイプを自前でチェックする
			// exif_imagetype関数は「IMAGETYPE_GIF」「IMAGETYPE_JPEG」などの定数を返す
			$type = @exif_imagetype($file['tmp_name']);
			if(!in_array($type,[IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG],true)){
				throw new RuntimeException('画像形式が未対応です');

			}

			// ファイルデータをSHA-1ハッシュをとってファイル名を決定し、ファイルを保存する
			// ハッシュ化しておかないとアップロードされたままのファイルで保存され、同じファイル名がアップロードされる可能性があり、
			// DBにパスを保存した場合、どっちの画像パスなのか判断つかなくなってしまう
			// img_type_to_extension関数はファイルの拡張子を取得するもの
			$path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);

			if(!move_uploaded_file($file['tmp_name'], $path)){
				throw new RuntimeException('画像の保存時にエラーが発生しました');
			}

			

			// 保存したファイルパスにパーミッション（権限）を変更する
			chmod($path, 0644);

			debug('ファイルは正常にアップロードされました。');
			debug('ファイルパス：'.$path);

			return $path;
		
	
	} catch (RuntimException $e){
		debug($e->getMessage());
		global $err_msg;
		$err_msg[$key] = $e->getMessage();
	}
}
}
// =============================
// お気に入り確認
// =============================
function isLike($u_id,$c_id){
	debug('お気に入り情報があるか確認します。');
	debug('ユーザーID:'.$u_id);
	debug('猫ちゃんID:'.$c_id);


	try {
		$dbh = dbConnect();
		$sql = 'SELECT * FROM `like` WHERE cat_id = :c_id AND user_id = :u_id';
		$data = array(':c_id' => $c_id, ':u_id' => $u_id);

		$stmt = queryPost($dbh, $sql, $data);

		if($stmt->rowCount()){
			debug('お気に入りです。');
			return true;
		}else{
			debug('お気に入りじゃありません。');
			return false;
		}


	} catch (Exception $e){
		error_log('エラー発生：'.$e->getMessage());
	}


}


// =============================
// 連絡掲示板
// =============================
function getMsgsAndBord($id){
	debug('msg情報を取得します');
	debug('掲示板ID:'.print_r($id,true));

	try {
		$dbh = dbConnect();
		$sql = 'SELECT m.id AS m_id, cat_id, bord_id, send_date, to_user, from_user, transferor, applicant, msg, b.create_date FROM `message` AS m RIGHT JOIN bord AS b ON b.id = m.bord_id WHERE b.id = :id
				 ORDER BY send_date ASC';

		$data = array(':id' => $id);

		$stmt = queryPost($dbh, $sql, $data);

		if($stmt){
			return $stmt->fetchAll();
			debug('$stmtの中身って何？'.print_r($stmt,true));
		}else{
			return false;
		}
	
	} catch(Exception $e){
		error_log('エラー発生：'.$e->getMessage());
	}

}
// =============================
// 連絡掲示板取得
// =============================
function getMyMsgsAndBord($u_id){
	debug('自分の掲示板情報を取得します。');
	debug('自分のユーザーID:'.$u_id);

	try {
		$dbh = dbConnect();
		$sql = 'SELECT * FROM bord AS b WHERE b.transferor = :id OR b.applicant = :id AND b.delete_flg = 0';
		$data = array(':id' => $u_id);

		$stmt = queryPost($dbh, $sql, $data);

		// 自分の掲示板データを全部取る
		$rst = $stmt->fetchAll();

		if(!empty($rst)){
			foreach($rst as $key => $val){

				// (掲示板IDのメッセージをごっそり取ってくる)
				// bord_idは＄m＿idだよ
				$sql = 'SELECT * FROM `message` WHERE bord_id = :id AND delete_flg = 0 ORDER BY send_date DESC';
				$data = array(':id' => $val['id']);
				$stmt = queryPost($dbh, $sql, $data);
				$rst[$key]['msg'] = $stmt->fetchAll();
			}
		}
		if($stmt){
			return $rst;
		}else{
			return false;
		}

	}catch (Exception $e){
		error_log('エラー発生：'.$e->getMessage());
	}
}
//==============================
//メール送信　passEdit.php
//==============================
function sendMail($from, $to, $subject, $comment){
	if(!empty($to) && !empty($subject) && !empty($comment)){

		//文字化けしないように設定（お決まりパターン）
		//現在使っている言語を設定する
		mb_language('Japanese');
		//内部の日本語をどうエンコーディング（機械がわかる言葉へ変換）するかを設定
		mb_internal_encoding('UTF-8');

		//メールを送信（送信結果はtrueかfalseで返ってくる）
		$result= mb_send_mail($to, $subject, $comment, "From: ".$from);

		//送信結果を判定
			if($result){
				debug('メールが送信されました。');

		}else{
			debug('メールの送信に失敗しました。');

		}
	}
}
//=========================
//sessionを１回だけ取得できる
//=========================
function getSessionFlash($key){
	if(!empty($_SESSION[$key])){
		$data = $_SESSION[$key];
		$_SESSION[$key] = '';
		return $data;
	}
}
// ===========================
// 認証キー生成
// ===========================
function makeRandKey($length = 8){
	static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
	$str = '';
	for ($i = 0; $i < $length; ++$i){
		$str .= $chars[mt_rand(0,61)];
	}
	return $str;
}
// =============================
// サニタイズ
// =============================
function sanitize($str){
	return htmlspecialchars($str,ENT_QUOTES);
}
// 画像表示用関数
function showImg($path){
	if(empty($path)){
		return 'img/sample-img.png';
	}else{
		return $path;
	}
}
// ================================
// GETパラメータ付与
// ================================
// $del_key：付与から取り除きたいGETパラメータのキー

function appendGetParam($arr_del_key = array()){
	if(!empty($_GET)){
		$str = '?';
		foreach($_GET as $key => $val){
		if(!in_array($key, $arr_del_key, true)){
			$str .= $key .'='.$val."&";
		}
	}
	$str = mb_substr($str, 0, -1, "UTF-8");
	debug('$strの中身：'.print_r($str,true));
	return $str;
}
}
