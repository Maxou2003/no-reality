function openModal(imageSrc) {
    const modal = document.getElementById('post-modal');
    const modalImage = document.getElementById('modal-image');
    modalImage.src = imageSrc;
    modal.style.display = 'flex';
    escapeModal();
}

function openModalFollow(followers) {
    const modal = document.getElementById('post-modal-follow');
    const follow_modal_body = document.querySelector(".follow-modal-body");
    const followersBtn = document.getElementById("follow-btn");
    const userId = followersBtn.getAttribute("data-user-id"); // Get user ID
    let headerdiv = document.querySelector(".follow-modal-header h2");

    const apiUrl = followers ? `${API_BASE_URL}getFollowers&user_id=${userId}` : `${API_BASE_URL}getFollowings&user_id=${userId}`;

    if (!followers) {
        headerdiv.innerHTML = "Following";
    } else {
        headerdiv.innerHTML = "Followers";
    }

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            follow_modal_body.innerHTML = ""; // Clear previous content

            data.forEach(user => {
                const userElement = document.createElement("div");
                userElement.classList.add("follower-item");
                userElement.innerHTML = `
                    <div class="follow-modal-profile">
                        <div class="follow-modal-profile-img">
                            <img src="${PROFILE_IMG_PATH}${user.profile_picture}" alt="Image">
                        </div>
                        <div class="follow-modal-profile-info">
                            <a class="nav-link" href="${MY_URL}profile/${user.username}">
                                <span class="user-name">${user.username}</span>
                            </a>
                            <span class="user-full-name">${user.firstname} ${user.lastname} </span>
                        </div>
                        <button class="follow-btn">Follow</button>
                    </div>
                    `;
                follow_modal_body.appendChild(userElement);
            });

        })
        .catch(error => console.error("Error fetching followers:", error));
    modal.style.display = 'flex';
    escapeModalFollow();
}

function openModalPost(postId) {
    const modalImage = document.getElementById('modal-image');
    const modal = document.getElementById('post-modal');
    const description = modal.querySelector(".description");
    const likes = modal.querySelector(".likes");
    const timestamp = modal.querySelectorAll(".timestamp");
    const username = modal.querySelectorAll(".username");
    const profile_img = modal.querySelectorAll(".profile-img");
    
    modal.style.display = 'flex';
    
    const comments = document.querySelector(".comments");

    comments.innerHTML = "";
    const apiUrl = `${API_BASE_URL}getModalPost&postId=${postId}`;
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            const post = data.post;
            modalImage.src =  POST_IMG_PATH+ post.post_picture_path;
            description.innerHTML = post.post_description;
            likes.innerHTML = post.nb_likes;
            for (i=0;i<2;i++) {
                timestamp[i].innerHTML = post.time_stamp.date;
                username[i].innerHTML = post.username;
                profile_img[i].src = PROFILE_IMG_PATH + post.user_pp_path;
            }
            data.comments.forEach(comment => {
                const commentElement = document.createElement("div");
                commentElement.classList.add("comment-item");
                commentElement.innerHTML = `
                    <div class="comment">
						<div class="custom-modal-comment-profile-img">
							<img src="${PROFILE_IMG_PATH}${comment.user_profile_picture}" alt="Image">
						</div>
                        <div class="comment-content">
                            <a class="nav-link" href="${MY_URL}profile/${comment.user_username}">
                                <span class="username">${comment.user_username}</span>
                            </a>
                            <span class="comment-text">${comment.comment_text}</span>
                            <div class="timestamp">
                                    <span>${comment.time_stamp}</span>
                            </div>
                            <button class="show-response-btn">View all DUR responses</button>
                        </div>

                    </div>
                    `;
                comments.appendChild(commentElement);
            });

        })
        .catch(error => console.error("Error fetching post:", error));
    escapeModal();
}

function search() {
    const searchBar = document.querySelector('#follow-search-container input');
    const follow_modal_body = document.querySelector(".follow-modal-body");
    const followersBtn = document.getElementById("follow-btn");
    const userId = followersBtn.getAttribute("data-user-id");
    let headerdiv = document.querySelector(".follow-modal-header h2");
    let apiUrl = "";


    if (headerdiv.innerHTML == "Followers") {
        if (searchBar.value === "") {
            apiUrl = `${API_BASE_URL}getFollowers&user_id=${userId}`;
        } else {
            apiUrl = `${API_BASE_URL}searchInFollowers&user_id=${userId}&searchContent=${searchBar.value}&follow=1`
        }

    } else {
        if (searchBar.value === "") {
            apiUrl = `${API_BASE_URL}getFollowings&user_id=${userId}`;
        } else {
            apiUrl = `${API_BASE_URL}searchInFollowers&user_id=${userId}&searchContent=${searchBar.value}&follow=0`;
        }
    }
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            follow_modal_body.innerHTML = ""; // Clear previous content

            data.forEach(user => {
                const userElement = document.createElement("div");
                userElement.classList.add("follower-item");
                userElement.innerHTML = `
                    <div class="follow-modal-profile">
                        <div class="follow-modal-profile-img">
                            <img src="${PROFILE_IMG_PATH}${user.profile_picture}" alt="Image">
                        </div>
                        <div class="follow-modal-profile-info">
                            <a class="nav-link" href="${MY_URL}profile/${user.username}">
                                <span class="user-name">${user.username}</span>
                            </a>
                            <span class="user-full-name">${user.firstname} ${user.lastname} </span>
                        </div>
                        <button class="follow-btn">Follow</button>
                    </div>
                    `;
                follow_modal_body.appendChild(userElement);
            });

        })
        .catch(error => console.error("Error fetching followers:", error));
}


function closeModal() {
    const heartButtons = document.querySelectorAll('.heart_icon');
    for (let i = 0; i < heartButtons.length; i++) {
        heartButtons[i].childNodes[1].name = 'heart-outline';
        heartButtons[i].className = 'heart_icon';
    }
    const modal = document.getElementById('post-modal');
    modal.style.display = 'none';
}

function closeModalFollow() {
    const searchBar = document.querySelector('.search-container input');
    searchBar.value = '';
    const modal_follow = document.getElementById('post-modal-follow');
    modal_follow.style.display = 'none';
}

function escapeModal() {
    addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
}

function escapeModalFollow() {
    addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModalFollow();
        }
    });
}


