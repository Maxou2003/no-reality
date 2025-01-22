<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réseau Social</title>
    <link rel="stylesheet" href="/no-reality/web/instagram/public/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="/no-reality/web/instagram/public/js/script.js" defer></script>
    <script src="/no-reality/web/instagram/public/js/heart_icons.js" defer></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="grid-container ">
        <div class="sidebar">
            <div class="logo">
                <i class="fab fa-instagram"></i>
                <span>Instagram</span>
            </div>

            <!-- Navigation Links -->
            <nav class="nav-links">
                <a href="#" class="nav-item">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-search"></i>
                    <span>Search</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-compass"></i>
                    <span>Explore</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-heart"></i>
                    <span>Notifications</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </nav>
        </div>

        <!-- Fil d'actualité -->
        <main class="feed">
            <h1>Fil d'actualité</h1>
            <div class="post">
                <div class="post_header">
                    <div class="profile_info">
                        <div class="profile_img">
                            <img src="img/pc160005.jpg" alt="Image">
                        </div>
                        <span>User</span>
                    </div>
                    <div class="options">
                        <span><ion-icon name="ellipsis-horizontal"></ion-icon></span>
                    </div>
                </div>
                <div class="post_img">
                    <img src="img/pc160005.jpg" alt="Image">
                </div>
                <div class="post_body">
                    <div class="post_actions">
                        <span class="heart_icon"><ion-icon name="heart-outline"></ion-icon></span>
                        <span><ion-icon name="chatbubble-outline"></ion-icon></span>
                        <span><ion-icon name="paper-plane-outline"></ion-icon></span>
                        <span><ion-icon name="bookmark-outline"></ion-icon></span>
                    </div>
                    <div class="post_info">1,8M likes</div>
                    <div class="post_title">
                        <span class="username">User</span>
                        <span class="title">Lovely face</span>
                    </div>
                    <div class="post_comments">
                        <span>View all 213K comments </span>
                        <div class="comment">
                            <span class="comment_username">User</span>
                            <span class="comment_text">Trop choupinou</span>
                            <span class="heart_icon"><ion-icon name="heart-outline"></ion-icon></span>
                        </div>
                    </div>
                    <div class="post_timestamp">2 hours ago</div>
                </div>
                <div class="input_box">
                    <div class="emoji"><ion-icon name="happy-outline"></ion-icon></div>
                    <input type="text" placeholder="Add a comment...">
                    <button>Post</button>
                </div>
            </div>

        </main>

        <!-- Suggestions -->
        <aside class="suggestions">
            <h2>Suggestions</h2>
            <div class="user">Utilisateur 1</div>
            <div class="user">Utilisateur 2</div>
            <div class="user">Utilisateur 3</div>
        </aside>
    </div>

</body>

</html>