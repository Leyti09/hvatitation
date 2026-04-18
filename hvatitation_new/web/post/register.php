<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/initialize.php');
	
	

	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$pattern = "/^[a-zA-Z0-9]+$/";
		
		if (!preg_match($pattern, $_POST['username'])) {
			echo('Username can only contain letters or numbers!<br><a href="/register.php">Go back</a>');
			die();
		}
		
		$username = $_POST['username'];
		$password = $_POST['password'];
		$recaptcha = $_POST['g-recaptcha-response'];
		
		$url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $_server->recaptcha_private . '&response=' . $recaptcha;
          
        $response = file_get_contents($url);
        
        $response = json_decode($response);
        
        if ($response->success == true) {
            if(empty($username)){
    			header("Location: /register");
    			die();
    		}elseif(empty($password)){
    			header("Location: /register");
    			die();
    		}else {
    			$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    			$stmt->bind_param("s", $username);
    			$stmt->execute();
    			$stmt->bind_result($count);
    			$stmt->fetch();
    			$stmt->close();
    			
    			if ($count > 0) {
    				echo('Username already in use.<br><a href="/register">Go back</a>');
    				die();
    			}
    			
    			$hash = password_hash($password, PASSWORD_DEFAULT);
    					
    			$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    			$stmt->bind_param("ss", $username, $hash);
    			
    			try {
    				$stmt->execute();
    				$stmt->close();
    				$_SESSION['username'] = $username;
                                    
                    header('Location: /');
                    die();
    			}
    			catch(mysqli_sql_exception $e) {
    				echo("Error");
    				die();
    			}
    		}	
        } else {
            echo 'Please complete the captcha.<br><a href="/register">Go back</a>';
        }
	}
?>
