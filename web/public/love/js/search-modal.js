document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('.search_box input');
    const searchModal = document.getElementById('searchModal');
    let searchTimeout;

    searchInput.addEventListener('input', function (e) {
        clearTimeout(searchTimeout);

        if (e.target.value.trim().length > 0) {
            searchModal.style.display = 'flex';

            searchTimeout = setTimeout(() => {
                updateSearchResults(e.target.value.trim());
            }, 300);
        } else {
            searchModal.style.display = 'none';
        }
    });

    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && searchModal.style.display === 'flex' && searchInput.value.trim().length > 0) {
            e.preventDefault();
            const searchContent = encodeURIComponent(searchInput.value.trim());
            window.location.href = `${MY_URL}SearchResults/all/${searchContent}`;
        }
    });

    window.addEventListener('click', function (e) {
        if (e.target != searchModal) {
            searchModal.style.display = 'none';
        }
    });

    async function updateSearchResults(query) {
        try {
            document.getElementById('groupsResults').innerHTML = '<div class="loading">Loading groups...</div>';
            document.getElementById('usersResults').innerHTML = '<div class="loading">Loading people...</div>';

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

        container.innerHTML = groups.filter((group, index) => index < 3).map(group => `
            <div class="result-item" data-group-name="${group.group_name}">
                <div class="result-item-avatar">    
                    <i class="fas fa-search"></i>
                </div>
                <div class="result-item-info">
                    <div class="result-item-name">${group.group_name}</div>
                    <div class="result-item-meta">Group · ${group.nb_members} member(s)</div>
                </div>
            </div>
        `).join('');

        container.querySelectorAll('.result-item').forEach(item => {
            item.addEventListener('click', () => {
                window.location.href = `${MY_URL}SearchResults/all/${encodeURIComponent(item.dataset.groupName)}`;
            });
        });
    }

    function renderUsers(users) {
        const container = document.getElementById('usersResults');

        if (!users || users.length === 0) {
            container.innerHTML = '<p class="no-results">No people found</p>';
            return;
        }

        container.innerHTML = users.filter((user, index) => index < 3).map(user => `
            <div class="result-item" data-user-firstname="${user.user_firstname}">
                <div class="result-item-avatar">
                    <i class="fas fa-search"></i>
                </div>
                <div class="result-item-info">
                    <div class="result-item-name">${user.user_firstname} ${user.user_lastname}</div>
                    <div class="result-item-meta">Persons</div>
                </div>
            </div>
        `).join('');

        container.querySelectorAll('.result-item').forEach(item => {
            item.addEventListener('click', () => {
                window.location.href = `${MY_URL}SearchResults/all/${encodeURIComponent(item.dataset.userFirstname)}`;
            });
        });
    }
});