// JavaScript to handle search toggle
function toggleSearch() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('search-active');

    // Close search when clicking outside (optional)
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