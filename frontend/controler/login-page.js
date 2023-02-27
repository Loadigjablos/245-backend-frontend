
const loginForm = document.querySelector("#login-form");
const cancel = document.querySelector("#cancel");

const username = document.querySelector("#username");
const password = document.querySelector("#password");

loginForm.addEventListener("click", function(e) {
    requestPost();
});
cancel.addEventListener("click", function(e) {
    window.location = "../../index.html";
});


/**
 * this is the button to send data to the server
 */
function requestPost() {
    /**
     * here will be the validation of the result
     * @returns if the server didn't responde corectly
     */
    const onRequstUpdate = function() {
        if (request.readyState < 4) {
            return;
        }
        console.log(request.status + " " + request.statusText);
        console.log(JSON.parse(request.responseText));
    }
    var request = new XMLHttpRequest();
    request.open("POST", "../../");
    request.onreadystatechange = onRequstUpdate;
    const requestArray = {
        username: username.value,
        password: password.value,
    };
    request.send(JSON.stringify(requestArray));
}