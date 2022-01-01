<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('　「　連絡掲示板ページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// =====================================
// 画面処理
// =====================================
$partnerUserId = '';
$partnerUserInfo = '';
$myUserInfo = '';
$catInfo = '';

// 画面表示用データ取得
// =====================================
// GETパラメータを取得
$m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '';

// DBから掲示板とメッセージデータを取得
$viewData = getMsgsAndBord($m_id);
debug('掲示板ID:'.print_r($m_id,true));
debug('取得したDBデータ：'.print_r($viewData,true));

// パラメータに不正な値が入っているかチェック
if(empty($viewData)){
	error_log('エラー発生：指定ページに不正な値が入りました。');
	header("Location:mypage.php");
}

// 商品情報を取得
$catInfo = getCatOne($viewData[0]['cat_id']);
debug('$viewData[0]ってなんだろう：'.print_r($viewData[0],true));
// debug('ちなみに$viewData[1]って入ってますか？：'.print_r($viewData[1],true));
debug('取得したDBデータ：'.print_r($catInfo,true));

// 猫ちゃん情報が入っているかチェック
if(empty($catInfo)){
	error_log('エラー発生：猫ちゃん情報が取得できませんでした');
	header("Locaton:mypage.php");
}

// $viewdataから相手（売り手）の情報を取り出す
$dealUserIds[] = $viewData[0]['transferor'];
$dealUserIds[] = $viewData[0]['applicant'];
debug('$dealUserIdsの中身はなんですか？'.print_r($dealUserIds,true));


// もしも$dealUserIdsの中に＄_SESSION['user_id']が入っていたら
if( ($key = array_search($_SESSION['user_id'],$dealUserIds)) == true) {
	debug('$keyの中身はなんですか？（たぶん自分のuser_id）：'.print_r($key,true));

	// $dealUserIds[$key]を削除する = $dealUserIdsの中から$_SESSION['user_id']を削除する
	unset($dealUserIds[$key]);
}
// $dealUserIdsの中に値があったら$partnerUserIdに入れる
$partnerUserId = array_shift($dealUserIds);
debug('取得した相手のユーザーID:'.print_r($partnerUserId,true));

// DBから取引相手のユーザー情報を取得
if(isset($partnerUserId)){
	$partnerUserInfo = getUser($partnerUserId);
debug('取得した相手のユーザー情報：'.print_r($partnerUserInfo,true));
}

// 相手のユーザー情報が取れたかチェック
if(empty($partnerUserInfo)){
	error_log('エラー発生：相手のユーザー情報が取得できませんでした。');
	header("Location:mypage.php");
}

// DBから自分のユーザー情報をチェック
$myUserInfo = getUser($_SESSION['user_id']);
debug('取得した自分のユーザー情報：'.print_r($myUserInfo,true));

// 自分のユーザー情報が取れたかチェック
if(empty($myUserInfo)){
	error_log('エラー発生：自分のユーザー情報が取得できませんでした。');
	header("LOcation:mypage.php");
}

// POST送信されていた場合
if(!empty($_POST)){
	debug('POST送信があります');

	// ログイン認証
	require('auth.php');

	// バリデーションチェック
	$msg = (isset($_POST['msg'])) ? $_POST['msg'] : '';
	debug('$msgの中身：'.print_r($msg, true));

	// 最大文字数チェック
	validMaxLen($msg, 'msg', 500);
	// 未入力チェック
	validRequired($msg, 'msg');

	if(empty($err_msg)){
		debug('バリデーションOKです。');


		try {
			$dbh = dbConnect();
			$sql = 'INSERT INTO `message` (bord_id, send_date, to_user, from_user, msg, create_date) VALUES (:b_id, :send_date, :to_user, :from_user, :msg, :date)';
			$data = array(':b_id' => $m_id, ':send_date' => date('Y-m-d H:i:s'), ':to_user' => $partnerUserId, ':from_user' => $_SESSION['user_id'], ':msg' => $msg, ':date' => date('Y-m-d H:i:s'));

			$stmt = queryPost($dbh, $sql, $data);

			debug('$sqlの中身：'.print_r($sql,true));

			if($stmt){
				$_POST = array();
				debug('再度連絡掲示板へ遷移します。');
				header("Location:" .$_SERVER['PHP_SELF'].'?m_id='.$m_id); //自分自身に遷移する

				debug('$viewDataの中身'.print_r($viewData[0],true));
			}


		} catch (Exception $e){
			error_log('エラー発生：'.$e->getMessage());
			$err_msg['common'] = MSG07;
		}
	}

	}

debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>
<?php
$siteTitle = '連絡掲示板';
require('head.php');
?>

<p id="js-show-msg" style="display:none;" class="msg-slide">
	<?php echo getSessionFlash('msg_success'); ?>
	</p>

<body class="msg page-2colum page-logined">

<!--メニュー-->
<?php
require('header.php');
?>

<!--メインコンテンツ-->
<div id="contents" class="site-width">

<!--サイドバー-->
<section id="sidebar">
		<h2 class="title">お相手</h2>
		<div class="avatar-img">
			<img src="<?php echo showImg(sanitize($partnerUserInfo['pic']));?>" alt="" class="avatar"><br>
		</div>
		<div class="avatar-info">
			<?php echo sanitize($partnerUserInfo['username'])."さん";?><br>
			〒<?php echo sanitize($partnerUserInfo['zip'],4,"-",true); ?><br>
			住所：<?php echo sanitize($partnerUserInfo['addr']); ?><br>
			TEL:<?php echo sanitize($partnerUserInfo['tel']); ?>
		</div>
		<h2 class="title">ご応募したい猫ちゃん</h2>
		<div class="cat-img">
			<img src="<?php echo sanitize($catInfo['pic1']); ?>" alt="">
		</div>
		<div class="cat-info">
			<?php echo sanitize($catInfo['name']); ?><br>
			<?php if(($catInfo['gender_id']) == 1){
							echo "男の子"."/";
						}else{
							echo "女の子"."/";
						};?>
			<?php echo sanitize($catInfo['age_id'])."歳"; ?>
		<br>
		</div>
		
</section>


<!--Main-->

<section id="main">
	<div class="msg-info">
		<div class="area-bord" id="js-scroll-bottom">
			<?php
				if(!empty($viewData[0]['m_id'])){
					foreach($viewData as $key => $val){
						if(!empty($val['from_user']) && $val['from_user'] == $partnerUserId){
			?>
			<div class="msg-cnt msg-left">
				<div class="avatar">
					<img src="<?php echo sanitize(showImg($partnerUserInfo['pic'])); ?>" alt="" class="avatar" style="object-fit: cover;">
				</div>
				<p class="msg-inrTxt">
					<span class="triangle"></span>
					<?php echo sanitize($val['msg']); ?>
				</p>
				<div style="font-size:.5em;"><?php echo sanitize(date('Y.m.d H.i',strtotime($val['send_date']))); ?></div>
			</div>
			<?php
			}else{
			?>
			<div class="msg-cnt msg-right">
				<div class="avatar">
					<img src="<?php echo sanitize(showImg($myUserInfo['pic'])); ?>" alt="" class="avatar">
				</div>
				<p class="msg-inrTxt">
					<span class="triangle"></span>
					<?php echo sanitize($val['msg']); ?>
				</p>
				<div style="font-size:.5em; text-align:right;"><?php echo sanitize($val['send_date']); ?></div>
			</div>
			<?php
						}
					}
			?>
			<?php
				}else{
			?>
					<p style="text-align:center;line-height:20px;">メッセージ投稿はまだありません。</p>
			<?php
				}
			
			?>
		</div>

		<div class="area-send-msg">
			<form action="" method="post">
			<textarea name="msg" id="" cols="30" rows="3"></textarea>
			<input type="submit" value="送信" class="btn btn-send">
			</form>
		</div>

	</div>
</section>
</div>

<?php
require('footer.php');
?>
