const heartButtons = document.querySelectorAll('.heart_icon');


heartButtons.forEach(function (iconButton) {
    iconButton.onclick = function () {
        const heartIcon = iconButton.querySelector('ion-icon');
        const isLiked = heartIcon.name === 'heart';
        heartIcon.name = isLiked ? 'heart-outline' : 'heart';
        iconButton.className = isLiked ? 'heart_icon' : 'heart_icon heart_icon_fill';
    }
});
