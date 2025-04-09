var splitted_location = window.location.href.split("/").filter((val) => val != "");
var current_location = splitted_location[splitted_location.length - 1];

console.log(current_location);

var discussionsLink = document.getElementById("discussions");
var announcementsLink = document.getElementById("announcements");
var membersLink = document.getElementById("members");
var mediaLink = document.getElementById("media");

discussionsLink.classList.remove("active");
announcementsLink.classList.remove("active");
membersLink.classList.remove("active");
mediaLink.classList.remove("active");

console.log("Current location: ", current_location);
if (current_location === "announcements") {
    announcementsLink.classList.add("active");
} else if (current_location === "members") {
    membersLink.classList.add("active");
} else if (current_location === "media") {
    mediaLink.classList.add("active");
} else {
    discussionsLink.classList.add("active");
}
