function changeActiveFriendsNav(event) {
    const active = document.querySelector('.friends-nav .active');
    if (active != event.target) {
        const userId = document.querySelector('.profile_container').getAttribute('data-user-id');
        fetchFriends(event.target.id, '', userId);
        event.target.classList.add("active");
        active.classList.remove('active');
    }
}

function searchFriends(event) {
    if (event.target.value.length > 2) {
        const userId = document.querySelector('.profile_container').getAttribute('data-user-id');
        const activeFriendNav = document.querySelector('.friends-nav .active');
        fetchFriends(activeFriendNav.id, event.target.value, userId);
    }
}

async function fetchFriends(filter, search, userId) {
    try {
        const response = await fetch(`${API_BASE_URL}getFriends&userId=${userId}&filter=${filter}&search=${search}`);
        if (!response.ok) throw new Error('Network response was not ok');
        const friends = await response.json();
        renderFriends(friends);
    } catch (error) {
        console.error('Error fetching friends:', error);
    }
}

function renderFriends(friends) {
    const container = document.querySelector('.friends_box');
    const splitted_location = window.location.href.split("/").filter((val) => val != "");
    const current_location = splitted_location[splitted_location.length - 1];

    if (!friends || !friends.length) {
        container.innerHTML = '<p>No friends found</p>';
        return;
    }
    if (current_location != "friends") {
        friends = friends.filter((friend, index) => index < 6);
    }

    container.innerHTML = friends.map(friend => `
        <div class="friend-item-container">
            <a href="${MY_URL}/profile/${friend.user_slug}" class="friend-item">
                <div class='friend-profile_picture'>
                    <img src="${PROFILE_IMG_PATH}${friend.user_pp_path}" alt=""> 
                </div>
                <p>${friend.user_firstname} ${friend.user_lastname}</p>
            </a>
            <button class="add-friend-btn">Add friend</button>
        </div>
    `).join('');
}