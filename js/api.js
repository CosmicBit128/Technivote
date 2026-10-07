// const ROOT = "https://adegdansk.pl/cosmic/technivote/api.php";
const ROOT = "http://localhost/api.php";

/**
 * Fetches JSON from an URL
 *
 * @param {String} baseUrl Request URL
 * @returns {Promise<Object | Array>} Javascript Object
 */
async function fetch_json(baseUrl) {
    const cacheBust = `_ts=${Date.now()}`;
    const url = baseUrl.includes("?")
        ? `${baseUrl}&${cacheBust}`
        : `${baseUrl}?${cacheBust}`;

    const res = await fetch(url, {
        method: "GET",
        mode: "cors",
        cache: "no-store",
        credentials: "include",
        headers: {
            Accept: "application/json",
        },
    });

    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    const data = await res.json();
    if (data.code === "error") throw new Error(data.reason || "Unknown API error");
    return data;
}

export async function getIdeas() {
    return await fetch_json(`${ROOT}?get_ideas`);
}

export async function castVote(id, voteYes) {
    return await fetch_json(`${ROOT}?cast_vote=${id}&yes=${voteYes?1:0}`);
}

export async function logout() {
    return await fetch_json(`${ROOT}?logout`);
}

export async function createIdea(idea) {
    return await fetch_json(`${ROOT}?create_idea=${encodeURIComponent(idea)}`)
}

export async function getUnapproved() {
    return await fetch_json(`${ROOT}?get_unapproved`);
}

export async function review(id, approved) {
    return fetch_json(`${ROOT}?review=${id}&res=${approved?"approved":"discarded"}`);
}