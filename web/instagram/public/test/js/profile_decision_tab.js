function switchActiveBtn(btnTag) {
    const activeDiv = btnTag === 'post' ? document.querySelector(".post-choice") : document.querySelector(".identification-choice");
    const unactiveDiv = !(btnTag === 'post') ? document.querySelector(".post-choice") : document.querySelector(".identification-choice");

    if (!activeDiv.classList.contains("active")) {
        activeDiv.classList.toggle("active");
        user_id = document.querySelector('.profile-info').getAttribute('user-id');
        unactiveDiv.classList.remove("active");
        openIdentifications(user_id, btnTag);
    }
}

function openIdentifications(user_id, choice) {
    const profile_posts = document.querySelector(".profile-posts");
    profile_posts.innerHTML = "";
    const apiUrl = `${API_BASE_URL}getIdentifications&userId=${user_id}&choice=${choice}`;
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            data.forEach(post => {
                const post_identification = document.createElement("div");
                post_identification.classList.add("post");
                post_identification.setAttribute('onclick', "openModalPost(" + post.post_id + ")");
                post_identification.innerHTML = `
                    <img src="${POST_IMG_PATH}${post.post_picture_path}" alt="Post">
                    `;
                profile_posts.appendChild(post_identification);
            });
        })
        .catch(error => console.error("Error fetching post:", error));
}