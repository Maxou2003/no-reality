
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
    const nav_links = modal.querySelectorAll(".nav-link");

    modal.style.display = 'flex';

    const comments = document.querySelector(".comments");

    comments.innerHTML = "";
    const apiUrl = `${API_BASE_URL}getModalPost&postId=${postId}`;
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            const post = data.post;
            modalImage.src = POST_IMG_PATH + post.post_picture_path;
            description.innerHTML = post.post_description;
            likes.innerHTML = post.nb_likes;
            for (i = 0; i < 2; i++) {
                timestamp[i].innerHTML = post.time_stamp;
                username[i].innerHTML = post.username;
                profile_img[i].src = PROFILE_IMG_PATH + post.user_pp_path;
                nav_links[i].href = MY_URL + "profile/" + post.username;
            }
            data.comments.forEach(comment => {
                const commentElement = document.createElement("div");
                let showResponseBtn = "";
                if (comment.nb_responses > 0) {
                    showResponseBtn = `<button class="show-response-btn"  commentid="${comment.comment_id}">View all ${comment.nb_responses} responses</button>`
                }
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
                            ${showResponseBtn}
                        </div>
                    </div>
                    <div class="comment-responses" commentid="${comment.comment_id}"></div>
                    `;
                comments.appendChild(commentElement);

            });
            responses();
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
    document.getElementById('modal-image').src = '';
    modal.querySelectorAll(".profile-img").src = '';
    modal.querySelector(".description").innerHTML = '';
    document.querySelector(".comments").innerHTML = '';
    modal.querySelector(".likes").innerHTML = '';
    const timestamps = modal.querySelectorAll(".timestamp");
    const usernames = modal.querySelectorAll(".username");
    for (i = 0; i < 2; i++) {
        timestamps[i].innerHTML = '';
        usernames[i].innerHTML = '';
    }

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

function responses() {

    const commentResponses = document.querySelectorAll(".comment-responses");
    const viewResponsesBtn = document.querySelectorAll(".show-response-btn");

    for (let i = 0; i < viewResponsesBtn.length; i++) {
        viewResponsesBtn[i].addEventListener("click", () => {

            const buttonCommentId = viewResponsesBtn[i].getAttribute('commentid');

            const targetResponse = Array.from(commentResponses).find(element =>
                element.getAttribute('commentid') === buttonCommentId
            );

            if (targetResponse) {
                console.log(`style: ${targetResponse.style.display}, commentid: ${buttonCommentId}, targetResponse: ${targetResponse.getAttribute('commentid')}`);

                if (targetResponse.style.display === 'flex') {
                    targetResponse.style.display = 'none';
                    viewResponsesBtn[i].innerHTML = "View all responses";
                    console.log(`Decision : hide\n`);

                } else {
                    if (targetResponse.innerHTML == "") {
                        getResponses(targetResponse);
                    }
                    targetResponse.style.display = 'flex';
                    viewResponsesBtn[i].innerHTML = "Hide all responses";
                    console.log(`Decision : show\n`);
                }
            }
        });
    }
}

function getResponses(commentResponses) {

    apiUrl = `${API_BASE_URL}getResponses&commentId=${commentResponses.getAttribute('commentId')}`;


    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            commentResponses.innerHTML = ""; // Clear previous content

            data.forEach(response => {
                const responseElement = document.createElement("div");
                responseElement.classList.add("response");
                responseElement.innerHTML = `
                <div class="custom-modal-comment-profile-img">
                    <img src="${PROFILE_IMG_PATH}${response.user_profile_picture}" alt="Image">
                </div>
                <div class="response-content">
                    <span class="username">${response.user_username}</span>
                    <span class="response-text">${response.content}</span>
                    <div class="timestamp">
                        <span>${response.time_stamp}</span>
                    </div>
                </div>
                `;
                commentResponses.appendChild(responseElement);
            });

        })
        .catch(error => console.error("Error fetching responses:", error));
}


