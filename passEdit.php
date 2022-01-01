<?php

require('function.php');


debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 「　パスワード変更ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//=============================
//画面処理
//=============================
//DBからユーザー情報を取得
$userData = getUser($_SESSION['user_id']);
debug('[パス変更]取得したユーザー情報'.print_r($userData,true));

//POST送信されていた場合
if(!empty($_POST)){
	debug('[パス変更]POST送信があります。');
	debug('[パス変更]POST情報'.print_r($_POST, true));

	//変数にユーザー情報を代入
	$pass_old = $_POST['pass_old'];
	$pass_new = $_POST['pass_new'];
	$pass_new_re = $_POST['pass_new_re'];



	//未入力チェック
	validRequired($pass_old,'pass_old');
	validRequired($pass_new, 'pass_new');
	validRequired($pass_new_re, 'pass_new_re');

	if(empty($err_msg)){
		debug('[パス変更]未入力チェックOKです。');

		//古いパスワードのチェック
		validPass($pass_old, 'pass_old');

		//新しいパスワードのチェック
		validPass($pass_new, 'pass_new');

		//古いパスワードとDBパスワードを照合（DBに入っているデータと同じであれば、半角英数字チェックや最大文字数チェックは行わなくても問題ない）
		if(!password_verify($pass_old, $userData['password'])){
			$err_msg['pass_old'] = MSG12;
		}
		//新しいパスワードと古いパスワードが同じかチェック
		if($pass_old === $pass_new){
			$err_msg['pass_new'] = MSG13;

		}
		//パスワードと新しいパスワードが合っているかチェック（ログイン画面では最大・最小チェックもしていたがパスワードの方がチェックしているので問題ない）
		validMatch($pass_new, $pass_new_re, 'pass_new_re');



		if(empty($err_msg)){
			debug('[パス変更]バリデーションOKです。');


			//例外処理
				try {
					 $dbh = dbConnect();
					 $sql = 'UPDATE users SET password = :pass WHERE id = :id';
					 $data = array(':id' => $_SESSION['user_id'], ':pass' => password_hash($pass_new, PASSWORD_DEFAULT));

					 //SQL実行
					 $stmt = queryPost($dbh, $sql, $data);

					 //クエリ成功の場合
					 if($stmt){
						 debug('[パス変更]クエリ成功です。');
						$_SESSION['msg_success'] = SUC01;

						//メール送信
						$username = ($userData['username']) ? $userData['username'] : '名無し';
						$from = 'klaine2424@gmail.com';
						$to = $userData['email'];
						$subject = 'パスワード変更通知 | nekonote';
						$comment = <<<EOT
{$username}　様
パスワードが変更されました。

///////////////////////////
nekonote
///////////////////////////
EOT;

						sendMail($from, $to, $subject, $comment);

						header("Location:mypage.php");

			}else{
				debug('[パス変更]クエリ失敗です');
				$err_msg['common'] = MSG07;
			}

		} catch (Exeption $e){
			error_log('エラー発生：'. $e->getMessage());
			$err_msg['common'] = MSG07;
		}

		}
	}
}

?>


<?php
$siteTitle = 'パスワード変更';
require('head.php');
?>

<body class="page-passedit page-2colum page-logined">

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

				<form action="" method="post" class="form">
					<h2 class="title">パスワード変更</h2>
					<div class="area-msg">
					<?php
					echo getErrMsg('common');
					?>
					</div>

					<label class="<?php if(!empty($err_msg['pass_old'])) echo 'err'; ?>">
						<input type="text" name="pass_old" placeholder="古いパスワード" value="<?php echo getFormData('pass_old'); ?>">
					</label>
					<div class="area-msg">
					<?php
					echo getErrMsg('pass_old');
					?>
					</div>

					<label class="<?php if(!empty($err_msg['pass_new'])) echo 'err'; ?>">
						<input type="text" name="pass_new" placeholder="新しいパスワード" value="<?php echo getFormData('pass_new'); ?>">
					</label>
					<div class="area-msg">
					<?php
					echo getErrMsg('pass_new');
					?>
					</div>

					<label class="<?php if(!empty($err_msg['pass_new_re'])) echo 'err'; ?>">
						<input type="text" name="pass_new_re" placeholder="新しいパスワード（再入力）" value="<?php echo getFormData('pass_new_re'); ?>">
					</label>
					<div class="area-msg">
					<?php
					echo getErrMsg('pass_new_re');
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
