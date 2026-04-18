<?php
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/initialize.php');
    
    $comment = $_POST['comment'];
    $uid = $_POST['uid'];
    $username = $_SESSION['username']; 
    
	if(!isset($_SESSION['username'])) {
		header('Location: /login');
		die();
	}

    if($_posts->canComment($username, $conn) == true) {
        $stmt = $conn->prepare('INSERT INTO comments (post, user, content) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $uid, $username, $comment);
        $stmt->execute();
        $stmt->close();
        header('Location: /art/' . $uid);
        die();
    } else {
        echo 'Please wait before commenting again!';
        die();
    }
?>
