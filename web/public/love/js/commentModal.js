// Comment modal for classic posts

async function showCommentsModal(postId) {
    const modal = document.getElementById('commentModal');
    const modalContent = document.querySelector('.comment-modal-content');

    // Prevent background scrolling
    document.body.classList.add('body-no-scroll');

    // Show loading state
    modalContent.innerHTML = '<div class="loading">Loading comments...</div>';
    modal.style.display = 'block';

    try {
        // Fetch post and comments data
        const response = await fetch(`${API_BASE_URL}getCommentModal&postId=${postId}`);
        const data = await response.json();

        if (!data.post) {
            throw new Error('Post data not available');
        }

        // Format the timestamp
        const postDate = new Date(data.post.time_stamp);
        const options = { day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' };
        const formattedDate = postDate.toLocaleDateString('fr-FR', options);

        // Generate HTML for tagged users
        let taggedUsersHtml = '';
        if (data.taggedUsers && data.taggedUsers.length > 0) {
            taggedUsersHtml = `<div class="tagged-users">
                <span>Avec </span>${data.taggedUsers.map(user =>
                `<a href="${MY_URL}profile/${user.user_slug}">${user.user_firstname} ${user.user_lastname}</a>`
            ).join(', ')}
            </div>`;
        }

        // Generate HTML for comments
        let commentsHtml = '';
        if (data.comments && data.comments.length > 0) {
            commentsHtml = data.comments.map(comment => {
                const commentDate = new Date(comment.time_stamp);
                const commentFormattedDate = commentDate.toLocaleDateString('fr-FR', {
                    day: 'numeric',
                    month: 'short',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                return `
                <div class="comment" data-comment-id="${comment.comment_id}">
                    <div class="comment-header">
                        <img src="${PROFILE_IMG_PATH}${comment.user_profile_picture}" class="comment-avatar" alt="${comment.user_firstname}">
                        <div class="comment-text-container">
                            <div class="comment-author">${comment.user_firstname} ${comment.user_lastname}</div>
                            <div class="comment-content">${comment.comment_text}</div>
                        </div>
                    </div>
                    <div class="comment-meta">
                        <span class="comment-time">${commentFormattedDate}</span>
                        <span class="comment-action">Likes</span>
                        <span class="comment-action">Send</span>
                        <span class="comment-action">Share</span>
                    </div>
                    ${comment.nb_responses > 0 ? `
                        <div class="comment-replies" onclick="toggleResponses(${comment.comment_id}, this)">
                            See all ${comment.nb_responses} responses
                        </div>
                        <div class="responses-container" id="responses-${comment.comment_id}" style="display: none;"></div>
                        ` : ''}
                    </div>`;
            }).join('');
        } else {
            commentsHtml = '<div class="no-comments">No comment as of now</div>';
        }

        // Update modal content
        modalContent.innerHTML = `
            <div class="comment-modal-header">
                <div class="comment-modal-title">Post from ${data.post.firstname}</div>
                <span class="close-comment-modal">&times;</span>
            </div>

            <div class="post_header">
                <div class="post-profile">
                    <a class="profile_img" href="${MY_URL}profile/${data.post.user_slug}">
                        <img src="${PROFILE_IMG_PATH}${data.post.user_pp_path}">
                    </a>
                    <div class="profile-data">
                        <a class="profile-name" href="${MY_URL}profile/${data.post.user_slug}">
                            ${data.post.firstname} ${data.post.lastname}
                        </a>
                        <div class="post_timestamp">
                            ${formattedDate} · <ion-icon name="earth-outline"></ion-icon>
                        </div>
                        ${taggedUsersHtml}
                    </div>
                </div>
                <span><i class="fa fa-ellipsis-h"></i></span>
            </div>

            <div class="original-post">
                <p class="post_text">${data.post.post_description}</p>
                ${data.post.post_picture_path ? `<img src="${POST_IMG_PATH}${data.post.post_picture_path}" alt="Post image" class="post_img">` : ''}
                <div class="post_stats">
                    <div class="post_stats_item" onclick="showLikesModal(${data.post.post_id})">
                        <ion-icon class="like-icon" src="${MY_URL}img/svg-icons/heart-circle.svg"></ion-icon>
                        <span>${data.post.nb_likes}</span>
                    </div>
                    <div class="post_stats_item">
                        <span>${data.post.nb_comments} comments</span>
                        <span>${data.post.nb_shares} shares</span>
                    </div>
                </div>
                <div class="post-bottom-row">
                    <div class="post-bottom-row-item">
                        <ion-icon src="${MY_URL}img/svg-icons/thumbs-up-outline.svg"></ion-icon>
                        <span>Like</span>
                    </div>
                    <div class="post-bottom-row-item">
                        <ion-icon src="${MY_URL}img/svg-icons/chatbubble-outline.svg"></ion-icon>
                        <span>Comment</span>
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

            <div class="comment-section">
                ${commentsHtml}
                <div class="comment-input-area">
                    <input type="text" class="comment-input" placeholder="Write a comment...">
                    <button class="send-comment-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>`;

    } catch (error) {
        console.error('Error loading comments:', error);
        modalContent.innerHTML = `
            <div class="error">
                <div class="comment-modal-header">
                    <div class="comment-modal-title">Error</div>
                    <span class="close-comment-modal">&times;</span>
                </div>
                <p>Impossible to load comments</p>
            </div>`;
    }

    // Close modal when clicking X
    document.querySelector('.close-comment-modal').addEventListener('click', function () {
        modal.style.display = 'none';
        document.body.classList.remove('body-no-scroll');
    });

    // Close modal when clicking outside
    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            modal.style.display = 'none';
            document.body.classList.remove('body-no-scroll');
        }
    });
}

async function toggleResponses(commentId, toggleElement) {
    const responsesContainer = document.getElementById(`responses-${commentId}`);
    const isHidden = responsesContainer.style.display === 'none';

    if (isHidden) {
        // Show loading state
        responsesContainer.innerHTML = '<div class="loading-responses">Loading...</div>';
        responsesContainer.style.display = 'block';

        try {
            // Fetch responses
            const response = await fetch(`${API_BASE_URL}getResponses&commentId=${commentId}`);
            const responses = await response.json();

            if (responses.length > 0) {
                let responsesHtml = responses.map(response => {
                    const responseDate = new Date(response.time_stamp);
                    const formattedDate = responseDate.toLocaleDateString('fr-FR', {
                        day: 'numeric',
                        month: 'short',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    return `
                    <div class="response">
                        <div class="response-header">
                            <img src="${PROFILE_IMG_PATH}${response.user_profile_picture}" class="response-avatar">
                            <div class="response-text-container">
                                <div class="response-author">${response.user_firstname} ${response.user_lastname}</div>
                                <div class="response-content">${response.content}</div>
                            </div>
                        </div>
                        <div class="response-meta">
                            <span class="response-time">${formattedDate}</span>
                            <span class="comment-action">Likes</span>
                            <span class="comment-action">Send</span>
                            <span class="comment-action">Share</span>
                        </div>
                    </div>`;
                }).join('');

                responsesContainer.innerHTML = responsesHtml;
                toggleElement.textContent = `Hide all ${responses.length} responses`;
            } else {
                responsesContainer.innerHTML = '<div class="no-responses">No responses </div>';
            }
        } catch (error) {
            console.error('Error loading responses:', error);
            responsesContainer.innerHTML = '<div class="error">Loading error...</div>';
        }
    } else {
        // Hide responses
        responsesContainer.style.display = 'none';
        toggleElement.textContent = `See all responses`;
    }
}

// Comment modal for group posts

async function showCommentsModal(postId) {
    const modal = document.getElementById('commentModal');
    const modalContent = document.querySelector('.comment-modal-content');

    // Prevent background scrolling
    document.body.classList.add('body-no-scroll');

    // Show loading state
    modalContent.innerHTML = '<div class="loading">Loading comments...</div>';
    modal.style.display = 'block';

    try {
        // Fetch post and comments data
        const response = await fetch(`${API_BASE_URL}getGroupCommentModal&postId=${postId}`);
        const data = await response.json();

        if (!data.post) {
            throw new Error('Post data not available');
        }

        // Format the timestamp
        const postDate = new Date(data.post.time_stamp);
        const options = { day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' };
        const formattedDate = postDate.toLocaleDateString('fr-FR', options);

        // Generate HTML for tagged users
        let taggedUsersHtml = '';
        if (data.taggedUsers && data.taggedUsers.length > 0) {
            taggedUsersHtml = `<div class="tagged-users">
                <span>Avec </span>${data.taggedUsers.map(user =>
                `<a href="${MY_URL}profile/${user.user_slug}">${user.user_firstname} ${user.user_lastname}</a>`
            ).join(', ')}
            </div>`;
        }

        // Generate HTML for comments
        let commentsHtml = '';
        if (data.comments && data.comments.length > 0) {
            commentsHtml = data.comments.map(comment => {
                const commentDate = new Date(comment.time_stamp);
                const commentFormattedDate = commentDate.toLocaleDateString('fr-FR', {
                    day: 'numeric',
                    month: 'short',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                return `
                <div class="comment" data-comment-id="${comment.comment_id}">
                    <div class="comment-header">
                        <img src="${PROFILE_IMG_PATH}${comment.user_profile_picture}" class="comment-avatar" alt="${comment.user_firstname}">
                        <div class="comment-text-container">
                            <div class="comment-author">${comment.user_firstname} ${comment.user_lastname}</div>
                            <div class="comment-content">${comment.comment_text}</div>
                        </div>
                    </div>
                    <div class="comment-meta">
                        <span class="comment-time">${commentFormattedDate}</span>
                        <span class="comment-action">Likes</span>
                        <span class="comment-action">Send</span>
                        <span class="comment-action">Share</span>
                    </div>
                    ${comment.nb_responses > 0 ? `
                        <div class="comment-replies" onclick="toggleResponses(${comment.comment_id}, this)">
                            See all ${comment.nb_responses} responses
                        </div>
                        <div class="responses-container" id="responses-${comment.comment_id}" style="display: none;"></div>
                        ` : ''}
                    </div>`;
            }).join('');
        } else {
            commentsHtml = '<div class="no-comments">No comment as of now</div>';
        }

        // Update modal content
        modalContent.innerHTML = `
            <div class="comment-modal-header">
                <div class="comment-modal-title">Post from ${data.post.firstname}</div>
                <span class="close-comment-modal">&times;</span>
            </div>

            <div class="post_header">
                <div class="post-profile">
                    <a class="profile_img" href="${MY_URL}profile/${data.post.user_slug}">
                        <img src="${PROFILE_IMG_PATH}${data.post.user_pp_path}">
                    </a>
                    <div class="profile-data">
                        <a class="profile-name" href="${MY_URL}profile/${data.post.user_slug}">
                            ${data.post.firstname} ${data.post.lastname}
                        </a>
                        <div class="post_timestamp">
                            ${formattedDate} · <ion-icon name="earth-outline"></ion-icon>
                        </div>
                        ${taggedUsersHtml}
                    </div>
                </div>
                <span><i class="fa fa-ellipsis-h"></i></span>
            </div>

            <div class="original-post">
                <p class="post_text">${data.post.post_description}</p>
                ${data.post.post_picture_path ? `<img src="${POST_IMG_PATH}${data.post.post_picture_path}" alt="Post image" class="post_img">` : ''}
                <div class="post_stats">
                    <div class="post_stats_item" onclick="showLikesModal(${data.post.post_id})">
                        <ion-icon class="like-icon" src="${MY_URL}img/svg-icons/heart-circle.svg"></ion-icon>
                        <span>${data.post.nb_likes}</span>
                    </div>
                    <div class="post_stats_item">
                        <span>${data.post.nb_comments} comments</span>
                        <span>${data.post.nb_shares} shares</span>
                    </div>
                </div>
                <div class="post-bottom-row">
                    <div class="post-bottom-row-item">
                        <ion-icon src="${MY_URL}img/svg-icons/thumbs-up-outline.svg"></ion-icon>
                        <span>Like</span>
                    </div>
                    <div class="post-bottom-row-item">
                        <ion-icon src="${MY_URL}img/svg-icons/chatbubble-outline.svg"></ion-icon>
                        <span>Comment</span>
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

            <div class="comment-section">
                ${commentsHtml}
                <div class="comment-input-area">
                    <input type="text" class="comment-input" placeholder="Write a comment...">
                    <button class="send-comment-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>`;

    } catch (error) {
        console.error('Error loading comments:', error);
        modalContent.innerHTML = `
            <div class="error">
                <div class="comment-modal-header">
                    <div class="comment-modal-title">Error</div>
                    <span class="close-comment-modal">&times;</span>
                </div>
                <p>Impossible to load comments</p>
            </div>`;
    }

    // Close modal when clicking X
    document.querySelector('.close-comment-modal').addEventListener('click', function () {
        modal.style.display = 'none';
        document.body.classList.remove('body-no-scroll');
    });

    // Close modal when clicking outside
    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            modal.style.display = 'none';
            document.body.classList.remove('body-no-scroll');
        }
    });
}

async function toggleResponses(commentId, toggleElement) {
    const responsesContainer = document.getElementById(`responses-${commentId}`);
    const isHidden = responsesContainer.style.display === 'none';

    if (isHidden) {
        // Show loading state
        responsesContainer.innerHTML = '<div class="loading-responses">Loading...</div>';
        responsesContainer.style.display = 'block';

        try {
            // Fetch responses
            const response = await fetch(`${API_BASE_URL}getResponses&commentId=${commentId}`);
            const responses = await response.json();

            if (responses.length > 0) {
                let responsesHtml = responses.map(response => {
                    const responseDate = new Date(response.time_stamp);
                    const formattedDate = responseDate.toLocaleDateString('fr-FR', {
                        day: 'numeric',
                        month: 'short',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    return `
                    <div class="response">
                        <div class="response-header">
                            <img src="${PROFILE_IMG_PATH}${response.user_profile_picture}" class="response-avatar">
                            <div class="response-text-container">
                                <div class="response-author">${response.user_firstname} ${response.user_lastname}</div>
                                <div class="response-content">${response.content}</div>
                            </div>
                        </div>
                        <div class="response-meta">
                            <span class="response-time">${formattedDate}</span>
                            <span class="comment-action">Likes</span>
                            <span class="comment-action">Send</span>
                            <span class="comment-action">Share</span>
                        </div>
                    </div>`;
                }).join('');

                responsesContainer.innerHTML = responsesHtml;
                toggleElement.textContent = `Hide all ${responses.length} responses`;
            } else {
                responsesContainer.innerHTML = '<div class="no-responses">No responses </div>';
            }
        } catch (error) {
            console.error('Error loading responses:', error);
            responsesContainer.innerHTML = '<div class="error">Loading error...</div>';
        }
    } else {
        // Hide responses
        responsesContainer.style.display = 'none';
        toggleElement.textContent = `See all responses`;
    }
}