<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/initialize.php');
    
    header('Content-Type: application/json');

    if(!isset($_SESSION['username'])) {
        echo json_encode(['status' => 'login']);
        die();
    }
    
    $user = $_SESSION['username'];
    $post = htmlspecialchars($_GET['v']);
    
    $stmt_check = $conn->prepare("SELECT 1 FROM ratings WHERE post = ? AND user = ? AND status = 'like' LIMIT 1");
    $stmt_check->bind_param("ss", $post, $user);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if($result_check->num_rows > 0) {
        $stmt_remove = $conn->prepare("DELETE FROM ratings WHERE post = ? AND user = ? AND status = 'like'");
        $stmt_remove->bind_param("ss", $post, $user);
        $stmt_remove->execute();
        $stmt_remove->close();
        
        $stmt_decrease = $conn->prepare("UPDATE posts SET likes = likes - 1 WHERE uid = ?");
        $stmt_decrease->bind_param("s", $post);
        $stmt_decrease->execute();
        $stmt_decrease->close();
        
        echo json_encode(['status' => 'like']);
    } else {
        $stmt_check_2 = $conn->prepare("SELECT 1 FROM ratings WHERE post = ? AND user = ? AND status = 'dislike' LIMIT 1");
        $stmt_check_2->bind_param("ss", $post, $user);
        $stmt_check_2->execute();
        $result_check_2 = $stmt_check_2->get_result();
        
        if($result_check_2->num_rows > 0) {
            $stmt_remove_2 = $conn->prepare("DELETE FROM ratings WHERE post = ? AND user = ? AND status = 'dislike'");
            $stmt_remove_2->bind_param("ss", $post, $user);
            $stmt_remove_2->execute();
            $stmt_remove_2->close();
            
            $stmt_decrease_2 = $conn->prepare("UPDATE posts SET dislikes = dislikes - 1 WHERE uid = ?");
            $stmt_decrease_2->bind_param("s", $post);
            $stmt_decrease_2->execute();
            $stmt_decrease_2->close();
        }
        
        $stmt_add = $conn->prepare("INSERT INTO ratings (post, user, status) VALUES (?, ?, 'like')");
        $stmt_add->bind_param("ss", $post, $user);
        $stmt_add->execute();
        $stmt_add->close();
        
        $stmt_increase = $conn->prepare("UPDATE posts SET likes = likes + 1 WHERE uid = ?");
        $stmt_increase->bind_param("s", $post);
        $stmt_increase->execute();
        $stmt_increase->close();
        
        echo json_encode(['status' => 'liked']);
    }
?>
