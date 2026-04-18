<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/initialize.php');
    
    if (!isset($_SESSION['username'])) {
        echo json_encode(['status' => 'not_subscribed']);
        die();
    }
    
    $subscriber = $_SESSION['username'];
    $channel = htmlspecialchars($_GET['user']);
    
    if ($subscriber == $channel) {
        echo json_encode(['status' => 'error']);
        die();
    }
    
    $stmt_check = $conn->prepare("SELECT 1 FROM subscriptions WHERE subscriber = ? AND channel = ? LIMIT 1");
    $stmt_check->bind_param("ss", $subscriber, $channel);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        echo json_encode(['status' => 'subscribed']);
    } else {
        echo json_encode(['status' => 'not_subscribed']);
    }
    
    $stmt_check->close();
    $conn->close();
?>
