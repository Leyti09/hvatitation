<?php
    class _user {
        public function fetchPfp($username, $conn) {
            $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            $pfp = $row['pfp'];
            
            return $pfp;
        }
        
        public function canPost($ip, $conn) {
            $stmt = $conn->prepare('SELECT username FROM users WHERE ip = ?');
            $stmt->bind_param('s', $ip);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        
            if (!$row) {
                return true; 
            }
        
            $username = $row['username'];
        
            $stmt = $conn->prepare('SELECT COUNT(*) AS post_count FROM posts WHERE artist = ? AND upload_date >= NOW() - INTERVAL 5 MINUTE');
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        
            if ($row['post_count'] >= 5) {
                return false; 
            }
        
            return true;
        }
        
        public function setIp($username, $ip, $conn) {
            $stmt = $conn->prepare('UPDATE users SET ip = ? WHERE username = ?');
            $stmt->bind_param('ss', $ip, $username);
            $stmt->execute();
        }
    }
?>