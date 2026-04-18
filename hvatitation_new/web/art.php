<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/initialize.php');
    
    $uid = $_GET['uid'];
    
    $_posts->view($_SERVER['REMOTE_ADDR'], $uid, $conn);
    
    $stmt = $conn->prepare('SELECT * FROM posts WHERE uid = ? LIMIT 1');
    $stmt->bind_param('s', $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if($result->num_rows === 0) {
        header('Location: /');
        die();
    }
    
    $pfp = $_user->fetchPfp($row['artist'], $conn);
    
    $stmt_comments = $conn->prepare('SELECT * FROM comments WHERE post = ? ORDER BY post_date DESC');
    $stmt_comments->bind_param('s', $uid);
    $stmt_comments->execute();
    $result_comments = $stmt_comments->get_result();
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/static/module/head.php'); ?>
    <style>
        
    </style>
</head>
<body>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/static/module/header.php'); ?>
    <div class="container">
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/static/module/side.php'); ?>
        <div class="art">
            <img width="800" style="max-width:none;" id="art" src="<?php echo $row['file']; ?>">
            <div class="userinfo">
                <img style="float:left;margin-right:8px;margin-top:0;border-radius:3px;" width="70" height="70" src="<?php echo $pfp; ?>">
                <div style="float:left;">
                    <h3 style="font-size:36px;margin:0;padding:0;"><?php echo htmlspecialchars($row['title']); ?></h3>
                    <h5 style="font-size:15px;margin:0;margin-top:-8px;">by <strong><a style="text-decoration:none;color:white;" href="/user/<?php echo htmlspecialchars($row['artist']); ?>"><?php echo htmlspecialchars($row['artist']); ?></a></strong> | <strong><?php echo $_time->timeAgo($row['upload_date']); ?></strong> | <strong><?php echo number_format($row['views']); ?> views</strong></h5>
                </div>
                <div style="float:right;margin:4px;">
                    <button class="ratingbutton">Like (???)</button>
                    <button class="ratingbutton">Dislike (???)</button>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                    const postId = "<?php echo $row['uid']; ?>";
                    const likeButton = document.querySelector(".ratingbutton:nth-child(1)");
                    const dislikeButton = document.querySelector(".ratingbutton:nth-child(2)");
                    
                    const updateButtons = (likes, dislikes, status) => {
                        likeButton.textContent = `Like (${likes})`;
                        dislikeButton.textContent = `Dislike (${dislikes})`;
                
                        if (status === "liked") {
                            likeButton.textContent = `Liked (${likes})`;
                            dislikeButton.textContent = `Dislike (${dislikes})`;
                        } else if (status === "disliked") {
                            likeButton.textContent = `Like (${likes})`;
                            dislikeButton.textContent = `Disliked (${dislikes})`;
                        }
                    };
                
                    const disableButtons = (disable) => {
                        likeButton.disabled = disable;
                        dislikeButton.disabled = disable;
                    };
                
                    const fetchRatings = async () => {
                        try {
                            const response = await fetch(`/api/check_ratings.php?post=${postId}`);
                            const data = await response.json();
                            updateButtons(data.likes, data.dislikes, data.status);
                        } catch (error) {
                            console.error("Error fetching ratings:", error);
                        }
                    };
                
                    const handleButtonClick = async (type) => {
                        const url = `/post/${type}.php?v=${postId}`;
                        disableButtons(true);
                        try {
                            const response = await fetch(url, { method: "POST" });
                            const data = await response.json();
                            if (data.status === "login") {
                                window.location.href = "/login";
                            } else {
                                fetchRatings();
                            }
                        } catch (error) {
                            console.error(`Error handling ${type} request:`, error);
                        } finally {
                            disableButtons(false);
                        }
                    };
                
                    likeButton.addEventListener("click", () => {
                        if (likeButton.textContent.startsWith("Liked")) {
                            handleButtonClick("like");
                        } else {
                            handleButtonClick("like");
                        }
                    });
                
                    dislikeButton.addEventListener("click", () => {
                        if (dislikeButton.textContent.startsWith("Disliked")) {
                            handleButtonClick("dislike");
                        } else {
                            handleButtonClick("dislike");
                        }
                    });
                
                    fetchRatings();
                });
                </script>
            </div>
            <div style="margin-top:80px;">
                <p style="margin:0;"><?php echo htmlspecialchars($row['description']); ?></p>
            </div>
            <form method="post" action="/post/comment.php">
                <input type="text" id="comment" name="comment" placeholder="Comment here" style="margin-top: 5px;margin-bottom:8px;">
                <input type="hidden" name="uid" value="<?php echo $uid; ?>">
                <input type="submit" style="width:100px;margin-top:0;" value="Comment">
            </form>
            <div class="comments">
                <?php if($result_comments->num_rows > 0): ?>
                    <?php while($row_comments = $result_comments->fetch_assoc()): ?>
                        <div class="comment">
                            <img width="50" height="50" src="<?php echo $_user->fetchPfp($row_comments['user'], $conn); ?>"> 
                            <div style="margin-left:8px;">
                                <p style="font-size:12px;margin-top:0;margin-bottom:0;"><?php echo htmlspecialchars($row_comments['user']); ?> | <?php echo $_time->timeAgo($row_comments['post_date']); ?></p>
                                <p style="font-size:16px;margin-top:0;"><?php echo nl2br(htmlspecialchars($row_comments['content'])); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
            <style>
                .comment {
                    display: flex;
                }
                .art {
                    max-width: 50% !important;
                }
            </style>
        </div>
        <div class="browse">
            <div class="filter-tabs">
                <button class="tab">Newest</button>
                <button class="tab active">Trending</button>
                <button class="tab">Popular</button>
            </div>
            <div id="gallery-content" class="grid-container">
                        <script>
            async function loadTabContent(type, tab) {
                const gallery = document.getElementById('gallery-content');
                gallery.innerHTML = '<div class="spinner">Loading...</div>';
                
                try {
                    const response = await fetch(`/api/fetch.php?type=${type}&feedback=html`);
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
    </div>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/static/module/footer.php'); ?>
    <style>
    .browse {
        width: 39%;
        margin-left: auto;
    }
   .grid-container {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
   }
        .ratingbutton {
              background: linear-gradient(0deg, var(--coolblack), var(--cornflower));
              box-shadow: inset 0 0 0 1px var(--wtr2), 0 1px 2px var(--btr5);
              padding: 5px 10px;
              border-radius: 3px;
              color: #ffffff;
              cursor: pointer;
              border: none;
              margin-left: 1px;
              text-shadow: 0 1px var(--black);
            }
        
        hr {
            border: 0;
            background-color: #262626;
            height: 1px;
        }
        .art {
            margin-top: 0;
            padding-top:0;
        }
        .userinfo {
            
        }
        h5 {
            color: gray;
            font-weight: 300;
        }
        h5 strong {
            color: white;
        }
        .container input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 20px;
            border: 2px solid #1C3F4D;
            border-radius: 5px;
            background-color: #000000;
            color: #ffffff;
            transition: border 0.3s, box-shadow 0.3s;
        }
        
        .container input:focus {
            border: 2px solid #00B5B5;
            box-shadow: 0 0 10px rgba(0, 181, 181, 0.5);
            outline: none;
        }
        
        .art {
            max-width: 40%;
        }
    </style>
</body>
</html>