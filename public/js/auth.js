function logout() {
    localStorage.removeItem("role");

    // Retour à la racine peu importe le dossier
    window.location.href = "../connexion.html";
    if (location.pathname.includes("connexion.html") === false &&
        location.pathname.split("/").length <= 2) {
        window.location.href = "connexion.html";
    }
}