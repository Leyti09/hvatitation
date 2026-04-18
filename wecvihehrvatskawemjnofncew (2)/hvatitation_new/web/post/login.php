<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/initialize.php');
	
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
		$password = $_POST['password'];

		if(empty($username)){
			header("Location: /login.php");
			die();
		}elseif(empty($password)){
			header("Location: /login.php");
			die();
		}else {
			$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				$realPassword = $row['password'];

				if (password_verify($password, $realPassword)) {
					$_SESSION['username'] = htmlspecialchars($username);
					header("Location: /");
				} else {
					echo('Incorrect password.<br><a href="/register.php">Go back</a>');
					die();
				}
			} else {
				echo('Incorrect username.<br><a href="/register.php">Go back</a>');
				die();
			}

			$stmt->close();
		}
	}
?>
