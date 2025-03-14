const modalPost = document.getElementById('post-modal');
const description = modalPost.querySelector(".description");
const likes = modalPost.querySelectorAll(".likes");
const like_text = modalPost.querySelectorAll(".like-text");
const timestamps = modalPost.querySelectorAll(".timestamp");
const usernames = modalPost.querySelectorAll(".username");
const profile_img = modalPost.querySelectorAll(".profile-img");
const modalImage = document.getElementById('modal-image');
const comments = document.querySelector(".comments");

function openModalPost(postId) {


    const nav_links = modalPost.querySelectorAll(".nav-link");
    const pp_links = modalPost.querySelectorAll(".custom-modal-profile-img a");
    const chat_icon = modalPost.querySelector(".chat_icon");


    modalPost.style.display = 'flex';
    comments.innerHTML = "";
    const apiUrl = `${API_BASE_URL}getModalPost&postId=${postId}`;
    chat_icon.setAttribute("onclick", `openModalComments('${postId}') ;`)

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

            likes.forEach(like => {
                like.setAttribute('onclick', `openModalLikes(${postId})`);
                like.innerHTML = post.nb_likes;
            });
            like_text.forEach(liketxt => {
                liketxt.setAttribute('onclick', `openModalLikes(${postId})`);
            });
            for (i = 0; i < 3; i++) {
                usernames[i].innerHTML = post.username;
                profile_img[i].src = PROFILE_IMG_PATH + post.user_pp_path;
                nav_links[i].href = MY_URL + "profile/" + post.username;
            }
            timestamps.forEach(timestamp => timestamp.innerHTML = post.time_stamp);

            const fragment = document.createDocumentFragment();
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

                fragment.appendChild(commentElement);
            });
            comments.appendChild(fragment);
            responses();
        })
        .catch(error => console.error("Error fetching post:", error));
    noScrollOutside();
    escapeModal();
    document.addEventListener('click', closeModalPostOnClickOutside);

}

function closeModalPostOnClickOutside(event) {

    const modalcontent = document.querySelector('.custom-modal-content');

    if (!modalcontent.contains(event.target) && !event.target.matches(".post img") && !event.target.matches('ion-icon[name="chatbubble-outline"] ') && !event.target.matches(".post_comments span") && !event.target.matches(".likes-modal-content") && !event.target.matches(".likes-modal-content span") && !event.target.matches(".likes-modal-content img") && !event.target.matches(".comments-modal-content")) {
        closeModalPost();
    }
}

function responses() {
    comments.addEventListener("click", responsesCallback);
}
function responsesCallback(event) {
    const button = event.target.closest(".show-response-btn");
    if (button) {
        const buttonCommentId = button.getAttribute('commentid');
        const targetResponse = document.querySelector(`.comment-responses[commentid="${buttonCommentId}"]`);
        if (targetResponse) {
            if (targetResponse.style.display === 'flex') {
                targetResponse.style.display = 'none';
                button.innerHTML = "View all responses";
            } else {
                if (targetResponse.innerHTML == "") {
                    getResponses(targetResponse);
                }
                targetResponse.style.display = 'flex';
                button.innerHTML = "Hide all responses";
            }
        }
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
    document.addEventListener('keydown', escapeModalCallBack);
}
function escapeModalCallBack(event) {
    if (event.key === 'Escape') {
        closeModalPost();
    }
}

function closeModalPost() {
    if (document.getElementById('post-modal-likes').style.display === 'flex' || document.getElementById('post-modal-comments').style.display === 'flex') {
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

    modalPost.style.display = 'none';
    modalImage.src = '';
    profile_img.forEach(img => img.src = '');
    modalPost.querySelector(".description").innerHTML = '';

    comments.innerHTML = '';
    likes.forEach(like => like.innerHTML = '');

    const minLength = Math.min(timestamps.length, usernames.length, 2);
    for (let i = 0; i < minLength; i++) {
        timestamps[i].innerHTML = '';
        usernames[i].innerHTML = '';
    }

    scrollOutside();
    document.removeEventListener('click', closeModalPostOnClickOutside);
    document.removeEventListener('keydown', escapeModalCallBack);
    comments.removeEventListener("click", responsesCallback);
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





