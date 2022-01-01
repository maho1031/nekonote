<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　新規会員登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//POST送信されていた場合

if(!empty($_POST)){
	debug('POST送信があります。');

	//変数にユーザー情報を代入
	$email = $_POST['email'];
	$pass = $_POST['pass'];
	$pass_re = $_POST['pass_re'];

	//未入力チェック
	validRequired($email, 'email');
	validRequired($pass, 'pass');
	validRequired($pass_re, 'pass_re');

	if(empty($err_msg)){


		//Emailバリデーションチェック
		validEmail($email, 'email');
		validMaxLen($email, 'email');
		validEmailDup($email);

		//パスワードバリデーションチェック
		validHalf($pass, 'pass');
		validMinLen($pass, 'pass');
		validMaxLen($pass, 'pass');



		if(empty($err_msg)){
			debug('バリデーションOKです。');

			//パスワード再入力チェック
			validMatch($pass, $pass_re, 'pass_re');

			if(empty($err_msg)){

				//例外処理
				try {
					$dbh = dbConnect();
					$sql = 'INSERT INTO users (email,password,login_time,create_date) VALUES (:email, :pass, :login_time, :create_date)';
					$data = array(':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT),
							':login_time' => date('Y-m-d H:i:s'),
							':create_date' => date('Y-m-d H:i:s'));

					$stmt = queryPost($dbh, $sql, $data);

					//クエリ成功の場合
					if($stmt){
						//ログイン有効期限（デフォルトを１時間とする）
						$sesLimit = 60 * 60;
						//最終ログイン日時を現在日時に
						$_SESSION['login_date'] = time();
						//ログイン有効期限を１時間に
						$_SESSION['login_limit'] = $sesLimit;

						//ユーザーIDを格納
						$_SESSION['user_id'] = $dbh->lastInsertId();

						//セッション変数の中身
						debug('セッション変数の中身：'.print_r($_SESSION,true));

						//マイページへ
						header("Location:mypage.php");

					}else{
						error_log('クエリに失敗しました。');
						$err_msg['common'] = MSG07;
					}
					

				} catch (Exception $e){
					error_log('エラー発生' .$e->getMessage());
					$err_msg['common'] = MSG07;

				}
			}

		}
	}
}

debug(' 画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = '新規会員登録';
require('head.php');
?>
	<body class="page-login page-1colum">

		<!--メニュー-->
		<?php
		require('header.php')
		?>

		<!--メインコンテンツ-->
		<div id="contents" class="site-width">
		<section id="main">
			<div class="form-container">

				<form action="" method="post" class="form">
					<h2 class="title">nekonote</h2>
					<h2 class="title">新規会員登録</h2>
					<div class="area-msg">
						<?php
						if(!empty($err_msg['common'])) echo $err_msg['common'];
						?>
					</div>

					<label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
						<input type="text" name="email" placeholder="メールアドレス" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
					</label>
					<div class="area-msg">
					<?php
					if(!empty($err_msg['email'])) echo $err_msg['email'];
					?>
					</div>

					<label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
						<input type="password" name="pass" placeholder="パスワード" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
					</label>
					<div class="area-msg">
					<?php
					if(!empty($err_msg['pass'])) echo $err_msg['pass'];
					?>
					</div>

					<label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
						<input type="password" name="pass_re" placeholder="パスワード(再入力)" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
					</label>
					<div class="area-msg">
					<?php
					if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re'];
					?>
					</div>


					<div class="btn-wrap">
					<div class="btn btn-container">
						<input type="submit" class="btn btn-mid" value="新規会員登録">
					</div>
					</div>

				</form>
			</div>
		</section>
		</div>
<?php
require('footer.php');
?>