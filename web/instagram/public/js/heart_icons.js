const heartButtons = document.querySelectorAll('.heart_icon');


heartButtons.forEach(function (iconButton) {
    iconButton.onclick = function () {
        const isLiked = iconButton.childNodes[1].name === 'heart';
        iconButton.childNodes[1].name = isLiked ? 'heart-outline' : 'heart';
        iconButton.className = isLiked ? 'heart_icon' : 'heart_icon heart_icon_fill';
    }
});
