document.addEventListener("heartIconsUpdated", function () {
    document.querySelectorAll('.heart_icon').forEach(function (iconButton) {
        iconButton.onclick = function () {
            const heartIcon = iconButton.querySelector('ion-icon');
            const isLiked = heartIcon.name === 'heart';
            heartIcon.name = isLiked ? 'heart-outline' : 'heart';
            iconButton.classList.toggle("heart_icon_fill", !isLiked);
        };
    });
});
