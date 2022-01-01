<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 「　猫ちゃん登録ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


//ログイン認証
require('auth.php');

// ============================
// 画面処理
// ============================

// 画面表示用データ取得
// ============================
// GETデータを格納
$c_id = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';



// DBから商品データを取得
$dbFormData = (!empty($c_id)) ? getCats($_SESSION['user_id'], $c_id) : '';

// 新規登録画面か編集画面が判別フラグ
$edit_flg = (!empty($dbFormData)) ? true : false;

// DBから譲渡可能地域と性別を取得
$dbPrefectureData = getPrefecture();
 $dbAgeData = getAge();


debug('猫ちゃんID'.$c_id);
debug('DBデータ：'.print_r($dbFormData,true));
debug('譲渡可能地域データ：'.print_r($dbPrefectureData, true));



// パラメータ改ざんチェック
// ===============================
// GETパラメータはあるが、改ざんされている場合（URLをいじくった）、正しい商品データが取れないのでマイページへ遷移させる
if(!empty($c_id) && empty($dbFormData)){
	debug('GETパラメータの商品IDが違います。');
	header("Location:mypage.php");

}
// POST送信時処理
// ================================
if(!empty($_POST)){
	debug('POST送信があります。');
	debug('POST情報：'.print_r($_POST, true));
	debug('FILE情報：'.print_r($_FILES, true));


	// 変数にユーザー情報を代入
	$name = $_POST['name'];
	$comment = $_POST['comment'];
	$age = $_POST['age_id'];
	$gender = $_POST['gender_id'];
	$prefecture = $_POST['prefecture_id'];


	// 画像をアップロードし、パスを格納
	$pic1 = (!empty($_FILES['pic1']['name'])) ? uploadImg($_FILES['pic1'], 'pic1') : '';

	// 画像をPOSTしていない（登録していない）
	$pic1 = (empty($pic1) && !empty($dbFormData['pic1'])) ? $dbFormData['pic1'] : $pic1;

	// 画像をアップロードし、パスを格納
	$pic2 = (!empty($_FILES['pic2']['name'])) ? uploadImg($_FILES['pic2'], 'pic2') : '';

	// 画像をPOSTしていない（登録していない）
	$pic2 = (empty($pic2) && !empty($dbFormData['pic2'])) ? $dbFormData['pic2'] : $pic2;

	// 画像をアップロードし、パスを格納
	$pic3 = (!empty($_FILES['pic3']['name'])) ? uploadImg($_FILES['pic3'], 'pic3') : '';

	// 画像をPOSTしていない（登録していない）
	$pic3 = (empty($pic3) && !empty($dbFormData['pic3'])) ? $dbFormData['pic3'] : $pic3;


	// 更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う
	if(empty($dbFormData)){

		// 名前バリデーション（未入力・最大文字数チェック）
		validRequired($name, 'name');
		validMaxLen($name, 'name');


		// 年齢チェック（未入力・半角数字・最大文字数チェック）
		validSelect($age, 'age_id');

		// 譲渡可能地域チェック（セレクトボックスチェック）
		validSelect($prefecture, 'prefecture_id');


		// コメントバリデーション（未入力・最大文字数チェック）
		validRequired($comment, 'comment');
		validMaxLen($comment, 'comment');



	
	}else{
	

			if($dbFormData['name'] !== $name){
				// 名前バリデーション（未入力・最大文字数チェック）
				validRequired($name, 'name');
				validMaxLen($name, 'name');
			}

			if($dbFormData['age'] !== $age){
				// 年齢チェック（未入力・半角数字・最大文字数チェック）
				validSelect($age, 'age_id');
			}

			if($dbFormData['prefecture_id'] !== $prefecture){
				// 譲渡可能地域チェック（セレクトボックスチェック）
				validSelect($orefecture, 'prefecture_id');
			}

			if($dbFormData['comment'] !== $comment){
				// コメントバリデーション（未入力・最大文字数チェック）
				validRequired($comment, 'comment');
				validMaxLen($comment, 'comment',500);
			}

	

		
		}


			if(empty($err_msg)){
				debug('バリデーションOKです。');

		// 例外処理
				try{
			// 編集画面の場合はUPDATE文、新規登録画面の場合はINSERT文を生成
			$dbh = dbConnect();

			if($edit_flg){
				debug('DB更新です。');
				$sql = 'UPDATE cats SET name = :name, gender_id = :gender, age_id = :age, prefecture_id = :prefecture, comment = :comment, pic1 = :pic1, pic2 = :pic2, pic3 = :pic3, update_date = :update_date WHERE user_id = :u_id AND id = :c_id';
				$data = array(':name' => $name, ':gender' => $gender, ':age' => $age, ':prefecture' => $prefecture, ':comment' => $comment, ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':u_id' => $_SESSION['user_id'], ':c_id' => $c_id, ':update_date' => date('Y-m-d H:i:s'));

			}else{
				debug('DB新規登録です。');
				$sql = 'INSERT INTO cats (name, gender_id, age_id, prefecture_id, comment, pic1, pic2, pic3, user_id, create_date) VALUES (:name, :gender, :age, :prefecture, :comment, :pic1, :pic2, :pic3, :u_id, :date)';
				$data = array(':name' => $name, ':gender' => $gender, ':age' => $age, ':prefecture' => $prefecture, ':comment' => $comment, ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));

			}
			debug('SQL実行データ：'.print_r($sql, true));
			debug('流し込みデータ：'.print_r($data, true));

			// クエリ実行
			$stmt = queryPost($dbh, $sql, $data);

			// クエリ成功の場合
			if($stmt){
				$_SESSION['msg_success'] = SUC04;
				debug('マイページへ遷移');
				header("Location:mypage.php");
			}
		} catch(Exception $e){
			error_log('エラー発生：'. $e->getMessage());
			$err_msg['common'] = MSG07;
		}
		}

	}



debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = (!$edit_flg) ? '猫ちゃん新規登録' : '猫ちゃん情報更新';
require('head.php');
?>
<body class="page-registproduct page-2colum page-logined">

<!--メニュー-->
<?php
require('header.php');
?>

<!--メインコンテンツ-->
<div id="contents" class="site-width">

<!--サイドバー-->
<?php
require('sidebar.php');
?>


<!--Main-->

<section id="main">
	<div class="form-container">

				<h2 class="title"><?php echo ($edit_flg) ? '猫ちゃん情報更新' : '猫ちゃん新規登録' ; ?></h2>
				<form action="" method="post" class="form" enctype="multipart/form-data" style="box-sizing:border-box;">
					<div class="area-msg">
						<?php
						if(!empty($err_msg['common'])) echo $err_msg['common'];
						?>
					</div>
					<label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
					<span class="label-require">必須項目</span>
						<input type="text" name="name" placeholder="名前" value="<?php echo getFormData('name'); ?>">
					</label>
					<div class="area-msg">
						<?php
						if(!empty($err_msg['name'])) echo $err_msg['name'];
						?>
					</div>

					<label class="<?php if(!empty($err_msg['gender_id'])) echo 'err'; ?>"></label>
					<span class="label-require">必須項目</span>
					<div class="gender-radio">
						<label class="gender" style="display:inline-block; box-sizing:border-box;"><input type="radio" name="gender_id" value="1"<?php if(getFormData('gender_id') === "1"){ echo 'checked';} ?> required>オス</label>
						<label class="gender" style="display:inline;"><input type="radio" name="gender_id" value="2"<?php if(getFormData('gender_id') === "2"){ echo 'checked'; }?>>メス</label>
					</div>
					<div class="area-msg">
						<?php
						if(!empty($err_msg['gender_id'])) echo $err_msg['gender'];
						?>
					</div>

					<label class="<?php if(!empty($err_msg['age_id'])) echo 'err'; ?>">
					<span class="label-require">必須項目</span>
					<select name="age_id" placeholder="年齢" >
						<option value="0" <?php if(getFormData('age_id') == 0 ){ echo 'selected'; }?>>年齢</option>
						<?php
						foreach($dbAgeData as $key => $val){
						?>
						<option value="<?php echo $val['id'] ?>" <?php if(getFormData('age_id') == $val['id']){echo 'selected';} ?>>
						<?php echo $val['name']."歳"; ?>
						</option>
						<?php
						}
						?>
						</select>
					</label>
					<div class="area-msg">
						<?php
						if(!empty($err_msg['age'])) echo $err_msg['age'];
						?>
					</div>

					<label class="<?php if(!empty($err_msg['prefecture_id'])) echo 'err'; ?>">
					<span class="label-require">必須項目</span>
						<select name="prefecture_id" placeholder="譲渡可能地域" >
						<option value="0" <?php if(getFormData('prefecture_id') == 0){ echo 'selected'; }?>>譲渡可能地域</option>
						<?php
						foreach($dbPrefectureData as $key => $val){
						?>
						<option value="<?php echo $val['id'] ?>" <?php if(getFormData('prefecture_id') == $val['id']){echo 'selected';} ?>>
						<?php echo $val['name']; ?>
						</option>
						<?php
						}
						?>
						</select>
					</label>
					<div class="area-msg">
						<?php
						if(!empty($err_msg['prefecture_id'])) echo $err_msg['prefecture_id'];
						?>
					</div>

					<label class="<?php if(!empty($err_msg['comment'])) echo 'err'; ?>">
					<span class="label-require">必須項目</span>
						<textarea name="comment" id="js-count" cols="37" rows="10" style="height:150px; border: 1px solid #D9CEC7;" placeholder="自己紹介"><?php echo getFormData('comment'); ?></textarea>
					</label>
					<p class="counter-text"><span id="js-count-view">0</span>/500文字</p>
					<div class="area-msg">
						<?php
						if(!empty($err_msg['comment'])) echo $err_msg['comment'];
						?>
					</div>
					</div>

				

					<div class="file-dropcontainer" style="margin:0 auto;">
						<div style="overflow:hidden;">
				
					<div class="area-dropcontainer">
							<label class="area-drop <?php if(!empty($err_msg['pic1'])) echo 'err'; ?>">
								<input type="hidden" name="MAX_FILE_SIZE" value="3145728">
								<input type="file" name="pic1" class="input-file" style="object-fit: cover;">
								<img src="<?php echo getFormData('pic1'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic1'))) echo 'display:none; object-fit: cover;' ?>">
								<div style="margin: 0 auto;">ここに画像をドラッグ&ドロップ</div>
							</label>
							<div class="area-msg">
								<?php
								if(!empty($err_msg['pic1'])) echo $err_msg['pic1'];
								?>
							</div>
						</div>
					

					
					<div class="area-dropcontainer">
					<!-- <div style="overflow:hidden;"> -->
					<label class="area-drop <?php if(!empty($err_msg['pic2'])) echo 'err'; ?>">
						<input type="hidden" name="MAX_FILE_SIZE" value="3145728">
						<input type="file" name="pic2" class="input-file">
						<img src="<?php echo getFormData('pic2'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic2'))) echo 'display:none; object-fit: cover;' ?>">
							ここに画像をドラッグ&ドロップ
					</label>
					<div class="area-msg">
						<?php
						if(!empty($err_msg['pic2']));
						?>
					</div>
					</div>
					
					

					<div class="area-dropcontainer">
					<label class="area-drop <?php if(!empty($err_msg['pic3'])) echo 'err'; ?>">
					<input type="hidden" name="MAX_FILE_SIZE" value="3145728">
					<input type="file" name="pic3" class="input-file">
					<img src="<?php echo getFormData('pic3'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic3'))) echo 'display:none; object-fit: cover;' ?>">
							ここに画像をドラッグ&ドロップ
						
					</label>
					<div class="area-msg">
						<?php
						if(!empty($err_msg['pic3'])) echo $err_msg['pic3'];
						?>
					</div>
					</div>
					</div>
					</div>
					

					<div class="btn-wrap">
						<div class="btn btn-container" style="overflow:hidden;">
							<input type="submit" class="btn btn-mid" value="<?php echo (!$edit_flg) ? '登録する' : '更新する'; ?>">
						</div>
						</form>
					</div>



</div>
</section>


<?php
require('footer.php');
?>
