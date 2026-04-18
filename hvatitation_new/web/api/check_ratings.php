<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/initialize.php');
    
    $uid = $_GET['post'];
    
    $username = $_SESSION['username'];
    
    $stmt = $conn->prepare('SELECT likes, dislikes FROM posts WHERE uid = ? LIMIT 1');
    $stmt->bind_param('s', $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $stmt_check = $conn->prepare('SELECT * FROM ratings WHERE user = ? AND post = ?');
    $stmt_check->bind_param('ss', $username, $uid);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if($result_check->num_rows > 0) {
        $row_check = $result_check->fetch_assoc();
        if ($row_check['status'] == 'like') {
            $status = 'liked';
        } elseif($row_check['status'] == 'dislike') {
            $status = 'disliked';
        }
    } else {
        $status = 'none';
    }
    
    if ($row) {
        echo json_encode([
            'likes' => $row['likes'],
            'dislikes' => $row['dislikes'],
            'status' => $status
        ]);
    }
?>
