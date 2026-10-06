import {
    getIdeas
} from "./api.js";

window.onload = () => {
    const list = document.getElementById("ideaList");
    const loginWrapper = document.getElementById("loginWrapper");

    function tryVote(id, voteYes) {
        console.debug("Voted " + (voteYes?"yes":"no") + " for id " + id);
        // if (!currentUser) {
            loginWrapper.classList.add("open");
            return;
        // }
    }

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

            const voteYes = document.createElement('button');
            voteYes.classList.add("voteYes", "btn");
            voteYes.textContent = idea.votes_yes;
            voteYes.addEventListener("click", () => { tryVote(idea.id, true); });
            const voteNo = document.createElement('button');
            voteNo.classList.add("voteNo", "btn");
            voteNo.textContent = idea.votes_no;
            voteNo.addEventListener("click", () => { tryVote(idea.id, false); });
            
            ideaVotes.appendChild(voteYes);
            ideaVotes.appendChild(voteNo);
            ideaEl.appendChild(ideaText);
            ideaEl.appendChild(ideaVotes);
            list.appendChild(ideaEl);
        });
    });
}