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
function openModalFollow() {
    const modal = document.getElementById('post-modal-follow');
    const follow_modal_body = document.querySelector(".follow-modal-body");
    const followersBtn = document.getElementById("follow-btn");
    const userId = followersBtn.getAttribute("data-user-id"); // Get user ID

    const apiUrl = '/no-reality/web/instagram/public/index.php?p=profile/getFollowers&user_id=';

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
                            <a class="nav-link" href="${MY_URL}profile/${user.user_id}">
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
    escapeModal();
}

function closeModal() {
    const heartButtons = document.querySelectorAll('.heart_icon');
    for (let i = 0; i < heartButtons.length; i++) {
        heartButtons[i].childNodes[1].name = 'heart-outline';
        heartButtons[i].className = 'heart_icon';
    }
    const modal = document.getElementById('post-modal');
    modal.style.display = 'none';
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


