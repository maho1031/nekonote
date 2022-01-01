<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('　「　退会ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

//ログイン認証
require('auth.php');

//===============================
//画面処理
//===============================
//posu送信されていた場合
if(!empty($_POST)){
	debug('POST送信があります。');

	//例外処理
	try {
		//DBへ接続
		$dbh = dbConnect();
		//SQL文作成
		$sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :us_id';
		$sql2 = 'UPDATE cats SET delete_flg = 1 WHERE id = :us_id';
		$sql3 = 'UPDATE `like` SET delete_flg = 1 WHERE id = :us_id';

		//データ流し込み
		$data = array(':us_id' => $_SESSION['user_id']);

		//クエリ実行
		$stmt1 = queryPost($dbh, $sql1, $data);
		$stmt2 = queryPost($dbh, $sql2, $data);
		$stmt3 = queryPost($dbh, $sql3, $data);


		//クエリ実行成功の場合（最悪userテーブルのみ削除成功していればよしとする）
		if($stmt1 && $stmt2){
			//セッション削除
			session_destroy();
			//セッションの中身を確認
			debug('セッション変数の中身'.print_r($_SESSION, true));

			//トップページへ
			debug('トップページへ遷移します。');
			header("Location:index.php");

		}else{
			debug('クエリ失敗しました。');
			$err_msg['common'] = MSG07;
		}

	} catch (Exception $e){
		error_log('エラー発生：' . $e->getMessage());
		$err_msg['common'] = MSG07;


	}
}


?>

<?php
$siteTitle = "退会する";
require('head.php');
?>

<body class="page-login page-1colum">

<!--メニュー-->
<?php
require('header.php');
?>

<!--メインコンテンツ-->
<div id="contents" class="site-width">
<section id="main">
	<div class="form-container">

		<form action="" method="post" class="form">
			<h2 class="title">nekonote</h2>
			<h2 class="title">退会する</h2>
			<div class="area-msg">
				<?php
				if(!empty($err_msg['common'])) echo $err_msg['common'];
				?>
			</div>

			<div class="btn-wrap">
			<div class="btn btn-container">
				<input type="submit" class="btn btn-mid" value="退会する" name="submit">
			</div>
		</form>
		<a href="mypage.php">&lt;　マイページへ戻る</a>
	</div>
	
	</div>
</section>

</div>
<?php
require('footer.php');
?>