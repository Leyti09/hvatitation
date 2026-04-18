<?php
    class _posts {
        public function fetchPosts($category, $type, $artist, $search, $conn) {
            $validTypes = ['newest', 'popular', 'trending'];
            $type = in_array($type, $validTypes) ? $type : 'newest';
    
            $sql = 'SELECT * FROM posts ';
            $params = [];
            $types = '';
    
            if (!is_null($category)) {
                $sql .= 'WHERE category = ? ';
                $params[] = $category;
                $types .= 's';
            }
    
            if (!is_null($artist)) {
                $sql .= (strpos($sql, 'WHERE') === false ? 'WHERE ' : 'AND ') . 'artist = ? ';
                $params[] = $artist;
                $types .= 's';
            }
    
            if (!is_null($search)) {
                $sql .= (strpos($sql, 'WHERE') === false ? 'WHERE ' : 'AND ') . '(title LIKE ? OR description LIKE ?) ';
                $searchTerm = '%' . $search . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= 'ss';
            }
    
            if ($type === 'newest') {
                $sql .= 'ORDER BY upload_date DESC';
            } elseif ($type === 'popular') {
                $sql .= 'ORDER BY views DESC';
            } elseif ($type === 'trending') {
                $sql .= (strpos($sql, 'WHERE') === false ? 'WHERE ' : 'AND ') . 'upload_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY views DESC';
            }
    
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception('Failed to prepare statement: ' . $conn->error);
            }
    
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
    
            $stmt->execute();
            $result = $stmt->get_result();
    
            return $result;
            return $sql;
        }
        
        public function view($ip, $uid, $conn) {
            $stmt = $conn->prepare('SELECT * FROM views WHERE ip = ? AND uid = ? AND date >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)');
            $stmt->bind_param('ss', $ip, $uid);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            
            if(!$result->num_rows > 0) {
                $stmt = $conn->prepare('INSERT INTO views (ip, uid) VALUES (?, ?)');
                $stmt->bind_param('ss', $ip, $uid);
                $stmt->execute();
                $stmt->close();
                
                $stmt = $conn->prepare('UPDATE posts SET views = views + 1 WHERE uid = ?');
                $stmt->bind_param('s', $uid);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        public function canComment($username, $conn) {
            $stmt = $conn->prepare('SELECT MAX(post_date) AS last_comment FROM comments WHERE user = ?');
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $lastComment = strtotime($row['last_comment']);
                if ($lastComment && (time() - $lastComment) < 300) {
                    return false;
                }
            }
            
            return true;
        }

    }
?>
