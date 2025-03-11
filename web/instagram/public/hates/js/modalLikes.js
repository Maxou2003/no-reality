function openModalLikes(postId) {
    const modal = document.getElementById('post-modal-likes');
    const likes_modal_body = document.querySelector(".likes-modal-body");


    const apiUrl = `${API_BASE_URL}getLikes&postId=${postId}`;

    modal.setAttribute('postId', postId);

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            likes_modal_body.innerHTML = "";

            data.forEach(user => {
                const userElement = document.createElement("div");
                userElement.classList.add("like-item");
                userElement.innerHTML = `
                    <div class="likes-modal-profile">
                        <div class="likes-modal-profile-img">
                            <a href="${MY_URL}profile/${user.username}">
                                <img src="${PROFILE_IMG_PATH}${user.profile_picture}" alt="Image">
                            </a>
                        </div>
                        <div class="likes-modal-profile-info">
                            <a class="nav-link" href="${MY_URL}profile/${user.username}">
                                <span class="user-name">${user.username}</span>
                            </a>
                            <span class="user-full-name">${user.firstname} ${user.lastname} </span>
                        </div>
                        <button class="follow-btn">Follow</button>
                    </div>
                    `;
                likes_modal_body.appendChild(userElement);
            });

        })
        .catch(error => console.error("Error fetching likes:", error));
    modal.style.display = 'flex';
    escapeModalLikes();
    document.addEventListener('click', closeModalLikesOnClickOutside);
}


function closeModalLikesOnClickOutside(event) {
    const modal = document.querySelector('.likes-modal-content');

    if (!modal.contains(event.target) && !event.target.matches(".like-text")) {
        document.removeEventListener('click', closeModalLikesOnClickOutside);
        closeModalLikes();
    }
}

function likesSearch() {

    const searchBar = document.querySelector('#likes-search-container input');
    const likes_modal_body = document.querySelector(".likes-modal-body");
    const modal = document.getElementById('post-modal-likes');
    const postId = modal.getAttribute('postId');

    apiUrl = `${API_BASE_URL}searchInLikes&postId=${postId}&searchContent=${searchBar.value}`;

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            likes_modal_body.innerHTML = "";

            data.forEach(user => {
                const userElement = document.createElement("div");
                userElement.classList.add("like-item");
                userElement.innerHTML = `
                    <div class="likes-modal-profile">
                        <div class="likes-modal-profile-img">
                            <a href="${MY_URL}profile/${user.username}">
                                <img src="${PROFILE_IMG_PATH}${user.profile_picture}" alt="Image">
                            </a>
                        </div>
                        <div class="likes-modal-profile-info">
                            <a class="nav-link" href="${MY_URL}profile/${user.username}">
                                <span class="user-name">${user.username}</span>
                            </a>
                            <span class="user-full-name">${user.firstname} ${user.lastname} </span>
                        </div>
                        <button class="follow-btn">Follow</button>
                    </div>
                    `;
                likes_modal_body.appendChild(userElement);
            });

        })
        .catch(error => console.error("Error fetching likes:", error));
}


function escapeModalLikes() {
    addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModalLikes();
        }
    });
}

function closeModalLikes() {
    const searchBar = document.querySelector('.search-container input');
    searchBar.value = '';
    const modal_likes = document.getElementById('post-modal-likes');
    modal_likes.style.display = 'none';
}