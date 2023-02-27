const loginButton = document.querySelector("#login");

loginButton.addEventListener("click", function(e) {
    if(document.cookie !== " ") {
        window.location = "frontend/view/login.html";
    } else {
        document.cookie = " ";
    }
});

var request = new XMLHttpRequest();
request.open("GET", "../../");
request.onreadystatechange = onRequstUpdate;
request.send();

/**
 * here will be the validation of the result
 * @returns if the server didn't responde corectly
 */
const onRequstUpdate = function() {
    if (request.readyState < 4) {
        return;
    }
    const response = JSON.parse(request.responseText);
    console.log(request.status + " " + request.statusText);
    console.log(response);
}