const loginButton = document.querySelector("#login");

loginButton.addEventListener("click", function(e) {
    if(document.cookie !== " ") {
        window.location = "frontend/view/login.html";
    } else {
        document.cookie = " ";
    }
});