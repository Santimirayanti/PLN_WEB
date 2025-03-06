document.addEventListener("DOMContentLoaded", function () {
    if (!localStorage.getItem("isLoggedIn")) {
        window.location.href = "login.html";
    }
});
