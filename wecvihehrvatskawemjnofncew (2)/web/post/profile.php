<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/initialize.php');
    
    if (empty($_SESSION['username'])) {
        header('Location: /');
        die();
    }
    
    $uploader = htmlspecialchars($_SESSION['username']);
    $bio = $_POST['bio'];

    $pfp_default = 0;
    if ($_FILES["pfp"]["error"] == UPLOAD_ERR_NO_FILE) {
        $pfp_default = 1;
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $uploader);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $pfp = $row['pfp'];
    }
    
    if ($_FILES["pfp"]["size"] > 10000000) {
        echo 'file too large';
        die();
    }
    
    $banner_default = 0;
    if ($_FILES["banner"]["error"] == UPLOAD_ERR_NO_FILE) {
        $banner_default = 1;
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $uploader);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $banner = $row['banner'];
    }
    
    if ($_FILES["banner"]["size"] > 10000000) {
        echo 'file too large';
        die();
    }
    
    $mime_types = ["image/png", "image/jpg", "image/jpeg", "image/gif"];
    $extensions = ["png", "jpg", "jpeg", "gif"];
    $base = md5(microtime() . mt_rand());
    
    if ($pfp_default == 0) {
        $pathinfo_pfp = pathinfo($_FILES["pfp"]["name"]);
        $extension_pfp = $pathinfo_pfp["extension"];
        if (!in_array($extension_pfp, $extensions)) {
            echo 'invalid file type';
            die();
        }
        $filename_pfp = $base . "." . $extension_pfp;
        $destination_pfp = $_SERVER['DOCUMENT_ROOT'] . "/dynamic/pfp/" . $filename_pfp;
        if (!move_uploaded_file($_FILES["pfp"]["tmp_name"], $destination_pfp)) {
            echo "uh oh";
        }
        $pfp_path = "/dynamic/pfp/" . $filename_pfp;
    } else {
        $pfp_path = $pfp;
    }
    
    if ($banner_default == 0) {
        $pathinfo_banner = pathinfo($_FILES["banner"]["name"]);
        $extension_banner = $pathinfo_banner["extension"];
        if (!in_array($extension_banner, $extensions)) {
            echo 'invalid file type';
            die();
        }
        $filename_banner = $base . "." . $extension_banner;
        $destination_banner = $_SERVER['DOCUMENT_ROOT'] . "/dynamic/banner/" . $filename_banner;
        if (!move_uploaded_file($_FILES["banner"]["tmp_name"], $destination_banner)) {
            echo "uh oh";
        }
        $banner_path = "/dynamic/banner/" . $filename_banner;
    } else {
        $banner_path = $banner;
    }
    
    $bio_default = empty($_POST['bio']) ? 1 : 0;
    if ($bio_default == 0) {
        $bio = htmlspecialchars($_POST['bio']);
    }
    
    $stmt = $conn->prepare("UPDATE users SET bio = ?, banner = ?, pfp = ? WHERE username = ?");
    $stmt->bind_param("ssss", $bio, $banner_path, $pfp_path, $uploader);
    $stmt->execute();
    $stmt->close();
    
    header('Location: /user/' . $uploader);
?>
