<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram</title>
    <link rel="stylesheet" href="/no-reality/web/instagram/public/css/sidebar.css">
    <link rel="stylesheet" href="/no-reality/web/instagram/public/css/profile.css">
    <link rel="stylesheet" href="/no-reality/web/instagram/public/css/custom-modal.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="/no-reality/web/instagram/public/img/favicon.ico" type="image/x-icon">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script src="/no-reality/web/instagram/public/js/post_view.js" defer></script>
    <script src="/no-reality/web/instagram/public/js/heart_icons.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>

    <div class="grid-container ">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <a href="index.html" class="navbar-brand">
                    <i class="fab fa-instagram"></i>
                    <span>Instagram</span>
                </a>
            </div>
            <nav class="nav-links">
                <a href="index.html" class="nav-item">
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
        <!-- Profile header -->
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-picture">
                    <img src="/no-reality/web/instagram/public/img/profile_picture/pc160005.jpg " alt="Profile Picture">
                </div>
                <div class="profile-info">
                    <div class="profile-username">
                        <h2>LeMaxou</h2>
                        <button class="follow-btn">Follow</button>
                    </div>
                    <ul class="profile-stats">
                        <li><a href="#" class="nav-link"><strong>150</strong> posts</li></a>
                        <li><a href="#" class="nav-link"><strong>2.5k</strong> followers</li></a>
                        <li><a href="#" class="nav-link"><strong>300</strong> following</li></a>
                    </ul>
                    <div class="profile-bio">
                        <p>Maxence Martin</p>
                        <p>Ma bio est trop cool 🌟</p>
                        <p>📍 Angers, France</p>
                    </div>
                </div>
            </div>
            <!-- Post gallery-->
            <div class="profile-posts">
                <div class="post" onclick="openModal('/no-reality/web/instagram/public/img/post_img/pexels-kaboompics-6256.jpg')"><img
                        src="/no-reality/web/instagram/public/img/post_img/pexels-kaboompics-6256.jpg" alt="Post"></div>
                <div class="post" onclick="openModal('/no-reality/web/instagram/public/img/post_img/pexels-mikebirdy-211762.jpg')"><img
                        src="/no-reality/web/instagram/public/img/post_img/pexels-mikebirdy-211762.jpg" alt="Post"></div>
                <div class="post" onclick="openModal('/no-reality/web/instagram/public/img/post_img/pexels-muratak-30326892.jpg')"><img
                        src="/no-reality/web/instagram/public/img/post_img/pexels-muratak-30326892.jpg" alt="Post"></div>
                <div class="post" onclick="openModal('/no-reality/web/instagram/public/img/post_img/pexels-ps-photography-14694-67184.jpg')"><img
                        src="/no-reality/web/instagram/public/img/post_img/pexels-ps-photography-14694-67184.jpg" alt="Post"></div>
                <div class="post" onclick="openModal('/no-reality/web/instagram/public/img/post_img/pexels-steve-861414.jpg')"><img
                        src="/no-reality/web/instagram/public/img/post_img/pexels-steve-861414.jpg" alt="Post"></div>
                <div class="post" onclick="openModal('/no-reality/web/instagram/public/img/post_img/pexels-gabby-k-6621244.jpg')"><img
                        src="/no-reality/web/instagram/public/img/post_img/pexels-gabby-k-6621244.jpg" alt="Post"></div>
            </div>

        </div>
    </div>
    <!-- Selected post -->
    <div id="post-modal" class="custom-modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="custom-modal-content">
            <div class="custom-modal-left">
                <img id="modal-image" src="" alt="Post">
            </div>
            <div class="custom-modal-right">
                <div class="custom-modal-post-header">
                    <div class="custom-modal-profile-info">
                        <div class="custom-modal-profile-img">
                            <img src="/no-reality/web/instagram/public/img/profile_picture/pc160005.jpg" alt="Image">
                        </div>
                        <a class="nav-link" href="profile.html"><span>User</span></a>
                    </div>
                    <div class="options">
                        <span><ion-icon name="ellipsis-horizontal"></ion-icon></span>
                    </div>
                </div>

                <div class="comments">
                    <div class="title">
                        <div class="custom-modal-profile-img">
                            <img src="/no-reality/web/instagram/public/img/profile_picture/pc160005.jpg" alt="Image">
                        </div>
                        <div class="description-content">
                            <span class="username">User</span>
                            <span class="description">My favorite</span>
                            <div class="timestamp">
                                <span>2 days ago</span>
                            </div>
                        </div>
                    </div>

                    <div class="comment">
                        <div class="custom-modal-comment-profile-img">
                            <img src="/no-reality/web/instagram/public/img/profile_picture/maxime_lambert.jpg" alt="Image">
                        </div>
                        <div class="comment-content">
                            <span class="username">MaxLambert</span>
                            <span class="comment-text">Super la photo !</span>
                            <div class="timestamp">
                                <span>2 hours ago</span>
                            </div>
                            <button class="show-response-btn">View all 12 responses</button>
                        </div>

                    </div>

                    <div class="comment">
                        <div class="custom-modal-comment-profile-img">
                            <img src="/no-reality/web/instagram/public/img/profile_picture/234363700-portrait-de-tête-de-renard-sur-fond-blanc-retouché-avec-un-effet-réaliste.jpg"
                                alt="Image">
                        </div>
                        <div class="comment-content">
                            <span class="username">DirtyRabbit</span>
                            <span class="comment-content">Magnifique 😍</span>
                            <div class="timestamp">
                                <span>6 hours ago</span>
                            </div>
                            <button class="show-response-btn">View all 12 responses</button>
                        </div>

                    </div>
                </div>
                <div class="custom-modal-post-info">
                    <div class="custom-modal-actions">
                        <span class="heart_icon"><ion-icon name="heart-outline"></ion-icon></span>
                        <span><ion-icon name="chatbubble-outline"></ion-icon></span>
                        <span><ion-icon name="paper-plane-outline"></ion-icon></span>
                        <span><ion-icon name="bookmark-outline"></ion-icon></span>
                    </div>
                    <div class="custom-modal-stats">
                        <span>1,8M likes</span>
                        <span>2 days ago</span>
                    </div>
                </div>
                <div class="add-comment">
                    <div class="emoji"><ion-icon name="happy-outline"></ion-icon></div>
                    <input type="text" placeholder="Ajouter un commentaire...">
                    <button>Publier</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>