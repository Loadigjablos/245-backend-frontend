const loginButton = document.querySelector("#login");

loginButton.addEventListener("click", function(e) {
    if(document.cookie !== " ") {
        window.location.href = "/frontend/view/login.html";
    } else {
        document.cookie = " ";
    }
});

/**
 * here will be the validation of the result
 * @returns if the server didn't responde corectly
 */
const onRequstUpdate = function() {
    if (request.readyState < 4) {
        return;
    }
    console.log(request.status + " " + request.statusText);
    const response = JSON.parse(request.responseText);
    console.log(response);
}

let request = new XMLHttpRequest();
request.open("GET", "../../../../API/V1/WhoAmI");
request.onreadystatechange = onRequstUpdate;
request.send();
