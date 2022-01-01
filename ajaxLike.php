<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 A jax');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// =================================
// Ajax 処理
// =================================

// POST送信があり、ユーザーIDがあり、ログインしている場合
if(isset($_POST['catId']) && isset($_SESSION['user_id']) && isLogin()){
	debug('POST送信があります。');
	$c_id = $_POST['catId'];
	debug('猫ちゃんID:'.$c_id);

	try{
		$dbh = dbConnect();
		$sql = 'SELECT * FROM `like` WHERE cat_id = :c_id AND user_id = :u_id';
		$data = array(':c_id' => $c_id, ':u_id' => $_SESSION['user_id']);

		$stmt = queryPost($dbh, $sql, $data);
		$resultCount = $stmt->rowCount();
		debug('$resultCountの中身：'.print_r($resultCount,true));


		// レコードが一件でもある場合
		if(!empty($resultCount)){
			debug('お気に入りを削除します。');
			// レコード削除

			$sql = 'DELETE FROM `like` WHERE cat_id = :c_id AND user_id = :u_id';
			$data = array(':c_id' => $c_id, ':u_id' => $_SESSION['user_id']);

			$stmt = queryPost($dbh, $sql, $data);

		}else{
			debug('お気に入りを登録します。');
			// レコード挿入

			$sql = 'INSERT INTO `like` (cat_id, user_id, create_date) VALUES (:c_id, :u_id, :date)';
			$data = array(':c_id' => $c_id, 'u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));

			$stmt = queryPost($dbh, $sql, $data);
		}

	

	}catch (Exception $e){
		error_log('エラー発生：'.$e->getMessage());
	}
}
debug('A jax 処理完了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>