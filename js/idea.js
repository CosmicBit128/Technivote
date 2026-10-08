import {createIdea, logout} from "./api.js";

window.onload = () => {
    const accountToggle = document.getElementById("accountToggle");
    const accountMenu = document.getElementById("accountMenu");
    const ideaTextarea = document.getElementById("ideaTextarea");
    const submitButton = document.getElementById("submitButton");

    accountToggle.addEventListener("click", () => {
        accountToggle.ariaExpanded = String(!accountToggle.ariaExpanded == "true");
        accountMenu.classList.toggle("open");
    });

    document.getElementById("backButton").addEventListener("click", () => {
        const homepage = new URL("/cosmic/technivote/", location.origin).href;
        const referrer = new URL(document.referrer || "/", location.origin).href;

        if (referrer.pathname === new URL(homepage).pathname) {
            history.back();
        } else {
            location.href = homepage;
        }
    });

    submitButton.addEventListener("click", () => {
        const idea = ideaTextarea.value;
        if (idea.trim() === "") {
            alert("wpisz cos");
        }

        createIdea(idea).then((res) => {
            if (res.status === "empty") {
                alert("wpisz cos");
            } else if (res.status === "login") {
                location.href = "login";
            } else if (res.status === "yay") {
                location.href = ".";
            }
        });
    });

    document.getElementById("logout").addEventListener("click", () => {
        logout().then((res) => {
            if (res.status === "yay") {
                location.href = "login";
            } else if (res.status === "login") {
                alert("musisz byc zalogowany zeby sie wylogowac!! duh");
            }
        });
    });
}