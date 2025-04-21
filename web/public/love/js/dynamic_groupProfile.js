let noPostsShown = false;
document.addEventListener("DOMContentLoaded", function () {
    var splitted_location = window.location.href.split("/").filter((val) => val != "");
    var group_slug = splitted_location[splitted_location.length - 3];
    var user_slug = splitted_location[splitted_location.length - 1];    
    console.log("group_slug : ", group_slug); 
    console.log("user_slug : ", user_slug);

    let loading = false;
    let page = 1;
    const nb_home_post = 10;

    async function loadMorePosts() {
        if (loading) return;
        loading = true;

        try {           
            const response = await fetch(`${API_BASE_URL}getDynamicUserGroupPosts&page=${page}&nbPosts=${nb_home_post}&groupSlug=${group_slug}&userSlug=${user_slug}`);
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            const newPosts = await response.json();

            if (newPosts.posts.length > 0) {
                newPosts.posts.forEach(post => {
                    document.querySelector(".post_col").insertAdjacentHTML("beforeend", renderPost(group_slug, post));
                });                

                page++;
            } else if (page === 1 && !noPostsShown) {
                document.querySelector(".post_col").insertAdjacentHTML("beforeend", 
                    `
                    <div class="no-posts-container">
                        <span class="no-new-posts">No new posts</span>
                        <span class="not-posted-yet">${newPosts.user?.user_firstname || "This user"} ${newPosts.user?.user_lastname || ""} hasn't posted anything in ${newPosts.group?.group_name || "this group"} yet.</span>
                        <a class="back-home" href="${MY_URL}"><span>Back</span></a>
                    </div>
                    `
                );
                noPostsShown = true;
                window.removeEventListener("scroll", handleScroll);
            } else {
                // Plus de posts à charger
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

function renderPost(group_slug, post) {
    const postImageHtml = post.post_picture_path
        ? `<img src="${POST_IMG_PATH}${post.post_picture_path}" alt="Feedback" class="post_img" onclick="showGroupCommentsModal(${post.post_id})">`
        : '';

    const postDate = new Date(post.time_stamp);
    const options = { day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' };
    const formattedDate = postDate.toLocaleDateString('en-US', options);

    return `
        <div class="post-container">
            <div class="post-top-row">
                <div class="user-profile">
                    <a href="${MY_URL}groups/${group_slug}/user/${post.user_slug}"><img src="${PROFILE_IMG_PATH}${post.user_pp_path}" alt="Avatar"></a>
                    <div class="post-data">
                        <a href="${MY_URL}groups/${group_slug}/user/${post.user_slug}"><span class="post-data-name">${post.firstname} ${post.lastname}</span></a>
                        <span class="post-data-date">${formattedDate} · <ion-icon src="${MY_URL}img/svg-icons/earth-outline.svg"></span>
                    </div>
                </div>
                <div class="post-setting">
                    <span><i class="fa fa-ellipsis-h"></i></span>
                    <span class="close">&times;</span>
                </div>
            </div>
            <p class="post_text">${post.post_description}</p>
            ${postImageHtml}
            <div class="post_stats">
                <div class="post_stats_item" onclick="showGroupLikesModal(${post.post_id})"> 
                    <ion-icon class="like-icon" src="${MY_URL}img/svg-icons/heart-circle.svg" ></ion-icon>
                    <span>${post.nb_likes}</span>
                </div>
                <div class="post_stats_item"> 
                    <span  onclick="showGroupCommentsModal(${post.post_id})">${post.nb_comments} comments </span>
                </div>
            </div>
            <div class="post-bottom-row">
                <div class="post-bottom-row-item">
                    <ion-icon src="${MY_URL}img/svg-icons/thumbs-up-outline.svg"></ion-icon>
                    <span>Likes</span>
                </div>
                <div class="post-bottom-row-item">
                    <ion-icon src="${MY_URL}img/svg-icons/chatbubble-outline.svg"></ion-icon>
                    <span>Comments</span>
                </div>
                <div class="post-bottom-row-item">
                    <ion-icon src="${MY_URL}img/svg-icons/logo-whatsapp.svg"></ion-icon>
                    <span>Send</span>
                </div>
                <div class="post-bottom-row-item">
                    <ion-icon src="${MY_URL}img/svg-icons/arrow-redo-outline.svg"></ion-icon>
                    <span>Share</span>
                </div>
            </div>
        </div>
    `;
}
