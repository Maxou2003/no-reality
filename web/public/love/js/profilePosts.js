function changeActivePhotosNav(event) {
    // to Change
    const active = document.querySelector('.friends-nav .active');
    if (active != event.target) {
        const userId = document.querySelector('.profile_container').getAttribute('data-user-id');
        fetchFriends(event.target.id, '', userId);
        event.target.classList.add("active");
        active.classList.remove('active');
    }
}