<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログインページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


//ログイン認証
require('auth.php');

//================================
//ログイン画面処理
//================================
//POST送信されていた場合
if(!empty($_POST)){
	debug('POST送信があります。');

	//変数にユーザー情報を代入
	$email = $_POST['email'];
	$pass = $_POST['pass'];
	//ログイン保持にチェックが入っているか
	$pass_save = (!empty($_POST['pass_save'])) ? true : false;


	//emailバリデーション（Email形式）
	validEmail($email, 'email');
	//emailバリデーション（最大文字数）
	validMaxLen($email, 'email');

	//passバリデーション（最大文字数・最小文字数・半角英数）
	validMaxLen($pass, 'pass');
	validMinLen($pass, 'pass');
	validHalf($pass, 'pass');

		//バリデーション（未入力）
		validRequired($email, 'email');
		validRequired($pass, 'pass');

	if(empty($err_msg)){
		debug('バリデーションOKです。');


		//例外処理
		try {
			//DB接続
			$dbh = dbConnect();
			//SQL文作成
			$sql = 'SELECT password, id FROM users WHERE email = :email AND delete_flg = 0';
			$data = array(':email' => $email);

			//クエリ実行
			$stmt = queryPost($dbh, $sql, $data);

			//クエリ結果の値を取得
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			debug('クエリ結果の中身：'.print_r($result, true));

			//パスワード照合
			//$resultの中身が空でないか&パスワードハッシュしたパスワードの値の１つ目が入っているか
				if(!empty($result) && password_verify($pass, array_shift($result))){
					debug('パスワードがマッチしました。');

				//ログイン有効期限（デフォルトを１時間とする）
					$sesLimit = 60 * 60;

				//最終ログインを現在の日時に
				//time関数は1970/01/01 00:00:00を０として、1秒経過するごとに１ずつ増加させた値が入る
					$_SESSION['login_date'] = time();

				//ログイン保持にチェックがある場合
					if($pass_save){
						debug('ログイン保持にチェックがあります。');

					//ログイン有効期限を30日にしてセット
						$_SESSION['login_limit'] = $sesLimit * 24 * 30;

				}else{
					debug('ログイン保持にチェックがありませんでした。');
					$_SESSION['login_limit'] = $sesLimit;
				}

				//ユーザーIDを格納
				$_SESSION['user_id'] = $result['id'];

				//$_SESSION変数の中身を表示
				debug('セッション変数の中身：'.print_r($_SESSION, true));

				//マイページへ
				debug('マイページへ遷移します。');
				header("Location:mypage.php");

			}else{
				debug('パスワードがマッチしませんでした。');
				$err_msg['common'] = MSG09;
			}

		} catch (Exception $e) {
			error_log('エラー発生：'. $e->getMessage());
			$err_msg['common'] = MSG07;
		}

		}
	}

debug(' 画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>



<?php
$siteTitle = 'ログイン';
require('head.php');
?>
	<body class="page-login page-1colum">

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
					<h2 class="title">nekonote</h2>
					<h2 class="title">ログイン</h2>

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

					<label>
						<input type="checkbox" name="pass_save">次回ログインを省略する
					</label>

					<div class="btn-wrap">
					<div class="btn btn-container">
						<input type="submit" class="btn btn-mid" value="ログイン">
					</div>
					</div>
					<a href="passRemindSend.php">パスワードを忘れた方はコチラ</a>
				</form>
			</div>
		</section>
		</div>
<?php
require('footer.php');
?>