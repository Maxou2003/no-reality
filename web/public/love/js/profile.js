var splitted_location = window.location.href.split("/").filter((val) => val != "");
var current_location = splitted_location[splitted_location.length - 1];

var publicationsLink = document.getElementById("publications");
var aboutLink = document.getElementById("about");
var friendsLink = document.getElementById("friends");
var picturesLink = document.getElementById("pictures");

publicationsLink.classList.remove("active");
aboutLink.classList.remove("active");
friendsLink.classList.remove("active");
picturesLink.classList.remove("active");

if (current_location === "publications") {
    publicationsLink.classList.add("active");
} else if (current_location === "about") {
    aboutLink.classList.add("active");
} else if (current_location === "friends") {
    friendsLink.classList.add("active");
} else if (current_location === "pictures") {
    picturesLink.classList.add("active");
}else {
    publicationsLink.classList.add("active");
}
