<?php

require('function.php');
debug('　「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' パスワード再発行認証キー入力ページ');
debug('　「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証はなし（ログインできない人が使う画面なので）

// SESSIONに認証キーがあるか確認、なければリダイレクト
if(empty($_SESSION['auth_key'])){
	header("Location:passRemindSend.php"); //認証キー送信ページへ
}

// ===============================
// 画面処理
// ===============================
// post送信されていた場合
if(!empty($_POST)){
	debug('POST送信があります。');
	debug('POST情報：'.print_r($_POST, true));

	//変数に認証キーを代入
	$auth_key = $_POST['token'];

	//未入力チェック
	validRequired($auth_key, 'token');

	if(empty($err_msg)){
	debug('未入力チェックOKです。');

		// 固定長チェック
		validLength($auth_key, 'token');
		// 半角チェック
		validHalf($auth_key, 'token');

		if(empty($err_msg)){
			debug('バリデーションOKです。');

			if($auth_key !== $_SESSION['auth_key']){
				$err_msg['common'] = MSG15;
			}

			if(time() > $_SESSION['auth_key_limit']){
				$err_msg['common'] = MSG16;

			}

			if(empty($err_msg)){
				debug('認証OKです。');

				$pass = makeRandKey();

				// 例外処理
				try {
					$dbh = dbConnect();
					$sql = 'UPDATE users SET password = :pass WHERE email = :email AND delete_flg = 0';
					$data = array(':email' => $_SESSION['auth_email'], ':pass' => password_hash($pass, PASSWORD_DEFAULT));

					//クエリ実行
					$stmt = queryPost($dbh, $sql, $data);
					

					//クエリ成功の場合
					if($stmt){
						debug('クエリ成功です。');

						// メールを送信
						$from = 'klaine2424@gmail.com';
						$to = $_SESSION['auth_email'];
						$subject = '【パスワード再発行完了】| nekonote';
						$comment = <<<EOT
本メールアドレス宛にパスワード再発行いたしました。
再発行パスワード{$pass}

////////////////////////////////////////
nekonote
////////////////////////////////////////
EOT;

						sendMail($from, $to, $subject, $comment);
						
						//セッション削除
							session_unset();
							$_SESSION['msg_success'] = SUC03;
							debug('セッション変数の中身:'.print_r($_SESSION, true));

							header("Location:login.php");

					}else{
						debug('クエリに失敗しました。');
						$err_msg['common'] = MSG07;
					}
				} catch (Exception $e){
					error_log('エラーが発生しました：'. $e->getMessage());
					$err_msg['common'] = MSG07;
				}
				}
			}
		}
	}


?>


<?php
$siteTitle = 'パスワード再発行認証';
require('head.php');
?>

<body class="page-passre page-1colum">

<!--メニュー-->
<?php
require('header.php');
?>
<p id="js-show-msg" style="display:none;" class="msg-slide">
	<?php echo getSessionFlash('msg_success'); ?>
	</p>


<!--メインコンテンツ-->
<div id="contents" class="site-width">
<section id="main">
	<div class="form-container">

		<form action="" method="post" class="form">
			<h2 class="title">パスワード認証キー</h2>

			<p>ご指定のメールアドレス宛にお送りした【パスワード再発行認証】メール内にある「認証キー」をご入力ください。</p>
			<div class="area-msg">
			<?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
			</div>

			<label class="<? if(!empty($err_nsg['token'])) echo 'err'; ?>">
			<input type="text" name="token" placeholder="認証キー" value="<?php echo getFormData('token'); ?>">
			</label>
			<div class="area-msg">
			<?php
			if(!empty($err_msg['token'])) echo $err_msg['token'];
			?>
			</div>

			<div class="btn-wrap">
			<div class="btn btn-container">
				<input type="submit" class="btn btn-mid" value="パスワード再発行">
			</div>
			<a href="mypagge.php">&lt; パスワード再発行メールを再度送信する。</a>
		</form>
		
	</div>

	</div>
</section>

</div>
<?php
require('footer.php');
?>