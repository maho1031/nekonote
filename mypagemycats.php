<?php
require('function.php');
debug(' 「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 「マイページ　');
debug(' 「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

require('auth.php');

// =========================
// 画面表示用データ取得
// =========================
$u_id = $_SESSION['user_id'];
$catData = getMyCats($u_id);
debug('取得した登録済みデータ：'.print_r($catData,true));

debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>


<?php
$siteTitle = 'マイページ';
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
		<section class="list panel-list">
			<h2 class="title">
			里親登録中の猫ちゃん
			</h2>
			<?php
			if(!empty($catData)):
				foreach($catData as $key => $val):
			?>
			<a href="registProduct.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&c_id='.$val['id'] : '?c_id='.$val['id']; ?>" class="panel">
				<div class="panel-head">
					<img src="<?php echo sanitize($val['pic1']); ?>" alt="<?php echo sanitize($val['name']); ?>" width="150px" height = "150px" style="object-fit: cover;">
				</div>
				<div class="panel-body">
					<p class="title" style="margin-top:10px;"><?php echo sanitize($val['name']); ?>
					<br>
					<span class="area">
						<?php if(($val['gender_id']) == 1){
							echo "男の子"."/";
						}else{
							echo "女の子"."/";
						};?></span>
						<span class="area"><?php echo sanitize($val['age_id'])."歳"; ?></span>
						</p>
					
				</div>
			</a>
			<?php
						endforeach;
					endif;
						?>

		</section>

		
	</section>
</div>




<?php
require('footer.php');
?>