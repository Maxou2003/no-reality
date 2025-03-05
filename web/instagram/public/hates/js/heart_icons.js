document.addEventListener("click", function (event) {
    const iconButton = event.target.closest(".heart_icon");
    if (!iconButton) return;

    const heartIcon = iconButton.querySelector("ion-icon");
    const isLiked = heartIcon.name === "heart";
    heartIcon.name = isLiked ? "heart-outline" : "heart";
    iconButton.classList.toggle("heart_icon_fill", !isLiked);
});
