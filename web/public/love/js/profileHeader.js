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

console.log("Current location: ", current_location);
if (current_location === "about") {
    aboutLink.classList.add("active");
} else if (current_location === "friends") {
    friendsLink.classList.add("active");
} else if (current_location === "photos") {
    picturesLink.classList.add("active");
} else {
    publicationsLink.classList.add("active");
}
