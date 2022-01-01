<?php
require('function.php');
debug(' 「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 「マイページ　');
debug(' 「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');



// 画面表示処理
// ========================
// ログイン認証
require('auth.php');


// 画面表示用データの取得
$u_id = $_SESSION['user_id'];
$bordData = getMyMsgsAndBord($u_id);


debug('取得した掲示板データ'.print_r($bordData, true));

debug('画面表示処理完了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>


<?php
$siteTitle = '掲示板一覧';
require('head.php');
?>
<body class="page-mypage page-2colum page-logined">

<!--メニュー-->
<?php
require('header.php');
?>

	<p id="js-show-msg" style="display:none;" class="msg-slide">
	<?php echo getSessionFlash('msg_success'); ?>
	</p>
<!--メインコンテンツ-->
<div id="contents" class="site-width">

<!--サイドバー-->
<?php
require('sidebar.php');
?>



	<!--Main-->
	<section id="main">

		<section class="list list-table">
			<h2 class="title">
				連絡掲示板一覧
			</h2>
			<table class="table">
				<thead style="text-align:left;">
					<tr>
						<th>最新送信日時</th>
						<th>メッセージ</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if(!empty($bordData)){
						foreach($bordData as $key => $val){
							if(!empty($val['msg'])){
								$msg = array_shift($val['msg']);
					?>
					<tr>
						<td><?php echo sanitize(date('Y.m.d',strtotime($msg['send_date']))); ?></td>
						<td><a href="msg.php?m_id=<?php echo sanitize($val['id']); ?>"><?php echo mb_substr(sanitize($msg['msg']),0,40); ?>...</a></td>
					</tr>


					<?php
							}else{
					?>

					<tr>
						<td>--</td>
						<td><a href="msg.php?m_id=<?php echo sanitize($val['id']); ?>">まだメッセージはありません。</a></td>
					</tr>
					<?php
							}
						}
					}
					?>

				</tbody>
			</table>
		</section>

		
	</section>

	
</div>




<?php
require('footer.php');
?>