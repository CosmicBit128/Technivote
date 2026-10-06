window.addEventListener("load", () => {
    const loginInput = document.getElementById("login");
    const passwordInput = document.getElementById("pass");
    const error = document.getElementById("error");

    document.getElementById("submitButton").addEventListener("click", () => {
        // fetch("https://adegdansk.pl/cosmic/technivote/api/login.php", {
        fetch("http://localhost/api/login.php", {
            method: "POST",
            credentials: "include",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
                login: loginInput.value,
                pass: passwordInput.value
            }),
        }).then((value) => {
            value.json().then((res)=> {
                console.log(res);
                if (res.status === "yay") {
                    location.href = "/";
                    console.log("redirect");
                } else if (res.status === "wrong" || res.status === "error") {
                    error.textContent = res.error;
                    error.classList.add("open");
                }
            })
        });
    });
});