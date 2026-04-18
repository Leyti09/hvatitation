<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/initialize.php');
    
    $user = $_GET['username'];
    
    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->bind_param('s', $user);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    if($result->num_rows === 0) {
        header('Location: /');
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
        <div class="user">
            <div class="banner">
                <img class="pfp" src="<?php echo $row['pfp']; ?>" width="190" height="190">
                <h2><?php echo htmlspecialchars($row['username']); ?></h2>
            </div>
        </div>
        <div class="stuff">
            <h2 style="margin-left:12px;font-size:30px;margin-top:0;margin-bottom:0;">Recent Artwork</h2>
            <div class="filter-tabs">
                <button class="tab active">Newest</button>
                <button class="tab">Trending</button>
                <button class="tab">Popular</button>
            </div>
            <div id="gallery-content" class="grid-container">
                <script>
                    async function loadTabContent(type, tab) {
                        const gallery = document.getElementById('gallery-content');
                        gallery.innerHTML = '<div class="spinner">Loading...</div>';
                        
                        try {
                            const response = await fetch(`/api/fetch.php?type=${type}&feedback=html&artist=<?php echo htmlspecialchars($user); ?>`);
                            const data = await response.json();
                            
                            if (response.status === 200) {
                                gallery.innerHTML = data.content;
                            } else {
                                gallery.innerHTML = `
                                    <div class="error-message">
                                        Sorry! We had an error while loading this content. Contact our support team and give them this error code: ${response.status}
                                    </div>`;
                            }
                        } catch (error) {
                            gallery.innerHTML = `
                                <div class="error-message">
                                    Sorry! We had an error while loading this content. Contact our support team and give them this error code: 500
                                </div>`;
                        }
                    }
                    
                    document.querySelectorAll('.tab').forEach(tab => {
                        tab.addEventListener('click', () => {
                            document.querySelector('.tab.active').classList.remove('active');
                            tab.classList.add('active');
                            const type = tab.textContent.toLowerCase();
                            loadTabContent(type, tab);
                        });
                    });
                    
                    window.addEventListener('DOMContentLoaded', () => {
                        const trendingTab = document.querySelector('.tab.active');
                        if (trendingTab) {
                            const type = trendingTab.textContent.toLowerCase();
                            loadTabContent(type, trendingTab);
                        }
                    });
                </script>
            </div>
            
        </div>
        <div class="info">
            <h2>
                About <?php echo htmlspecialchars($row['username']); ?>
                <?php if(isset($_SESSION['username']) && $_SESSION['username'] !== $row['username']): ?>
                    <button id="followButton" class="follow" style="float:right;margin-right:7px;">Follow</button>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const followButton = document.getElementById("followButton");
                        
                            if (followButton) {
                                fetch(`/api/check_follow.php?user=<?php echo htmlspecialchars($row['username']); ?>`)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === "not_subscribed") {
                                            followButton.textContent = "Follow";
                                        } else if (data.status === "subscribed") {
                                            followButton.textContent = "Following";
                                        }
                                    })
                                    .catch(err => console.error("Error checking follow status:", err));
                        
                                followButton.addEventListener("click", function() {
                                    followButton.disabled = true;
                        
                                    fetch(`/post/follow.php?user=<?php echo htmlspecialchars($row['username']); ?>`)
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.status === "login") {
                                                window.location.href = "/login";
                                            } else if (data.status === "Follow") {
                                                followButton.textContent = "Follow";
                                            } else if (data.status === "Following") {
                                                followButton.textContent = "Following";
                                            }
                                        })
                                        .catch(err => console.error("Error processing follow request:", err))
                                        .finally(() => {
                                            followButton.disabled = false;
                                        });
                                });
                            }
                        });
                    </script>
                <?php endif; ?>
            </h2>
            <p><?php echo nl2br(htmlspecialchars($row['bio'])); ?></p>
            <em>Joined <?php echo $_time->timeAgo(htmlspecialchars($row['join_date'])); ?></em><br>
            <em><?php echo number_format($row['followers']); ?> followers</em>
            <?php if(isset($_SESSION['username']) && $_SESSION['username'] === $row['username']): ?>
                <hr>
                <h2>Edit Profile</h2>
                <form enctype="multipart/form-data" method="post" action="/post/profile.php">
                    <label>Profile Picture:</label>
                    <input name="pfp" type="file">
                    <label>Banner:</label>
                    <input name="banner" type="file">
                    <label>Biography:</label>
                    <textarea name="bio" maxlength="1000"><?php echo htmlspecialchars($row['bio']); ?></textarea>
                    <input style="margin-left:8px;margin-top:8px;margin-bottom:6px;" type="submit" class="follow" value="Apply Changes!">
                </form>
                <hr>
                <a style="margin-left:8px;" href="/post/logout.php"><button class="follow">Log Out</button></a>
            <?php endif; ?>
        </div>
    </div>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/static/module/footer.php'); ?>
    <style>
        textarea {
            background: linear-gradient(0deg, var(--wtr1), var(--wtr3));
            box-shadow: inset 0 0 0 1px var(--wtr2), 0 1px 2px var(--btr5);
            padding: 8px;
            margin-left: 8px;
            border-radius: 3px;
            color: var(--white);
            transition: border .2s, box-shadow .2s;
            border: none;
        }
        label {
            margin-left: 8px;
        }
        input {
            margin-left: 8px;
        }
        hr {
            border: none;
            height: 1px;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .follow {
            background: linear-gradient(0deg, var(--coolblack), var(--cornflower));
            box-shadow: inset 0 0 0 1px var(--wtr2), 0 1px 2px var(--btr5);
            padding: 3px 10px;
            border-radius: 3px;
            color: #ffffff;
            cursor: pointer;
            border: none;
            margin-left: 1px;
            text-shadow: 0 1px var(--black);
        }
        .follow:hover {
            background: linear-gradient(0deg, var(--coolblack), var(--cornflower) 0%);
            box-shadow: inset 0 0 0 1px var(--wtr3), 0 1px 2px var(--btr5);
            border: none;

        }
        .container {
            display: block;
        }
        .stuff {
            margin-left: 24px;
            margin-right: 24px;
            width: 80%;
            float: left;
        }
        .info {
            float: right;
            width: 15%;
            margin-right: 24px;
            height: 100%;
            background: linear-gradient(340deg, var(--darkgunmetal), var(--richblack));
            box-shadow: inset 0 0 0 1px var(--wtr2), 0 1px 3px var(--btr6);
            border-radius: 3px;
            margin-top: -65px;
            padding-bottom: 6px;
        }
        #gallery-content {
            margin: 12px;
        }
        .user {
            width: 98.75%;
            margin-left: 12px;
            border-radius: 3px;
            height: 400px;
        }
        .info h2 {
            font-size: 16px;
            margin-top: 8px;
            margin-left: 8px;
            margin-bottom: 3px;
        }
        .info p {
            margin-left: 8px;
            margin-top: 0px;
        }
        .info em {
            color: gray;
            margin-left: 8px;
        }
        .banner h2 {
            font-size: 50px;
            margin-left: 265px;
            margin-top: -150px;
            text-shadow: black 1px 0 10px;
        }
        .pfp {
            margin-top: 180px;
            margin-left: 40px;
            border: solid 6px black;
            border-radius: 24px;
        }
        .banner {
            background-image: url(<?php echo htmlspecialchars($row['banner']); ?>);
            width: 100%;
            height: 320px;
            border-radius: 3px;
            background-size: cover;
            background-position: center;
        }
        .filter-tabs {
            margin-left: 12px;
        }
    </style>
</body>
</html>



