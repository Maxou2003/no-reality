
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
                userElement.classList.add("user-item");
                userElement.innerHTML = `
                <div class="profile">
                    <div class="profile-img">
                        <a href="${MY_URL}profile/${user.username}"><img src="${PROFILE_IMG_PATH}${user.profile_picture}" alt="User Avatar"></a>
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
        .catch(error => console.error("Error fetching users:", error));
}

function toggleSidebar() {
    const sidebar = document.querySelector(".sidebar");
    const mainContent = document.querySelector(".main-content");
    sidebar.classList.toggle("active");
    mainContent.classList.toggle("active");
}

function navItemManagement() {
    const explorer = document.querySelector('.explorer-icon');

    if (window.innerWidth <= 768) {
        if (explorer.classList.contains('fa-compass')) {
            explorer.classList.remove('fa-compass');
            explorer.classList.add('fa-search');
        }
    } else {
        if (explorer.classList.contains('fa-search')) {
            explorer.classList.remove('fa-search');
            explorer.classList.add('fa-compass');
        }
    }
}

navItemManagement();
window.addEventListener('resize', () => {
    navItemManagement();
});
