function showLikesModal(postId) {
    const modal = document.getElementById('likeModal');
    const modalBody = document.getElementById('likeModalBody');

    document.body.classList.add('body-no-scroll');
    modalBody.innerHTML = '<p>Loading...</p>';
    modal.style.display = 'block';

    fetch(`${API_BASE_URL}getLikes&postId=${postId}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                let html = '';
                data.forEach(user => {
                    html += `
                        <div class="like-user">
                            <a href="${MY_URL}profile/${user.user_slug}">
                                <img src="${PROFILE_IMG_PATH}${user.user_pp_path}" class="like-user-avatar" alt="${user.user_firstname}">
                                <div class="like-user-name">
                                    ${user.user_firstname} ${user.user_lastname}
                                </div>
                            </a>
                            <button class="add-friend-btn">
                                <i class="fas fa-user-plus"></i> Add to friends
                            </button>
                        </div>
                    `;
                });
                modalBody.innerHTML = html;
            } else {
                modalBody.innerHTML = '<p>No likes yet</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = '<p>Error loading likes</p>';
        });
}


document.querySelector('.close-like-modal').addEventListener('click', function () {
    document.getElementById('likeModal').style.display = 'none';
    document.body.classList.remove('body-no-scroll');
});


window.addEventListener('click', function (event) {
    const modal = document.getElementById('likeModal');
    if (event.target == modal) {
        document.body.classList.remove('body-no-scroll');
        modal.style.display = 'none';
    }
});


function showGroupLikesModal(postId) {
    const modal = document.getElementById('likeModal');
    const modalBody = document.getElementById('likeModalBody');

    document.body.classList.add('body-no-scroll');

    modalBody.innerHTML = '<p>Loading...</p>';
    modal.style.display = 'block';


    fetch(`${API_BASE_URL}getGroupLikes&postId=${postId}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                let html = '';
                data.forEach(user => {
                    html += `
                        <div class="like-user">
                            <a href="${MY_URL}profile/${user.user_slug}">
                                <img src="${PROFILE_IMG_PATH}${user.user_pp_path}" class="like-user-avatar" alt="${user.user_firstname}">
                                <div class="like-user-name">
                                    ${user.user_firstname} ${user.user_lastname}
                                </div>
                            </a>
                            <button class="add-friend-btn">
                                <i class="fas fa-user-plus"></i> Add to friends
                            </button>
                        </div>
                    `;
                });
                modalBody.innerHTML = html;
            } else {
                modalBody.innerHTML = '<p>No likes yet</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = '<p>Error loading likes</p>';
        });
}
