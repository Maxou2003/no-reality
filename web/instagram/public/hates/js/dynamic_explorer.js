
document.addEventListener("DOMContentLoaded", function () {
    let loading = false;
    let page = 1;
    const nb_explorer_post = 12;

    async function loadMorePosts() {
        if (loading) return;
        loading = true;

        try {
            const response = await fetch(`${API_BASE_URL}getMorePosts&page=${page}&nbPosts=${nb_explorer_post}`);
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            const newPosts = await response.json();

            if (newPosts.length > 0) {
                // newPosts.forEach(post => {
                //     document.querySelector(".explore-posts").insertAdjacentHTML("beforeend", `
                //         <div class="post" onclick="openModalPost('${post.post_id}')">
                //             <img src="${POST_IMG_PATH}${post.post_picture_path}" alt="Post" loading="lazy">
                // 	    </div>
                //     `);
                // });

                const fragment = document.createDocumentFragment();
                newPosts.forEach(post => {
                    const postElement = document.createElement('div');
                    postElement.classList.add('post');
                    postElement.innerHTML = `
                        <img src="${POST_IMG_PATH}${post.post_picture_path}" srcset="${POST_IMG_PATH}${post.post_picture_path}" alt="Post" loading="lazy">
                    `;
                    postElement.addEventListener('click', () => openModalPost(post.post_id));
                    fragment.appendChild(postElement);
                });
                document.querySelector(".explore-posts").appendChild(fragment);

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
            console.log("load more");
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
