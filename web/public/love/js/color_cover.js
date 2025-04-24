document.addEventListener("DOMContentLoaded", function () {
    const div = document.getElementById("cover");
    const img = document.querySelector('#cover .img-cover');

    if (img.complete) {
        const colorThief = new ColorThief();
        const palette = colorThief.getPalette(img, 5);

        div.style.backgroundImage = `linear-gradient(to bottom, rgb(${palette[1]}), white )`;
    }
});
