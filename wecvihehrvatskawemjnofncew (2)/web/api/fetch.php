<?php
    require_once('/home/mathultr/hv/static/classes/initialize.php');
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    $type = $_GET['type'] ?? 'newest';
    $category = $_GET['category'] ?? null;
    $artist = $_GET['artist'] ?? null;
    $search = $_GET['search'] ?? null;
    $feedback = $_GET['feedback'] ?? 'html';
    
    $postsResult = $_posts->fetchPosts($category, $type, $artist, $search, $conn);
    
    $content = 'No posts were found!';
    
    if ($postsResult->num_rows > 0) {
        $content = '';
    
        while ($post = $postsResult->fetch_assoc()) {
            $title = htmlspecialchars($post['title']);
            $description = htmlspecialchars($post['description']);
            $image = $post['file'] ? $post['file'] : '/dynamic/art/default.jpg';
            $postUrl = '/art/' . $post['uid'];
            $date = $post['upload_date'];
            $artist = htmlspecialchars($post['artist']);
    
            $content .= '
                <div class="art-item">
                    <a href="' . $postUrl . '">
                        <img src="' . $image . '" alt="' . $title . '">
                    </a>
                    <p style="margin-bottom:0;"><a style="color:white;text-decoration:none;" href="' . $postUrl . '">' . $title . '</a></p>
                    <p style="font-size:14px;color:white;margin-top:0;">Posted by <a style="color:white;text-decoration:none;" href="/user/' . $artist . '">' . $artist . '</a><br>' . $_time->timeAgo($date) . '<br>' . number_format($post['views']) . ' views</p>
                </div>
            ';
        }
    
        $response = '200';
    } else {
        $response = '404';
    }
    
    header('Content-Type: application/json');
    
    echo json_encode([
        'content' => $content,
        'response' => $response
    ]);
?>
