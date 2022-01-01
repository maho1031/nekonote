<?php
require('function.php');


debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('　「トップページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// =================================
// 画面処理
// =================================
// 猫ちゃんのGETパラメータを取得
// ----------------------------------
// カレントページ
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;

// 猫ちゃん
$c_id = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
debug('$c_idの中身：'.print_r($c_id,true));

// 性別
$gender = (!empty($_GET['g_id'])) ? $_GET['g_id'] : '';

// 譲渡可能地域
$prefecture = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';

// 年齢
$age = (!empty($_GET['a_id'])) ? $_GET['a_id'] : '';

// ソート
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';


$viewData = getCatOne($c_id);
debug('$viewdataの中身'.print_r($viewData, true));


if(!is_int((int)$currentPageNum)){
	error_log('エラー発生：指定ページに不正な値が入りました。');
	header("Location:index.php");
}

// 表示件数
$listSpan = 20;
// 現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum - 1) * $listSpan);
// DBから商品データを取得
$dbCatsData = getCatsList($currentMinNum, $gender, $prefecture, $age);
$dbGenderData = getGender();
$dbAgeData = getAge();
$dbPrefectureData = getPrefecture();
$dbSoldCats = getSold();

// ========================
$today = date("Y/m/d");

// ========================

debug('画面表示処理完了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'nekonote';
require('head.php');
?>
<body class="page-top page-2colum">

<!--メニュー-->
<?php
require('header.php');
?>

<!--メインコンテンツ-->
<div id="contents" class="site-width">

	<!--サイドバー-->
	<section id="sidebar">
		<form action="" method="get">
			<h1 class="title"><a href=""><i class="fas fa-paw fa-fw icn"></i>nekonoteとは？</a></h1>
			<h1 class="title"><a href=""><i class="fas fa-question fa-fw icn"></i>よくあるご質問</a></h1>
			<h1 class="title"><i class="fas fa-search fa-fw icn"></i>絞り込み検索</h1>
			<h2 class="title">性別</h2>
			<div class="selectbox">
					<select name="g_id">
						<option value="0" <?php if(getFormData('g_id',true) == 0){echo 'selected'; } ?>>指定なし</option>
						<?php
						foreach($dbGenderData as $key => $val){
						?>
						<option value="<?php echo $val['id'] ?>" <?php if(getFormData('g_id',true) == $val['id']){echo 'selected'; }?> >
							<?php echo $val['name']; ?>
					</option>
					<?php
						}
						?>
						
					</select>
			</div>

			<h2 class="title">譲渡可能地域</h2>
			<div class="selectbox">
					<select name="p_id">
						<option value="0" <?php if(getFormData('p_id',true) == 0 ){echo 'selected';} ?>>指定なし</option>
						<?php
						foreach($dbPrefectureData as $key => $val){
						?>
						<option value="<?php echo $val['id'] ?>" <?php if(getFormData('p_id',true) == $val['id']){echo 'selected'; }?> >
						<?php echo $val['name']; ?>
						</option>
					<?php
						}
					?>
						
					</select>
			</div>

			<h2 class="title">年齢</h2>
			<div class="selectbox">
				<select name="a_id">
					<option value="0" <?php if(getFormData('a_id',true) == 0 ){echo 'selected';} ?>>指定なし</option>
					<?php
					foreach($dbAgeData as $key => $val){
					?>
					<option value="<?php echo $val['id'] ?>" <?php if(getFormData('a_id',true) == $val['id']){echo 'selected';} ?> >
					<?php echo $val['name']."歳"; ?>
				</option>
				<?php
					}
					?>
				</select>
			</div>
			<input type="submit" value="検索">
				</form>
			
			<!-- <form id="" method="get">
			<h2 class="title">キーワード検索</h2>
			<input type="seach" name="seach" value="">
			<input type="submit" value="キーワード検索">
		</form> -->
	</section>

	<!--Main-->
	<section id="main" style="overflow:hidden;">
	<form name="" method="get">
	<div class="search-title">
		<div class="search-left">
			<span class="total-num"><?php echo sanitize($dbCatsData['total']); ?></span>件の情報があります
		</div>
		<div class="search-right">
			<span class="num"><?php echo (!empty($dbCatsData['data'])) ? $currentMinNum+1 : 0; ?></span> - <span class="num"><?php echo $currentMinNum+count($dbCatsData['data']); ?></span>件 / <span class="num"><?php echo sanitize($dbCatsData['total']); ?></span>件中
		</div>
</div>
	
		<div class="panel-list">



			<?php
			foreach($dbCatsData['data'] as $key => $val):
			?>
			
			<a href="productDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&c_id='.$val['id'] : '?c_id='.$val['id']; ?>" class="panel" style="text-align: left;">
			<span class="label-require" style="<?php if(strtotime($today) > strtotime($val['update_date'])){
					echo 'background:#fff; padding: 10px 13px;';
				} ;?>">

				<?php
				if(strtotime($today) < strtotime($val['update_date'])){
					echo "新着";
				} ;?></span>
				<div class="panel-head">
	

					<img src="<?php echo sanitize($val['pic1']); ?>" alt="<?php echo sanitize($val['name']); ?>" width="150px" height = "150px" style="object-fit: cover;">
				</div>
				<div class="panel-body" style="text-align:center; margin-top:10px;">
					<p class="title"><?php echo sanitize($val['name']); ?>
					<span class="gender" style="font-weight:bold; color:#F28F79;<?php if( ($val['gender_id']) == 1) echo 'color:#79ADDB;'?>">
						<?php
						if( ($val['gender_id']) == 1){
							echo "♂";
						}else{
							echo "♀";
						}
						?></span>
				<br>
					
					<span class="age" style="display:inline-block;"><?php echo sanitize($val['age_id'])."歳"; ?></span>
					
					
				</p>
				</div>
			</a>
			<?php
			endforeach;
		
			?>

</form>
		</div>


		<div class="pagination" style="overflow:hidden">
			<ul class="pagination-list">
				<?php
				$pageColNum = 5;
				$totalPageNum = $dbCatsData['total_page'];

				if($currentPageNum == $totalPageNum && $totalPageNum >= $pageColNum){
					$minPageNum = $currentPageNum - 4;
					$maxPageNum = $currentPageNum;

				}elseif($currentPageNum == ($totalPageNum - 1) && $totalPageNum >= $pageColNum){
					$minPageNum = $currentPageNum - 3;
					$maxPageNum = $currentPageNum + 1;

				}elseif($currentPageNum == 2 && $totalPageNum >= $pageColNum){
					$minPageNum = $currentPageNum - 1;
					$maxPageNum = $currentPageNum + 3;

				}elseif($currentPageNum == 1 && $totalPageNum >= $pageColNum){
					$minPageNum = $currentPageNum;
					$maxPageNum = $currentPageNum + 4;

				}elseif($totalPageNum < $pageColNum){
					$minPageNum = 1;
					$maxPageNum = $totalPageNum;

				}else{
					$minPageNum = $currentPageNum - 2;
					$maxPageNum = $currentPageNum + 2;
				}
				?>
				<?php if($currentPageNum != 1): ?>
				<li class="list-item"><a href="?p=1">&lt;</a></li>
				<?php endif; ?>
				<?php
				for($i = $minPageNum; $i <= $maxPageNum; $i++):
				?>
				<li class="list-item <?php if($currentPageNum == $i ) echo 'active'; ?>"><a href="?p=<?php echo $i; ?>"><?php echo $i; ?></a></li>
				<?php
				endfor;
				?>
				<?php if($currentPageNum != $maxPageNum && $maxPageNum > 1): ?>
				<li class="list-item"><a href="?p=<?php echo $maxPageNum; ?>">&gt;</a></li>
				<?php endif; ?>
			</ul>
		</div>

	</section>

<div id="sidebar-right">
	<section id="sidebar-right" class="right" style="">

		<h2 class="title">新しい家族が決まりました！</h2>
		<?php
		foreach($dbSoldCats as $key => $val){
		?>
		<a href="productDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&c_id='.$val['id'] : '?c_id='.$val['id']; ?>" class="panel">
		

				<div class="panel-head">
					<img src="<?php echo sanitize($val['pic1']) ?>" alt="<?php echo sanitize($val['name']); ?>">
				</div>
			
					<div class="panel-body" style="text-align:center; margin-top:10px;">
					<p class="title"><?php echo sanitize($val['name']); ?>
					<span class="gender" style="font-weight:bold;">
						<?php
						if( ($val['gender_id']) == 1){
							echo "♂";
						}else{
							echo "♀";
						}
						?></span>
				<br>
					
					<span class="age" style="display:inline-block;"><?php echo sanitize($val['age_id'])."歳"; ?></span>
					
					
				</p>
				</div>
		<?php
		}
		?>
			</a>


		</section>
		</div>
				</div>


<!--footer-->
<?php
require('footer.php');
?>
