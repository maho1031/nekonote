<?php
ini_set('log_errors','on');
ini_set('error_log','php.log');

define('','');

function validRequired($str, $key){
	if(empty($str)){
		global $err_msg;
		$err_msg[$key] = MSG01;
	}
}

function validEmailDup($email){
	global $err_msg;
	try {
		$dbh = dbConnect();
		$sql = 'SELECT count(*) FROM users WHERE email = :email';
		$data = array(':email' => $email);
		$stmt = queryPost($dbh, $sql, $data);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if(!empty(array_shift($resylt))){
			$err_msg['email'] = MSG07;
		}
	} catch (Exception $e ){
		error_log('エラー発生' . $e->getMessage());
		$err_msg['common'] = MSG08;
	}
}
function validEmailDup($email){
	global $err_msg;
	try {
		$dbh = dbConnect();
		$sql = 'SELECT count(*) FROM users WHERE email = :email';
		$data = array(':email' => $email);
		$stmt = queryPost($dbh, $sql, $data);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!empty(array_shift($result))){
			$err_msg[$key] = MSG08;
		}
	}catch (Exception $e){
		error_log('エラー発生' . $e->getMessage());
		$err_msg['common'] = MSG07;
	}
}