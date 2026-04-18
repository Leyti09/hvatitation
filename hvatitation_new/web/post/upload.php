<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

	require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/initialize.php');

	if(!isset($_SESSION['username'])) {
		header('Location: /');
		die();
	}
	
	$stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
	$stmt->bind_param('s', $_SESSION['username']);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows == 0) {
	    echo 'Invalid user.';
	}

	if($_SERVER["REQUEST_METHOD"] == "POST"){
        if (empty($_FILES) || !isset($_FILES["file"])) {
            echo json_encode([
                "status" => "error",
                "message" => "Please submit a video!"
            ]);
            exit();
        }
        
        if ($_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
            $errorMessage = "Fatal error while uploading";
            switch ($_FILES["file"]["error"]) {
                case UPLOAD_ERR_PARTIAL:
                    $errorMessage = "Error: File uploaded partially";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errorMessage = "Error: No file was submitted";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errorMessage = "Error: Server is out of storage";
                    break;
            }
            echo json_encode([
                "status" => "error",
                "message" => $errorMessage
            ]);
            exit();
        }
        
        if ($_FILES["file"]["size"] > 100000000) { 
            echo json_encode([
                "status" => "error",
                "message" => "File too large! (Max: 100MB)"
            ]);
            exit();
        }
        
        $mime_types = [
            "image/jpeg",
            "image/png",
            "image/gif",
            "image/webp",
            "image/bmp",
            "image/svg+xml",
            "image/tiff"
        ];
        
        $extensions = [
            "jpg",
            "jpeg",
            "png",
            "gif",
            "webp",
            "bmp",
            "svg",
            "tiff"
        ];

        if (!in_array($_FILES['file']['type'], $mime_types)) {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid file type!"
            ]);
            exit();
        }
        
        $pathinfo = pathinfo($_FILES["file"]["name"]);
        $extension = strtolower($pathinfo["extension"]);
        
        if (!in_array($extension, $extensions)) {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid file type!"
            ]);
            exit();
        }
        
        $base = md5(microtime() . mt_rand());
        $filename = $base . "." . $extension;
        $destination = $_SERVER['DOCUMENT_ROOT'] . "/dynamic/art/" . $filename;
        
        if (!move_uploaded_file($_FILES["file"]["tmp_name"], $destination)) {
            echo json_encode([
                "status" => "error",
                "message" => "Can't upload file. Check permissions or available space."
            ]);
            exit();
        }
		
		$username = $_SESSION['username'];
		$ip = $_SERVER['REMOTE_ADDR'];
		
		if($_user->canPost($ip, $conn) == false) {
		    echo('Please wait before posting again!<br><a href="/">Go back</a>');
		    die();
		}
		
		$title = $_POST['title'];
		$description = $_POST['description'];
		$artist = $_SESSION['username'];
		$category = $_POST['category'];
		$file = '/dynamic/art/' . $filename;
		
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		$length = 6;

		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[random_int(0, $charactersLength - 1)];
		}
		
		$uid = $randomString;
		
		$stmt = $conn->prepare('INSERT INTO posts (title, description, artist, category, file, uid) VALUES (?, ?, ?, ?, ?, ?)');
		$stmt->bind_param('ssssss', $title, $description, $artist, $category, $file, $uid);
		$stmt->execute();
		$stmt->close();
		
		header('Location: /');
		die();
	}
?>
