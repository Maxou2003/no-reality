function explorerSearch() {
    const searchBar = document.querySelector('.explore-container #explorer-search-container input');
    const searchResults = document.querySelector('.explore-container .search-results');

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

function toggleExplorerSearch() {
    const searchSection = document.querySelector('.explore-container');
    if (!searchSection.classList.contains('explorer-search-active')) {
        searchSection.classList.toggle('explorer-search-active');
    }
}

function exitExplorerSearch() {
    const searchSection = document.querySelector('.explore-container');
    const searchBar = document.querySelector('.explore-container #explorer-search-container input');
    searchSection.classList.remove('explorer-search-active');
    searchBar.value = "";
}