document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('.search_box input');
    const searchModal = document.getElementById('searchModal');
    let searchTimeout;

    // Show/hide modal based on input
    searchInput.addEventListener('input', function (e) {
        clearTimeout(searchTimeout);

        if (e.target.value.trim().length > 0) {
            searchModal.style.display = 'flex';

            // Debounce the API calls (300ms delay)
            searchTimeout = setTimeout(() => {
                updateSearchResults(e.target.value.trim());
            }, 300);
        } else {
            searchModal.style.display = 'none';
        }
    });

    // Close modal when clicking outside
    window.addEventListener('click', function (e) {
        if (e.target === searchModal) {
            searchModal.style.display = 'none';
        }
    });

    async function updateSearchResults(query) {
        try {
            // Show loading state
            document.getElementById('groupsResults').innerHTML = '<div class="loading">Loading groups...</div>';
            document.getElementById('usersResults').innerHTML = '<div class="loading">Loading people...</div>';

            // Fetch groups and users in parallel
            const [groupsResponse, usersResponse] = await Promise.all([
                fetch(`${API_BASE_URL}searchGroups&searchContent=${encodeURIComponent(query)}`),
                fetch(`${API_BASE_URL}searchUsers&searchContent=${encodeURIComponent(query)}`)
            ]);

            const groups = await groupsResponse.json();
            const users = await usersResponse.json();

            renderGroups(groups);
            renderUsers(users);
        } catch (error) {
            console.error('Search error:', error);
            document.getElementById('groupsResults').innerHTML = '<p class="error">Failed to load groups</p>';
            document.getElementById('usersResults').innerHTML = '<p class="error">Failed to load people</p>';
        }
    }

    function renderGroups(groups) {
        const container = document.getElementById('groupsResults');

        if (!groups || groups.length === 0) {
            container.innerHTML = '<p class="no-results">No groups found</p>';
            return;
        }

        container.innerHTML = groups.map(group => `
            <div class="result-item" data-group-slug="${group.group_slug}">
                <div class="result-item-avatar">    
                <i class="fas fa-users"></i>
                </div>
                <div class="result-item-info">
                    <div class="result-item-name">${group.group_name}</div>
                    <div class="result-item-meta">${group.nb_members} members</div>
                </div>
            </div>
        `).join('');

        container.querySelectorAll('.result-item').forEach(item => {
            item.addEventListener('click', () => {
                window.location.href = `${MY_URL}groups/${item.dataset.groupSlug}`;
            });
        });
    }

    function renderUsers(users) {
        const container = document.getElementById('usersResults');

        if (!users || users.length === 0) {
            container.innerHTML = '<p class="no-results">No people found</p>';
            return;
        }

        container.innerHTML = users.map(user => `
            <div class="result-item" data-user-slug="${user.user_slug}">
                <div class="result-item-avatar">
                    ${user.user_pp_path ?
                `<img src="${PROFILE_IMG_PATH}${user.user_pp_path}" alt="${user.user_firstname} ${user.user_lastname}">` :
                `<i class="fas fa-user"></i>`}
                </div>
                <div class="result-item-info">
                    <div class="result-item-name">${user.user_firstname} ${user.user_lastname}</div>
                </div>
            </div>
        `).join('');

        container.querySelectorAll('.result-item').forEach(item => {
            item.addEventListener('click', () => {
                window.location.href = `${MY_URL}profile/${item.dataset.userSlug}`;
            });
        });
    }
});