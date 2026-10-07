import {castVote, getIdeas, logout} from "./api.js";

window.onload = () => {
    const list = document.getElementById("ideaList");
    const loginWrapper = document.getElementById("loginWrapper");
    const accountToggle = document.getElementById("accountToggle");
    const accountMenu = document.getElementById("accountMenu");

    accountToggle.addEventListener("click", () => {
        accountToggle.ariaExpanded = String(!accountToggle.ariaExpanded == "true");
        accountMenu.classList.toggle("open");
    });

    document.getElementById("logout").addEventListener("click", () => {
        logout().then((res) => {
            if (res.status === "yay") {
                location.href = "/login";
            } else if (res.status === "login") {
                alert("musisz byc zalogowany zeby sie wylogowac!! duh");
            }
        });
    });

    if (typeof currentUser !== "undefined") {
        document.getElementById('nav-right').classList.add('logged-in');
    }

    /**
     * Attempts to cast a vote
     *
     * @param {Number} id Idea ID
     * @param {Boolean} voteYes Whether the user voted for or against
     * @param {HTMLElement} yesButton Yes button element
     * @param {HTMLElement} noButton No button element
     */
    function tryVote(id, voteYes, yesButton, noButton) {
        if (typeof currentUser === "undefined") {
            loginWrapper.classList.add("open");
            return;
        }
        castVote(id, voteYes).then((res) => {
            if (res.status === "error") {
                console.error("Couldn't cast vote:", res.error);
            } else if (res.status === "login") {
                location.href = "/login";
            } else if (res.status === "voted") {
                alert("You already voted for this!");
            } else if (res.status === "no_exist") {
                alert("cosmicbit cos skopal\nalbo hakujesz strone");
            } else if (res.status === "yay") {
                if (voteYes) {
                    yesButton.classList.add("voted");
                    yesButton.disabled = true;
                } else {
                    noButton.classList.add("voted");
                    noButton.disabled = true;
                }
            }
        });
    }

    document.getElementById("logInButton").addEventListener("click", () => {
        location.href = "/login";
    });
    document.getElementById("loginDisclaimerClose").addEventListener("click", () => {
        loginWrapper.classList.remove("open");
    });

    getIdeas().then((ideas) => {
        console.log(ideas);
        ideas.forEach((idea) => {
            const ideaEl = document.createElement('div');
            ideaEl.classList.add("idea", "glass");
            const ideaText = document.createElement('p');
            ideaText.classList.add("ideaText");
            ideaText.textContent = idea.text;
            const ideaVotes = document.createElement('div');
            ideaVotes.classList.add("ideaVotes");

            const voted = idea.user_vote != null;
            const voteYes = document.createElement('button');
            voteYes.classList.add("voteYes", "btn");
            if (voted) {
                if (idea.user_vote) voteYes.classList.add("voted");
                voteYes.disabled = true;
            }
            voteYes.textContent = idea.votes_yes;
            const voteNo = document.createElement('button');
            voteNo.classList.add("voteNo", "btn");
            if (voted) {
                if (!idea.user_vote) voteNo.classList.add("voted");
                voteNo.disabled = true;
            }
            voteNo.textContent = idea.votes_no;

            voteYes.addEventListener("click", () => { tryVote(idea.id, true, voteYes, voteNo); });
            voteNo.addEventListener("click", () => { tryVote(idea.id, false, voteYes, voteNo); });
            
            ideaVotes.appendChild(voteYes);
            ideaVotes.appendChild(voteNo);
            ideaEl.appendChild(ideaText);
            ideaEl.appendChild(ideaVotes);
            list.appendChild(ideaEl);
        });
    });
}