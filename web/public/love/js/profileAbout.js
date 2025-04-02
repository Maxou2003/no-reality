function changeActiveAboutNav(event) {
    const active = document.querySelector('.about-nav .active');
    if (active != event.target) {
        const activeAboutContent = document.querySelector(`.${event.target.id}`);
        const previousAvoutContent = document.querySelector(`.${active.id}`);
        activeAboutContent.style.display = 'block';
        previousAvoutContent.style.display = 'none';
        event.target.classList.add("active");
        active.classList.remove('active');
    }

}