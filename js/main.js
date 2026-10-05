import {
    getIdeas
} from "./api.js";

window.onload = () => {
    const list = document.getElementById("ideaList");

    getIdeas().then((ideas) => {
        console.log(ideas);
        ideas.forEach((idea) => {
            list.innerText += idea.id + ": " + idea.idea + "; " + idea.votes_yes + " za, " + idea.votes_no + " przeciw";
        });
    });
}