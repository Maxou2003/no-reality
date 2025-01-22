
document.addEventListener("DOMContentLoaded", () => {
    const suggestions = document.querySelector(".suggestions");

    // Exemple de suggestions dynamiques
    const users = ["Utilisateur 4", "Utilisateur 5", "Utilisateur 6"];
    users.forEach(user => {
        const userDiv = document.createElement("div");
        userDiv.className = "user";
        userDiv.textContent = user;
        suggestions.appendChild(userDiv);
    });

    // Exemple de chargement de posts dynamiques
    // const feed = document.querySelector(".feed");
    // const posts = ["Post 4", "Post 5", "Post 6"];
    // posts.forEach(post => {
    //     const postDiv = document.createElement("div");
    //     postDiv.className = "post";
    //     postDiv.textContent = post;
    //     feed.appendChild(postDiv);
    // });
});
