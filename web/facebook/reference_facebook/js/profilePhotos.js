function changeActivePhotosNav(event) {
    const active = document.querySelector('.photos-nav .active');
    if (active != event.target) {
        const userId = document.querySelector('.profile_container').getAttribute('data-user-id');
        fetchPhotos(event.target.id, userId);
        event.target.classList.add("active");
        active.classList.remove('active');
    }
}

function fetchPhotos(filter, userId) {
    fetch(`${API_BASE_URL}getPhotos&userId=${userId}&filter=${filter}`)
        .then(response => response.json())
        .then(photos => renderPhotos(photos))
        .catch(error => console.error('Error fetching photos:', error));
}

function renderPhotos(photos) {
    const container = document.querySelector('.photos_box');
    const splitted_location = window.location.href.split("/").filter((val) => val != "");
    const current_location = splitted_location[splitted_location.length - 1];

    if (!photos || !photos.length) {
        container.innerHTML = '<p>No photos found</p>';
        return;
    }
    if (current_location != "photos") {
        photos = photos.filter((photo, index) => index < 6);
    }

    container.innerHTML = photos.map(photo => `
         <div class="photos-item-container">
            <div class='photo-picture'>
                <img src="${POST_IMG_PATH}${photo}" alt=""> 
            </div>
        </div> 
    `).join('');
}