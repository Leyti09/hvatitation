<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/initialize.php');
    
    if (!isset($_SESSION['username'])) {
        echo json_encode(['status' => 'login']);
        die();
    }
    
    $subscriber = $_SESSION['username'];
    $channel = htmlspecialchars($_GET['user']);
    
    if ($subscriber == $channel) {
        echo json_encode(['status' => 'Error']);
        die();
    }
    
    $stmt_check = $conn->prepare("SELECT 1 FROM subscriptions WHERE subscriber = ? AND channel = ? LIMIT 1");
    $stmt_check->bind_param("ss", $subscriber, $channel);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        $stmt_remove = $conn->prepare("DELETE FROM subscriptions WHERE subscriber = ? AND channel = ?");
        $stmt_remove->bind_param("ss", $subscriber, $channel);
        $stmt_remove->execute();
        $stmt_remove->close();
    
        $stmt_decrease = $conn->prepare("UPDATE users SET followers = followers - 1 WHERE username = ?");
        $stmt_decrease->bind_param("s", $channel);
        $stmt_decrease->execute();
        $stmt_decrease->close();
    
        echo json_encode(['status' => 'Follow']);
    } else {
        $stmt_add = $conn->prepare("INSERT INTO subscriptions (subscriber, channel) VALUES (?, ?)");
        $stmt_add->bind_param("ss", $subscriber, $channel);
        $stmt_add->execute();
        $stmt_add->close();
    
        $stmt_increase = $conn->prepare("UPDATE users SET followers = followers + 1 WHERE username = ?");
        $stmt_increase->bind_param("s", $channel);
        $stmt_increase->execute();
        $stmt_increase->close();
    
        echo json_encode(['status' => 'Following']);
    }
    
    $stmt_check->close();
    $conn->close();
?>
