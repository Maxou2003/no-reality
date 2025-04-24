
async function getUserId()
{
    var splitted_location = window.location.href.split("/").filter((val) => val != "");
    var userSlug = splitted_location[splitted_location.length - 1];
    try {           
        const response = await fetch(`${API_BASE_URL}getUserIdBySlug&userSlug=${userSlug}`);
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

        const data = await response.json();
        return data.user_id;  
        
    } catch (error) {
        console.error("Error getting user id :", error);
        return -1;
    }
}

document.addEventListener("DOMContentLoaded", function () {
    getUserId()
    .then(user_id => {
        let loading = false;
        let page = 1;
        const nb_home_post = 10;
    
        async function loadMorePosts() {
            if (loading) return;
            loading = true;
    
            try {           
                const response = await fetch(`${API_BASE_URL}getDynamicUserPosts&page=${page}&nbPosts=${nb_home_post}&userId=${user_id}`);
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
    
                const newPosts = await response.json();
    
                if (newPosts.length > 0) {
                    console.log(newPosts);
                    newPosts.forEach(post => {
                        document.querySelector(".post_col").insertAdjacentHTML("beforeend", renderPost(post));
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
});

function renderPost(post) {
    const postImageHtml = post.post_picture_path
        ? `<img src="${POST_IMG_PATH}${post.post_picture_path}" alt="Feedback" class="post_img" onclick="showCommentsModal(${post.post_id})">`
        : '';

    const postDate = new Date(post.time_stamp);
    const options = { day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' };
    const formattedDate = postDate.toLocaleDateString('en-US', options);

    return `
        <div class="post-container">
            <div class="post-top-row">
                <div class="user-profile">
                    <a href="${MY_URL}profile/${post.user_slug}"><img src="${PROFILE_IMG_PATH}${post.user_pp_path}" alt="Avatar"></a>
                    <div class="post-data">
                        <a href="${MY_URL}profile/${post.user_slug}"><span class="post-data-name">${post.firstname} ${post.lastname}</span></a>
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
                <div class="post_stats_item" onclick="showLikesModal(${post.post_id})"> 
                    <ion-icon class="like-icon" src="${MY_URL}img/svg-icons/heart-circle.svg" ></ion-icon>
                    <span>${post.nb_likes}</span>
                </div>
                <div class="post_stats_item"> 
                    <span  onclick="showCommentsModal(${post.post_id})">${post.nb_comments} comments </span>
                    <span onclick="showSharesModal(${post.post_id})">${post.nb_shares} shares</span>
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
