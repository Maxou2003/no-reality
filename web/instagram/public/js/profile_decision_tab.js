function switchActiveBtn(btnTag) {
    const activeDiv = btnTag === 'post' ? document.querySelector(".post-choice") : document.querySelector(".identification-choice");
    const unactiveDiv = !(btnTag === 'post') ? document.querySelector(".post-choice") : document.querySelector(".identification-choice");

    if (!activeDiv.classList.contains("active")) {
        activeDiv.classList.toggle("active");
        unactiveDiv.classList.remove("active");
    }
}