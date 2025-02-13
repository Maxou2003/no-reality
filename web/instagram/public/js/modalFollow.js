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


function escapeModalFollow() {
    addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModalFollow();
        }
    });
}

function closeModalFollow() {
    const searchBar = document.querySelector('.search-container input');
    searchBar.value = '';
    const modal_follow = document.getElementById('post-modal-follow');
    modal_follow.style.display = 'none';
}