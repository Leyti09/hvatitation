<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/static/classes/initialize.php');
    
    $query = $_GET['query'];
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
        <div>
            <h2 style="margin-left:12px;margin-top:0;">Results for '<?php echo htmlspecialchars($query); ?>'</h2>
            <section class="gallery">
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
                                const response = await fetch(`/api/fetch.php?type=${type}&feedback=html&search=<?php echo htmlspecialchars($query); ?>`);
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
            </section>
        </div>
    </div>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/static/module/footer.php'); ?>
</body>
</html>
