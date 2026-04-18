<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/config.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/time.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/user.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/posts.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/helper.php');
	$_time = new _time();
	$_user = new _user();
	$_posts = new _posts();
	$_helper = new _helper();
	
	$ip = $_SERVER['REMOTE_ADDR'];
	
	if(isset($_SESSION['username'])) {
	    $_user->setIp($_SESSION['username'], $ip, $conn);
	}
	
	$_server->page_description = "";
?>