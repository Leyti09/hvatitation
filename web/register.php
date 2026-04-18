<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/initialize.php');
    
    if(isset($_SESSION['username'])) {
        header('Location: /');
        die();
    }
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/static/module/head.php'); ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/static/module/header.php'); ?>
    <div class="container">
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/static/module/side.php'); ?>
        
        <section class="form-section" style="float:left;margin-left:12px;margin-right:auto;">
            <h2>Register</h2>
            <form action="/post/register.php" method="post">
                <label for="username">Username:</label>
                <input maxlength="20" type="text" id="username" name="username" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                
                <div class="g-recaptcha" data-sitekey="<?php echo $_server->recaptcha_public; ?>"></div>
                
                <div class="form-footer">
                    <button type="submit">Register</button>
					<p class="account-text">Have an account? <a href="/login">Log In</a></p>
                </div>
            </form>
        </section>
    </div>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/static/module/footer.php'); ?>
</body>
</html>