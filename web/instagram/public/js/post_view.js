POST_IMG_PATH = '/no-reality/web/instagram/public/img/post_img/';
PROFILE_IMG_PATH = '/no-reality/web/instagram/public/img/profile_picture/';
MY_URL = '/no-reality/web/instagram/public/';

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

    const apiUrl = followers ? '/no-reality/web/instagram/public/index.php?p=profile/getFollowers&user_id=' : '/no-reality/web/instagram/public/index.php?p=profile/getFollowings&user_id=';

    if (!followers) {
        headerdiv.innerHTML = "Following";
    } else {
        headerdiv.innerHTML = "Followers";
    }

    fetch(apiUrl + userId)
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

function openModalPost(imageSrc, postId, post_description, nb_likes, time_stamp) {
    const modal = document.getElementById('post-modal');
    const modalImage = document.getElementById('modal-image');
    modalImage.src = imageSrc;
    modal.style.display = 'flex';

    const description = modal.querySelector(".description");
    description.innerHTML = post_description;

    const likes = modal.querySelector(".likes");
    likes.innerHTML = nb_likes;

    const time_stamp_div = modal.querySelector(".time_stamp");
    time_stamp_div.innerHTML = time_stamp;

    const apiUrl = `/no-reality/web/instagram/public/index.php?p=profile/getComments&post_id=${postId}`;

    const comments = document.querySelector(".comments");
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            comments.innerHTML = "";
            data.forEach(comment => {
                const commentElement = document.createElement("div");
                commentElement.classList.add("comment-item");
                commentElement.innerHTML = `
                    <div class="comment">
						<div class="custom-modal-comment-profile-img">
							<img src="${PROFILE_IMG_PATH}${comment.user_profile_picture}" alt="Image">
						</div>
                        <div class="comment-content">
                            <a class="nav-link" href="${MY_URL}profile/${comment.username}">
                                <span class="username">${comment.user_username}</span>
                            </a>
                            <span class="comment-content">${comment.comment_text}</span>
                            <div class="timestamp">
                                    <span>${comment.time_stamp}</span>
                            </div>
                            <button class="show-response-btn">View all DUR responses</button>
                        </div>

                    </div>
                    `;
                comments.appendChild(commentElement);
            });
        }

        )
    escapeModal();
}

function search() {
    const searchBar = document.querySelector('.search-container input');
    const follow_modal_body = document.querySelector(".follow-modal-body");
    const followersBtn = document.getElementById("follow-btn");
    const userId = followersBtn.getAttribute("data-user-id");
    let headerdiv = document.querySelector(".follow-modal-header h2");
    let apiUrl = "";

    if (headerdiv.innerHTML == "Followers") {
        apiUrl = `/no-reality/web/instagram/public/index.php?p=profile/searchInFollowers&user_id=${userId}&searchContent=${searchBar.value}&follow=1`
    } else {
        apiUrl = `/no-reality/web/instagram/public/index.php?p=profile/searchInFollowers&user_id=${userId}&searchContent=${searchBar.value}&follow=0`;
    }

    if (searchBar.value === "") {
        follow_modal_body.innerHTML = "";

    } else {
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
                            <a class="nav-link" href="${MY_URL}profile/${user.user_username}">
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


