function openModalPost(postId) {
    const modalImage = document.getElementById('modal-image');
    const modal = document.getElementById('post-modal');
    const description = modal.querySelector(".description");
    const likes = modal.querySelector(".likes");
    const liketext = modal.querySelector(".like-text");
    const timestamp = modal.querySelectorAll(".timestamp");
    const username = modal.querySelectorAll(".username");
    const profile_img = modal.querySelectorAll(".profile-img");
    const nav_links = modal.querySelectorAll(".nav-link");
    const pp_links = modal.querySelectorAll(".custom-modal-profile-img a");

    modal.style.display = 'flex';

    liketext.setAttribute('onclick', `openModalLikes(${postId})`);

    const comments = document.querySelector(".comments");

    comments.innerHTML = "";
    const apiUrl = `${API_BASE_URL}getModalPost&postId=${postId}`;
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            const post = data.post;
            const taggedUsers = data.taggedUsers;
            let descriptionContent = post.post_description;

            taggedUsers.forEach(taggedUser => {
                descriptionContent += ` <a href="${MY_URL}profile/${taggedUser}">@${taggedUser}</a>`;
            });

            description.innerHTML = descriptionContent;

            modalImage.src = POST_IMG_PATH + post.post_picture_path;

            pp_links.forEach(link => link.href = MY_URL + "profile/" + post.username);

            likes.innerHTML = post.nb_likes;

            for (i = 0; i < 2; i++) {
                timestamp[i].innerHTML = post.time_stamp;
                username[i].innerHTML = post.username;
                profile_img[i].src = PROFILE_IMG_PATH + post.user_pp_path;
                nav_links[i].href = MY_URL + "profile/" + post.username;
            }

            // Mobile Version 
            if (window.innerWidth <= 768) {
                const modalContent = document.querySelector('.custom-modal-content');
                const mobileHeader = document.createElement('div');
                const chatbubble = document.querySelector('.custom-modal-right ion-icon[name="chatbubble-outline"]');
                const actions = document.querySelector('.custom-modal-actions');
                mobileHeader.classList.add('mobile-header');
                mobileHeader.innerHTML = `
                    <div class="mobile-header-redirection">
                        <ion-icon name="arrow-back-outline"class="arrow" onclick="closeModalPost()"></ion-icon>
                        <h2>Post</h2>       
                    </div>
                    <div class="custom-modal-post-header">
                        <div class="custom-modal-profile-info">
                            <div class="custom-modal-profile-img">
                                <a href="${MY_URL}profile/${post.username}"><img src="${PROFILE_IMG_PATH + post.user_pp_path}" class="profile-img" alt="Image"></a>
                            </div>
                            <a class="nav-link" href="${MY_URL}profile/${post.username}">
                                <span class="username">${post.username}</span>
                            </a>
                        </div>
                        <div class="options">
                            <span>
                                <ion-icon name="ellipsis-vertical"></ion-icon>
                            </span>
                        </div>
                    </div>
                `;
                chatbubble.setAttribute('onclick', `openModalComments('${postId}')`);
                modalContent.insertBefore(mobileHeader, modalContent.firstChild);
                const like_text = document.createElement('div');
                like_text.classList.add('like-text');
                like_text.innerHTML = `
                    <span class="likes">${post.nb_likes}</span>
                `;
                like_text.setAttribute('onclick', `openModalLikes('${postId}')`);
                actions.insertBefore(like_text, modal.querySelector('.chat_icon'));

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
							<a href="${MY_URL}profile/${comment.user_username}"><img src="${PROFILE_IMG_PATH}${comment.user_profile_picture}" alt="Image"></a>
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
    noScrollOutside();
    escapeModal();
    document.addEventListener('click', closeModalPostOnClickOutside);
}

function closeModalPostOnClickOutside(event) {

    const modal = document.querySelector('.custom-modal-content');

    if (!modal.contains(event.target) && !event.target.matches(".post img") && !event.target.matches('ion-icon[name="chatbubble-outline"] ') && !event.target.matches(".post_comments span") && !event.target.matches(".likes-modal-content") && !event.target.matches(".likes-modal-content span") && !event.target.matches(".likes-modal-content img") && !event.target.matches(".comments-modal-content")) {
        document.removeEventListener('click', closeModalPostOnClickOutside);
        (event.target);
        closeModalPost();
    }
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

                if (targetResponse.style.display === 'flex') {
                    targetResponse.style.display = 'none';
                    viewResponsesBtn[i].innerHTML = "View all responses";

                } else {
                    if (targetResponse.innerHTML == "") {
                        getResponses(targetResponse);
                    }
                    targetResponse.style.display = 'flex';
                    viewResponsesBtn[i].innerHTML = "Hide all responses";
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
                <a href="${MY_URL}profile/${response.user_username}">
                    <img src="${PROFILE_IMG_PATH}${response.user_profile_picture}" alt="Image">
                </a>
                </div>
                <div class="response-content">
                    <a class="nav-link" href="${MY_URL}profile/${response.user_username}">
                        <span class="username">${response.user_username}</span>
                    </a>
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


function escapeModal() {
    addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModalPost();
        }
    });
}

function closeModalPost() {
    if (document.getElementById('post-modal-likes').style.display === 'flex' || document.getElementById('post-modal-comments').style.display === 'flex') {
        document.addEventListener('click', closeModalPostOnClickOutside);
        return;
    }
    const heartButtons = document.querySelectorAll('.heart_icon');
    for (let i = 0; i < heartButtons.length; i++) {
        const heartIcon = heartButtons[i].querySelector("ion-icon");
        if (heartIcon.name === "heart") {
            heartIcon.name = 'heart-outline';
        }
        heartButtons[i].className = 'heart_icon';
    }
    if (window.innerWidth <= 768) {
        const mobile_header = document.querySelector('.mobile-header');
        const like_text = document.querySelector(".custom-modal-actions .like-text");
        like_text ? like_text.remove() : '';
        mobile_header.innerHTML = '';
    }

    const modal = document.getElementById('post-modal');
    modal.style.display = 'none';
    document.getElementById('modal-image').src = '';
    modal.querySelectorAll(".profile-img").forEach(img => img.src = '');
    modal.querySelector(".description").innerHTML = '';
    document.querySelector(".comments").innerHTML = '';
    modal.querySelector(".likes").innerHTML = '';
    const timestamps = modal.querySelectorAll(".timestamp");
    const usernames = modal.querySelectorAll(".username");

    const minLength = Math.min(timestamps.length, usernames.length, 2);
    for (let i = 0; i < minLength; i++) {
        timestamps[i].innerHTML = '';
        usernames[i].innerHTML = '';
    }

    scrollOutside();
}

function noScrollOutside() {
    const body = document.querySelector('body');
    if (body) {
        body.classList.add('no-scroll');
    }
}

function scrollOutside() {
    const body = document.querySelector('body');
    if (body) {
        body.classList.remove('no-scroll');
    }
}





