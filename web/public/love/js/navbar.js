var splitted_location = window.location.href.split("/").filter((val) => val != "");
var current_location = splitted_location[splitted_location.length - 1];

if (current_location == "" || current_location == "home") {
    document.getElementById("home").classList.add("active");
    document.getElementById("groups").classList.remove("active");
}
if (current_location == "groups") {
    document.getElementById("groups").classList.add("active");
    document.getElementById("home").classList.remove("active");
}
