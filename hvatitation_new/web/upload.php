<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/initialize.php');
        
    if(!isset($_SESSION['username'])) {
        header('Location: /login');
        die();
    }
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/static/module/head.php'); ?>
</head>
<body>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/static/module/header.php'); ?>
    <div class="container">
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/static/module/side.php'); ?>
        
        <section class="form-section" style="float:left;margin-left:12px;margin-right:auto;">
            <h2>Submit</h2>
            <form action="/post/upload.php" method="post" enctype="multipart/form-data">
                <label for="upload">Upload your art:</label>
                <input type="file" name="file" id="fileUpload" accept="image/*">
                
                <div id="imagePreview" style="margin-top: 10px;"></div>
                
                <label for="text">Name your art:</label>
                <input maxlength="100" type="text" id="title" name="title" required>
                <label for="text">Category:</label>
                <select class="select" id="category" name="category" required>
                    <option name="Digital Art">Digital Art</option>
                    <option name="Traditional Art">Traditional Art</option>
                    <option name="Photography">Photography</option>
                    <option name="Artisan Crafts">Artisan Crafts</option>
                    <option name="Literature">Literature</option>
                    <option name="Comics and Cartoons">Comics and Cartoons</option>
                    <option name="Customizations">Customizations</option>
                </select>
                <label for="text">Describe your art:</label>
                <textarea class="textarea" type="text" id="description" name="description" maxlength="1000" required></textarea>
                <div class="form-footer">
                    <button type="submit">Publish</button>
                    <p class="account-text">Not sure what's allowed? <a href="/t/rules.php">Read our guidelines!</a></p>
                </div>
            </form>
        </section>
    </div>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/static/module/footer.php'); ?>
    
    <style>
        .textarea, .select {
            background: linear-gradient(0deg, var(--wtr1), var(--wtr3));
            box-shadow: inset 0 0 0 1px var(--wtr2), 0 1px 2px var(--btr5);
            width: 385px;
            padding: 8px;
            margin-bottom: 20px;
            border-radius: 3px;
            color: var(--white);
            transition: border .2s, box-shadow .2s;
            border: none;
        }

        #imagePreview img {
            max-width: 100%;
            max-height: 300px;
            margin-top: 10px;
        }
    </style>

    <script>
        document.getElementById('fileUpload').addEventListener('change', function(event) {
            var file = event.target.files[0];
            var reader = new FileReader();
            
            reader.onload = function(e) {
                var imgElement = document.createElement('img');
                imgElement.src = e.target.result;
                var previewDiv = document.getElementById('imagePreview');
                previewDiv.innerHTML = ''; 
                previewDiv.appendChild(imgElement); 
            };
            
            if (file) {
                reader.readAsDataURL(file); 
            }
        });
    </script>
</body>
</html>
