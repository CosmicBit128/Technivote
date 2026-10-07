window.addEventListener("load", () => {
    const loginInput = document.getElementById("login");
    const passwordInput = document.getElementById("pass");
    const error = document.getElementById("error");

    document.getElementById("submitButton").addEventListener("click", (e) => {
        e.preventDefault();

        fetch("./api.php", {
            method: "POST",
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                login: loginInput.value,
                pass: passwordInput.value
            }),
        })
            .then((value) => value.json())
            .then((res) => {
                console.log(res);
                if (res.status === "login" || res.status === "yay") {
                    location.href = "/";
                } else if (res.status === "wrong" || res.status === "error") {
                    error.textContent = res.error;
                    error.classList.add("open");
                }
            });
    });
});