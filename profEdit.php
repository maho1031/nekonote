<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('　「　プロフィール編集ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
//画面処理
//================================
//DBからユーザーデータを取得
$dbFormData = getUser($_SESSION['user_id']);

//取得したユーザー情報を確認
debug('取得したユーザー情報[プロフ編集]：'.print_r($dbFormData, true));

//post送信されていた場合
if(!empty($_POST)){
	debug('POST送信があります。');
	debug('FILE送信があります:'.print_r($_FILES,true));


	//変数にユーザー情報を代入
	$username = $_POST['username'];
	$birthday = $_POST['birthday'];
	$tel = $_POST['tel'];
	$zip = (!empty($_POST['zip'])) ? $_POST['zip'] : 0;
	$addr = $_POST['addr'];
	$email = $_POST['email'];

	// 画像をアップロードしパスを格納
	$pic = ( !empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'], 'pic') : '';
	// 画像をPOSTしていない（登録していない）がすでにDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
	$pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;



	//DB情報と入力情報が異なる場合にバリデーションを行う
	if($dbFormData['username'] !== $username){
		//名前の最大文字数チェック
		validMaxLen($username, 'username');
	}

	if($dbFormData['tel'] !== $tel){
		//電話番号の形式チェック
		validTel($tel, 'tel');
	}

	if($dbFormData['addr'] !== $addr){
		//住所の最大文字数チェック
		validMaxLen($addr, 'addr');
	}
	if($dbFormData['zip'] !== $zip){
		//郵便番号の形式チェック
		validZip($zip, 'zip');
	}

	if($dbFormData['email'] !== $email){
		//Emailの未入力チェック
		validRequired($email, 'email');

		//Emailの最大文字数チェック
		validMaxLen($email, 'email');

		//Emailの形式チェック
		validEmail($email, 'email');

		//Emailの重複チェック
		validEmailDup($email, 'email');
	}


		if(empty($err_msg)){
			debug('[プロフ編集]バリデーションOKです。');


			//例外処理
			try {
				$dbh = dbConnect();
				$sql = 'UPDATE users SET username = :u_name, birthday = :birthday, tel = :tel, addr = :addr, zip = :zip, email = :email, pic = :pic WHERE id = :u_id';
				$data = array(':u_name' => $username, ':birthday' => $birthday, ':tel' => $tel, ':addr' => $addr, ':zip' => $zip, ':email' => $email, ':pic' => $pic, ':u_id' => $dbFormData['id']);

				$stmt = queryPost($dbh, $sql, $data);

			
			

				//クエリ成功の場合
				if($stmt){
					debug('[プロフ編集]クエリ成功です。');
					
					//マイページへ遷移
					debug('マイページへ遷移します。');
					$_SESSION['msg_success'] = SUC06;
					header("Location:mypage.php");
					
				}else{
					debug('[プロフ編集]クエリ失敗です。');
					$err_msg['common'] = MSG07;
				}
			} catch (Exception $e){
				error_log('[プロフ編集]エラーが発生しました。' . $e->getMessage());
				$err_msg['common'] = MSG07;
			}
	
			}else{
				debug('$err_msgの中身：'.print_r($err_msg,true));
			}
		}

debug('画面表示処理完了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');


?>


<?php
$siteTitle = 'プロフィール編集';
require('head.php');
?>

<body class="page-profedit page-2colum page-logined">

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

				<form action="" method="post" class="form" enctype="multipart/form-data">

				
					<h2 class="title">プロフィール編集</h2>

				
					<label class="area-drop<?php if(!empty($err_msg['pic'])) echo 'err'; ?>" style=";">
	
						<input type="hidden" name="MAX_FILE_SIZE" value="3145728">
						<input type="file" name="pic" class="input-file" style="font-size:5px;">
						<img src="<?php echo getFormData('pic'); ?>" alt="<?php echo getFormData('username'); ?>" class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display:none;' ?> height:200px; width:200px; border-radius: 50%; position: absolute;top: 0;left: 0;">
						ドラッグ&ドロップ
					</label>
					



					<div class="area-msg">
						<?php
						if(!empty($err_msg['common'])) echo $err_msg['common'];
						?>
					</div>

					<label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
						<input type="text" name="username" placeholder="名前" value="<?php echo getFormData('username'); ?>">
					</label>
					<div class="area-msg">
						<?php
						if(!empty($err_msg['username'])) echo $err_msg['username'];
						?>
					</div>

					<label class="<?php if(!empty($err_msg['birthday'])) echo 'err';?>">
					<input type="date" name="birthday" placeholder="生年月日" value="<?php echo getFormData('birthday'); ?>">
					</label>

					<div class="area-msg">
						<?php
						if(!empty($err_msg['birthday'])) echo $err_msg['birthday'];
						?>
					</div>
					

					<label class="<?php if(!empty($err_msg['tel'])) echo 'err';?>">
						<span style="font-size:12px; margin-left: 5px">※ハイフンなしでご入力ください。</span>
						<input type="text" name="tel" placeholder="電話番号" value="<?php echo getFormData('tel'); ?>">
					</label>
					<div class="area-msg">
						<?php
						if(!empty($err_msg['tel'])) echo $err_msg['tel'];
						?>
					</div>

					<label class="<?php if(!empty($err_msg['zip'])) echo 'err'; ?>">
						<span style="font-size:12px; margin-left: 5px">※ハイフンなしでご入力ください。</span>
						<input type="text" name="zip" placeholder="郵便番号" value="<?php if(!empty(getFormData('zip'))) { echo getFormData('zip'); } ?>">
					</label>
					<div class="area-msg">
						<?php
						if(!empty($err_msg['zip'])) echo $err_msg['zip'];
						?>
					</div>

					<label class="<?php if(!empty($err_msg['addr'])) echo 'err'; ?>">
						<input type="text" name="addr" placeholder="住所" value="<?php echo getFormData('addr'); ?>">
					</label>
					<div class="area-msg">
						<?php
						if(!empty($err_msg['addr'])) echo $err_msg['addr'];
						?>
					</div>

					<label class="<?php if(!empty($err_msg['email'])) echo 'err';?>">
						<input type="text" name="email" placeholder="メールアドレス" value="<?php echo getFormData('email'); ?>">
					</label>
					<div class="area-msg">
						<?php
						if(!empty($err_msg['email'])) echo $err_msg['email'];
						?>
					</div>

				


					<div class="btn-wrap">
					<div class="btn btn-container">
						<input type="submit" class="btn btn-mid" value="変更する">
					</div>
					</div>
</form>
</div>
</section>





</div>
<?php
require('footer.php');
?>