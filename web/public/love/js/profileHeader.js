const splitted_location = window.location.href.split("/").filter((val) => val != "");
const current_location = splitted_location[splitted_location.length - 1];

const publicationsLink = document.getElementById("publications");
const aboutLink = document.getElementById("about");
const friendsLink = document.getElementById("friends");
const picturesLink = document.getElementById("pictures");

publicationsLink.classList.remove("active");
aboutLink.classList.remove("active");
friendsLink.classList.remove("active");
picturesLink.classList.remove("active");

if (current_location === "about") {
    aboutLink.classList.add("active");
} else if (current_location === "friends") {
    friendsLink.classList.add("active");
} else if (current_location === "photos") {
    picturesLink.classList.add("active");
} else {
    publicationsLink.classList.add("active");
}
