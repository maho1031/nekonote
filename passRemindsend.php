<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 　「パスワード再発行メール送信ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証はなし（ログインできない人が使う画面なので）

//=================================
//画面処理
//=================================
//POST送信されていた場合
if(!empty($_POST)){
	debug('[パス変更]POST送信があります。');
	debug('POST情報'. print_r($_POST, true));


	//変数にPOST情報を代入
	$email = $_POST['email'];

	//未入力チェック
	validRequired($email, 'email');

	if(empty($err_msg)){
		debug('未入力チェックOKです。');

		//emailの形式チェック
		validEmail($email, 'email');

		//emailの最大文字数チェック
		validMaxLen($email, 'email');

		if(empty($err_msg)){
			debug('バリデーションOKです。');

			//例外処理
			try {
				$dbh = dbConnect();
				$sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
				$data = array(':email' => $email);

				//クエリ実行
				$stmt = queryPost($dbh, $sql, $data);

				//クエリ結果の値を取得
				$result = $stmt->fetch(PDO::FETCH_ASSOC);

				//EmailがDBに登録されている場合
				if($stmt && array_shift($result)){

					debug('[パス変更]クエリ成功です。DB登録あり');
					$_SESSION['msg_success'] = SUC03;

					$auth_key = makeRandKey();

					//メール送信
					$from = 'klaine2424@gmail.com';
					$to = $email;
					$subject = '【パスワード再発行認証】| nekonote';
					$comment = <<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
下記のURLにて認証キーをご入力いただくとパスワードが再発行されます。

パスワード再発行認証キー入力ページ
認証キー{$auth_key}

/////////////////////////////
nekonote
/////////////////////////////
EOT;

						sendMail($from, $to, $subject, $comment);

						//認証に必要な情報をセッションへ保存
						$_SESSION['auth_key'] = $auth_key;
						$_SESSION['auth_email'] = $email;
						$_SESSION['auth_key_limit'] = time() * (60 * 30);//現在時刻より30分後のUNIXタイムスタンプを入れる
						debug('セッション変数の中身'.print_r($_SESSION, true));

						header("Location:passRemindRecieve.php");

					}else{
						debug('クエリに失敗したかDBに登録のないメールアドレスが入力されました。');
						$err_msg['common'] = MSG07;
					}

				} catch(Exception $e){
					error_log('エラー発生：'. $e->getMessage());
					$err_msg['common'] = MSG07;
				}
				}
			}
		}




?>

<?php
$siteTitle = 'パスワード再発行メール送信';
require('head.php');
?>

<body class="page-passre page-1colum">

<!--メニュー-->
<?php
require('header.php');
?>

<!--メインコンテンツ-->
<div id="contents" class="site-width">
<section id="main">
	<div class="form-container">

		<form action="" method="post" class="form">
			<h2 class="title">パスワード再送信</h2>

			<div class="area-msg">
			<?php
			if(!empty($err_msg['common'])) echo $err_msg['common'];
			?>
			</div>
			<p>ご指定のメールアドレス宛にパスワード再発行用のURLと認証キーをお送りいたします。</p>
			<label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
			<input type="text" name="email" placeholder="メールアドレス">
			</label>
			<div class="area-msg">
			<?php
			if(!empty($err_msg['email'])) echo $err_msg['email'];
			?>
			</div>

			<div class="btn-wrap">
			<div class="btn btn-container">
				<input type="submit" class="btn btn-mid" value="送信する">
			</div>
			<a href="mypagge.php">&lt; マイページへ戻る</a>
		</form>
		
	</div>

	</div>
</section>

</div>
<?php
require('footer.php');
?>