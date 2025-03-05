
document.addEventListener("DOMContentLoaded", function () {
    let loading = false;
    let page = 1;
    const nb_home_post = 10;

    async function loadMorePosts() {
        if (loading) return;
        loading = true;


        try {
            const response = await fetch(`${API_BASE_URL}getMorePosts&page=${page}&nbPosts=${nb_home_post}`);
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            const newPosts = await response.json();

            if (newPosts.length > 0) {
                newPosts.forEach(post => {
                    document.querySelector(".feed").insertAdjacentHTML("beforeend", `
                        <div class="post" user-id="${post.user_id}">
                            <div class="post_header">
                                <div class="profile_info">
                                    <div class="profile_img">
                                        <a href="${MY_URL}profile/${post.username}"><img src="${PROFILE_IMG_PATH}${post.user_pp_path}" alt="Image"> </a>
                                    </div>
                                    <a class="nav-link" href="${MY_URL}profile/${post.username}">
                                        <span>${post.username}</span>
                                    </a>
                                </div>
                                <div class="options">
                                    <span>
                                        <ion-icon name="ellipsis-horizontal"></ion-icon>
                                    </span>
                                </div>
                            </div>
                            <div class="post_img">
                                <img src="${POST_IMG_PATH}${post.post_picture_path}" onclick="openModalPost('${post.post_id}')">
                            </div>
                            <div class="post_body">
                                <div class="post_actions">
                                    <span class="heart_icon"><ion-icon name="heart-outline"></ion-icon></span>
                                    <span><ion-icon name="chatbubble-outline" onclick="openModalPost('${post.post_id}')"><img src="${POST_IMG_PATH}${post.post_picture_path}"></ion-icon></span>
                                    <span><ion-icon name="paper-plane-outline"></ion-icon></span>
                                    <span><ion-icon name="bookmark-outline"></ion-icon></span>
                                </div>
                                <div class="post_info">${post.nb_likes} likes</div>
                                <div class="post_title">
                                    <a class="nav-link"  href="${MY_URL}profile/${post.username}"><span class="username">${post.username}</span></a>
                                    <span class="title">${post.post_description}</span>
                                </div>
                                <div class="post_comments">
                                    <span onclick="openModalPost('${post.post_id}')">View all ${post.nb_comments} comments</span>
                                </div>
                                <div class="post_timestamp">${post.time_stamp}</div>
                            </div>
                            <div class="input_box">
                                <div class="emoji">
                                    <ion-icon name="happy-outline"></ion-icon>
                                </div>
                                <input type="text" placeholder="Add a comment...">
                                <button>Post</button>
                            </div>
                        </div>
                    `);
                });

                page++;
            } else {
                window.removeEventListener("scroll", handleScroll);
            }
        } catch (error) {
            console.error("Error loading posts :", error);
        } finally {
            loading = false;
        }
    }

    function handleScroll() {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 10) {
            loadMorePosts();
        }
    }

    function checkAndLoad() {
        if (document.documentElement.scrollHeight <= window.innerHeight) {
            loadMorePosts();
            setTimeout(checkAndLoad, 500);
        }
    }

    loadMorePosts();

    checkAndLoad();

    window.addEventListener("scroll", handleScroll);

});
