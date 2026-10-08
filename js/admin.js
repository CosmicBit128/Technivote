import {getUnapproved, review, logout} from "./api.js";

window.onload = () => {
    const list = document.getElementById("ideaList");
    const accountToggle = document.getElementById("accountToggle");
    const accountMenu = document.getElementById("accountMenu");

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

    document.getElementById("logout").addEventListener("click", () => {
        logout().then((res) => {
            if (res.status === "yay") {
                location.href = "login";
            } else if (res.status === "login") {
                alert("musisz byc zalogowany zeby sie wylogowac!! duh");
            }
        });
    });

    const check = document.createElement("img");
    check.classList.add("svg-icon");
    check.src = "res/approve.svg";
    check.alt = "Approve";
    const cross = document.createElement("img");
    cross.src = "res/discard.svg";
    cross.alt = "Discard";
    getUnapproved().then((res) => {
        if (res.status === "admin") {
            location.href = "login";
        } else if (res.status === "yay") {
            res.ideas.forEach((idea) => {
                const ideaEl = document.createElement('div');
                ideaEl.classList.add("idea", "glass");
                const ideaText = document.createElement('p');
                ideaText.classList.add("ideaText");
                ideaText.textContent = idea.text;
                const ideaVotes = document.createElement('div');
                ideaVotes.classList.add("ideaVotes");

                const approve = document.createElement('button');
                approve.classList.add("voteYes", "btn");
                approve.appendChild(check);
                const discard = document.createElement('button');
                discard.classList.add("voteNo", "btn");
                discard.appendChild(cross);

                approve.addEventListener("click", () => {
                    review(idea.id, true);
                    list.removeChild(ideaEl);
                });
                discard.addEventListener("click", () => {
                    review(idea.id, false);
                    list.removeChild(ideaEl);
                });

                ideaVotes.appendChild(approve);
                ideaVotes.appendChild(discard);
                ideaEl.appendChild(ideaText);
                ideaEl.appendChild(ideaVotes);
                list.appendChild(ideaEl);
            });
        }
    });
}