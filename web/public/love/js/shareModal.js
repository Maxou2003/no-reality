function showSharesModal(postId) {
    const modal = document.getElementById('shareModal');
    const modalBody = document.getElementById('shareModalBody');

    document.body.classList.add('body-no-scroll');
    // Show loading state
    modalBody.innerHTML = '<p>Loading...</p>';
    modal.style.display = 'block';

    // Fetch shared users for this post
    fetch(`${API_BASE_URL}getShares&postId=${postId}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                let html = '';
                data.forEach(user => {
                    html += `
                        <div class="share-user">
                            <a href="${MY_URL}profile/${user.user_slug}">
                                <img src="${PROFILE_IMG_PATH}${user.user_pp_path}" class="share-user-avatar" alt="${user.user_firstname}">
                                <div class="share-user-name">
                                    ${user.user_firstname} ${user.user_lastname}
                                </div>
                            </a>
                            <button class="add-friend-btn">
                                <i class="fas fa-user-plus"></i> Add to friends
                            </button>
                        </div>
                    `;
                });
                modalBody.innerHTML = html;
            } else {
                modalBody.innerHTML = '<p>No shares yet</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = '<p>Error loading shares</p>';
        });
}

// Close modal when clicking X
document.querySelector('.close-share-modal').addEventListener('click', function () {
    document.getElementById('shareModal').style.display = 'none';
    document.body.classList.remove('body-no-scroll');
});

// Close modal when clicking outside
window.addEventListener('click', function (event) {
    const modal = document.getElementById('shareModal');
    if (event.target == modal) {
        document.body.classList.remove('body-no-scroll');
        modal.style.display = 'none';
    }
});