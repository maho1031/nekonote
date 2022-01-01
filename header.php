<header>
		<h1><a href="index.php">nekonote</a></h1>
		<nav id="top-nav">
			<ul>
				<?php
				if(empty($_SESSION['user_id'])){
				?>
				<li><a href="signup.php" class="btn btn-primary">新規会員登録</a></li>
				<li><a href="login.php">ログイン</a></li>
			<?php
			}else{
			?>
				<li><a href="logout.php"><i class="fas fa-sign-out-alt fa-fw icn"></i>ログアウト</a></li>
				<li><a href="mypage.php"><i class="fas fa-user-circle fa-fw icn"></i>マイページ</a></li>

			<?php
			}
			?>
			</ul>
		</nav>
	</div>
</header>