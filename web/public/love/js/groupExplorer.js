document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('explorer_search_input');
    let searchTimeout;

    searchInput.addEventListener('input', function (e) {
        clearTimeout(searchTimeout);

        if (e.target.value.trim().length > 0) {
            searchTimeout = setTimeout(() => {
                console.log("search val", e.target.value.trim());
                updateSearchResults(e.target.value.trim());
            }, 300);
        }
    });

    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && searchInput.value.trim().length > 0) {
            e.preventDefault();
            const searchContent = encodeURIComponent(searchInput.value.trim());
            window.location.href = `${MY_URL}SearchResults/groups/${searchContent}`;
        }
    });

    async function updateSearchResults(query) {
        try {
            document.querySelector('.group-explorer-search-results').innerHTML = '<div class="loading">Loading groups...</div>';

            const groupsResponse = await fetch(`${API_BASE_URL}searchGroups&searchContent=${encodeURIComponent(query)}`);

            const groups = await groupsResponse.json();

            renderGroups(groups);
        } catch (error) {
            console.error('Search error:', error);
            document.getElementById('groupsResults').innerHTML = '<p class="error">Failed to load groups</p>';
        }
    }

    function renderGroups(groups) {
        const container = document.querySelector('.group-explorer-search-results');


        if (!groups || groups.length === 0) {
            container.innerHTML = '<p class="no-results">No groups found</p>';
            return;
        }

        container.innerHTML = groups.filter((group, index) => index < 5).map(group => `
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
                window.location.href = `${MY_URL}SearchResults/groups/${encodeURIComponent(item.dataset.groupName)}`;
            });
        });
    }
});