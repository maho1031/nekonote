<?php

//====================================
//ログイン認証・自動ログアウト
//====================================
//ログインしている場合
if(!empty($_SESSION['login_date'])){
	debug('ログイン済みユーザーです。');

	//現在日時が最終ログイン日時＋有効期限を超えていた場合
	if( ($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
		debug('ログイン有効期限切れです。');

		//セッションを削除する（ログアウト）
		session_destroy();

		header("Location:login.php");

	//ログイン有効期限内だった場合
	}else{
		debug('ログイン有効期限内です。');
		$_SESSION['login_date'] = time();

		//現在実行中のスクリプトファイル名がlogin.phpの場合、
		//$_SERVER['PHP_SELF']はドメインからパスを返すため、今回だとnekonote/login.phpが返ってくるので、
		//さらにbasename関数を使うことでファイル名だけを取り出せる
		if(basename($_SERVER['PHP_SELF']) === 'login.php'){
		debug('マイページへ遷移します。');
		header("Location:mypage.php");
		}
	}
//未ログインユーザーだった場合
}else{
	debug('未ログインユーザーです。');
	if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
		header("Location:login.php");
	}
}

?>