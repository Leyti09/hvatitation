<header>
    <div class="navbar">
        <a href="/">
            <img class="logo" src="/assets/images/logo2.png" alt="hvatitation">
        </a>
		<div class="split"></div>
		<div class="search-container">
            <form action="/search" method="get">
                <input type="text" placeholder="Search.." name="query">
                <button type="submit"><img style="margin-top:3px;" src="/assets/images/search-13-64.png" width="15"></button>
            </form>
        </div>
		<div class="split"></div>
        <ul class="menu">
            <?php if(!isset($_SESSION['username'])): ?>
                <li><a href="/upload">Submit</a></li>
                <li><a href="/login">Login</a></li>
            <?php else: ?>
                <li><a href="/upload">Submit</a></li>
                <li><a href="/user/<?php echo htmlspecialchars($_SESSION['username']); ?>"><?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
            <?php endif; ?>
        </ul>
    </div>
</header>