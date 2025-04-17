document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('.search_group_members input');
    const usersResults = document.querySelector('.members');
    const originalMembersHTML = usersResults.innerHTML;
    let searchTimeout;

    // Function to display search results
    function displaySearchResults(results) {
        if (results.length === 0) {
            usersResults.innerHTML = '<div class="no-results">No members found</div>';
            return;
        }
        const groupSlug = searchInput.getAttribute("data-group-slug");

        usersResults.innerHTML = results.map(member => {

            let locationInfo = '';
            if (member.users.user_location) {
                locationInfo = member.users.user_location;
            } else if (member.users.user_school) {
                locationInfo = member.users.user_school;
            }

            return `
                  <div class="member-list-item">
                    <div class="member-item-left">                        
                        <a class="user-page-link" href="${MY_URL}groups/${groupSlug}/user/${member.users.user_slug}"><img src="${PROFILE_IMG_PATH}${member.users.user_pp_path}" alt="Post_picture"></a>
                        <div class="member-data">
                            <a class="user-page-link" href="${MY_URL}groups/${groupSlug}/user/${member.users.user_slug}"><span class="member-names">${member.users.user_firstname} ${member.users.user_lastname}</span></a>
                            <span class="member-since">Member since ${member.timestamp}</span>
                            <span class="member-more">
                                ${locationInfo ? `${locationInfo}` : ''}
                            </span>
                        </div>
                    </div>
                    <div class="member-item-right">
                        <button class="add-button"><ion-icon name="person-add"></ion-icon> Add as friend</button>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Function to search group members
    function searchGroupMembers(searchTerm) {
        if (searchTerm.length < 2) {
            usersResults.innerHTML = originalMembersHTML;
            return;
        }

        const groupId = searchInput.getAttribute("data-group-id");

        // Show loading state
        usersResults.innerHTML = '<div class="loading">Searching members...</div>';

        fetch(`${API_BASE_URL}searchInGroupMembers&groupId=${groupId}&searchContent=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                displaySearchResults(data);
            })
            .catch(error => {
                console.error('Error searching members:', error);
                usersResults.innerHTML = '<div class="error">Error searching members</div>';
            });
    }


    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const searchTerm = this.value.trim();

        if (searchTerm) {
            searchTimeout = setTimeout(() => {
                searchGroupMembers(searchTerm);
            }, 300);
        } else {
            usersResults.innerHTML = originalMembersHTML;
        }
    });

});