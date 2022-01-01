<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 「猫ちゃん詳細ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

// ============================
// 画面処理
// ============================

// 画面表示用データ
// ============================
// 猫ちゃんのGETパラメータを取得
$c_id = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';


// DBから猫ちゃんデータを取得
$viewData = getCatOne($c_id);

// パラメータに不正な値が入っているかチェック
if(empty($viewData)){
	error_log('エラー発生：不正な値が入りました。');
	header("Location:index.php");
}
debug('取得したDBデータ：'.print_r($viewData, true));

// POST送信されていた場合
if(!empty($_POST['submit'])){
	debug('POST送信があります。');

	// ログイン認証
	require('auth.php');

	try{
		$dbh = dbConnect();
		$sql = 'UPDATE cats SET sold_flg = 1 WHERE id = :c_id';
		$data = array(':c_id' => $c_id);

		$stmt = queryPost($dbh, $sql, $data);

		if($stmt){
			debug('猫ちゃんが譲渡されました。ID:'.print_r($c_id,true));
		}
	}catch (Exception $e){
		error_log('エラー発生：'.$e->getMessage());
	}
}


// POST送信されていた場合
if(!empty($_POST['submit'])){
	debug('POST送信があります。');

	// ログイン認証
	require('auth.php');

	try{

	// 例外処理
	$dbh = dbConnect();
	$sql = 'INSERT INTO bord (transferor, applicant, cat_id, create_date) VALUES (:t_uid, :a_uid, :c_id, :date)';
	$data = array(':t_uid' => $viewData['user_id'], ':a_uid' => $_SESSION['user_id'], ':c_id' => $c_id, ':date' => date('Y-m-d H:i:s'));

	$stmt = queryPost($dbh, $sql, $data);

	if($stmt){
		$_SESSION['msg_success'] = SUC05;
		debug('連絡掲示板へ遷移します');
		header("Location:msg.php?m_id=".$dbh->lastInsertID()); //連絡掲示板へ
	}

	}catch(Exception $e){
		error_log('エラー発生：'.$e->getMessage());
		$err_msg['common'] = MSG07;
	}
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = '里親募集の猫ちゃん';
require('head.php');
?>

	<body class="page-productDetail page-1colum">

		<!--メニュー-->
		<?php
		require('header.php');
		?>

		<!--メインコンテンツ-->
		<div id="contents" class="site-width">
		<section id="main">
			<div class="product-page">
				<div class="contents-top">
					<a href="index.php<?php echo appendGetParam(array('p_id')); ?>">&lt; 里親募集一覧に戻る</a>
				</div>
				<div class="title">
				<p>譲渡可能地域：</p><span class="badge"><?php echo sanitize($viewData['prefecture']); ?></span>

				<i class="fa fa-heart icn-like js-click-like <?php if(isLogin() &&(isLike($_SESSION['user_id'],$viewData['id']))){ echo 'active'; } ?>" aria-hidden="true" data-catid="<?php echo sanitize($viewData['id']); ?>"></i>

				</div>
				<div class="product-img-container">
					<div class="img-main">
						<img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="メイン画像：<?php echo sanitize($viewData['name']); ?>" id="js-switch-img-main">
					</div>
					<div class="img-sub">
						<img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="画像1:<?php echo sanitize($viewData['name']); ?>" class="js-switch-img-sub">
						<img src="<?php echo showImg(sanitize($viewData['pic2'])); ?>" alt="画像2:<?php echo sanitize($viewData['name']); ?>" class="js-switch-img-sub">
						<img src="<?php echo showImg(sanitize($viewData['pic3'])); ?>" alt="画像3:<?php echo sanitize($viewData['name']); ?>" class="js-switch-img-sub">
					</div>
				</div>

			<div class="product-detail">
				<p class="name" style="font-weight:bold;"><?php echo sanitize($viewData['name']); ?></p>
				<br>
				<p class="gender" style="font-size:16px;"><?php echo sanitize($viewData['gender']); ?></p>
				<p class="age" style="font-size:16px;"><?php echo sanitize($viewData['age'])."歳"; ?></p>
				<p class="detail" style="color: #444;"><?php echo sanitize($viewData['comment']); ?>
				</p>
			
			<div class="product-bottom">
				<form action="" method="post">
				<div class="contens-right">
					<input type="submit" name="submit" class="btn-product" value="応募する">
				</div>
				</form>
				</div>
			
			
			</div>
		</section>
		</div>

<?php
require('footer.php');
?>