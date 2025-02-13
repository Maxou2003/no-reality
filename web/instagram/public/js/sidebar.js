
function toggleSearch() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('search-active');

    if (sidebar.classList.contains('search-active')) {
        document.addEventListener('click', closeSearchOnClickOutside);
    } else {
        document.removeEventListener('click', closeSearchOnClickOutside);
    }
}

function closeSearchOnClickOutside(event) {
    const sidebar = document.querySelector('.sidebar');
    const isClickInside = sidebar.contains(event.target);

    if (!isClickInside) {
        sidebar.classList.remove('search-active');
        document.removeEventListener('click', closeSearchOnClickOutside);
    }
}

function sidebarSearch() {
    const searchBar = document.querySelector('#sidebar-search-container input');
    const searchResults = document.querySelector('.search-results');

    let apiUrl = `${API_BASE_URL}searchInUsers&searchContent=${searchBar.value}`;

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            searchResults.innerHTML = "";

            data.forEach(user => {
                const userElement = document.createElement("div");
                userElement.classList.add("follower-item");
                userElement.innerHTML = `
                <div class="profile">
                    <div class="profile-img">
                        <img src="${PROFILE_IMG_PATH}${user.profile_picture}" alt="User Avatar">
                    </div>
                    <div class="profile-info">
                        <a class="nav-link" href="${MY_URL}profile/${user.username}">
                            <span class="user-name">${user.username}</span>
                        </a>
                        <span class="user-full-name">${user.firstname} ${user.lastname} </span>
                    </div>
                    <button class="follow-btn">Follow</button>
                </div>`;
                searchResults.appendChild(userElement);
            });

        })
        .catch(error => console.error("Error fetching followers:", error));
}

function toggleSidebar() {
    const sidebar = document.querySelector(".sidebar");
    const mainContent = document.querySelector(".main-content");
    sidebar.classList.toggle("active");
    mainContent.classList.toggle("active");
}