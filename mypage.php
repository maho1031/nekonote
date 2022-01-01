<?php
require('function.php');
debug(' 「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 「マイページ　');
debug(' 「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

require('auth.php');



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
		<section class="list list-table">
			<h2 class="title">
				お知らせ
			</h2>
			<table class="table">
				<thead>
					<tr>
						<th>最新更新日時</th>

					</tr>
				</thead>
				<tbody>
					<tr>
						<td>2020/08/09</td>
						<td><a href="">規約を更新いたしました。ご確認をお願いいたします。</a></td>
					</tr>

					<tr>
						<td>2020/07/19</td>
						<td><a href="">田中さんからメッセージが届いています。返信をお願いします。</a></td>
					</tr>

					<tr>
						<td>2020/04/19</td>
						<td><a href="">簡単なアンケートにお答えください。ご回答頂いた方には抽選で商品券をプレゼント！</a></td>
					</tr>
					<tr>
						<td>2020/04/19</td>
						<td><a href="">【初めての方へ】nekonoteの使い方をご説明いたします。</a></td>
					</tr>
				</tbody>
			</table>
		</section>

		</section>
	</section>

	
</div>




<?php
require('footer.php');
?>