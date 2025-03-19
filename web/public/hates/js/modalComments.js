function openModalComments(postId) {
    if (window.innerWidth <= 768) {
        const modal = document.getElementById('post-modal-comments');
        const description = modal.querySelector(".description");
        const timestamp = modal.querySelector(".timestamp");
        const username = modal.querySelector(".username");
        const profile_img = modal.querySelector(".profile-img");
        const nav_links = modal.querySelector(".nav-link");
        const pp_link = modal.querySelector(".custom-modal-profile-img a");

        modal.style.display = 'flex';

        const comments = modal.querySelector(".comments");

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

                pp_link.href = MY_URL + "profile/" + post.username;
                timestamp.innerHTML = post.time_stamp;
                username.innerHTML = post.username;
                profile_img.src = PROFILE_IMG_PATH + post.user_pp_path;
                nav_links.href = MY_URL + "profile/" + post.username;


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
                commentResponses();
            })
            .catch(error => console.error("Error fetching post:", error));
        noScrollOutsideComments();
        escapeModalComments();
        document.addEventListener('click', closeModalCommentsOnClickOutside);
    }

}

function closeModalCommentsOnClickOutside(event) {

    const modal = document.querySelector('.comments-modal-content');

    if (!modal.contains(event.target) && !event.target.matches('ion-icon[name="chatbubble-outline"] ') && !event.target.matches(".post_comments span")) {
        document.removeEventListener('click', closeModalCommentsOnClickOutside);
        closeModalComments();
    }
}

function commentResponses() {
    const comments = document.querySelector(".comments-modal .comments");
    comments.addEventListener("click", commentResponsesCallback);
}

function commentResponsesCallback(event) {
    const button = event.target.closest(".show-response-btn");
    if (button) {
        const buttonCommentId = button.getAttribute('commentid');
        const targetResponse = document.querySelector(`.comments-modal .comment-responses[commentid="${buttonCommentId}"]`);
        if (targetResponse) {
            if (targetResponse.style.display === 'flex') {
                targetResponse.style.display = 'none';
                button.innerHTML = "View all responses";
            } else {
                if (targetResponse.innerHTML == "") {
                    getCommentResponses(targetResponse);
                }
                targetResponse.style.display = 'flex';
                button.innerHTML = "Hide all responses";
            }
        }
    }
}

function getCommentResponses(commentResponses) {

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


function escapeModalComments() {
    addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModalComments();
        }
    });
}

function closeModalComments() {

    const modal = document.getElementById('post-modal-comments');
    modal.style.display = 'none';
    modal.querySelectorAll(".profile-img").forEach(img => img.src = '');
    modal.querySelector(".description").innerHTML = '';
    const timestamp = modal.querySelector(".timestamp");
    const username = modal.querySelector(".username");
    const comments = modal.querySelector(".comments");
    comments.innerHTML = '';
    comments.addEventListener("click", commentResponsesCallback);
    timestamp.innerHTML = '';
    username.innerHTML = '';
    scrollOutsideComments();
}

function noScrollOutsideComments() {
    const body = document.querySelector('body');
    if (body) {
        body.classList.add('no-scroll');
    }
}

function scrollOutsideComments() {
    const body = document.querySelector('body');
    if (body) {
        body.classList.remove('no-scroll');
    }
}





