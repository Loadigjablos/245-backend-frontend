const dateFrom = document.querySelector("#date-from");
const dateTo = document.querySelector("#date-to");
const place = document.querySelector("#place");
const description = document.querySelector("#description");

const confirmT = document.querySelector("#confirm");
const cancel = document.querySelector("#cancel");





confirmT.addEventListener("click", function(e) {
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
        type.innerText = request.statusText
        message.innerText = request.responseText
    }
    var request = new XMLHttpRequest();
    request.open("POST", "../../../../API/V1/Login");
    request.onreadystatechange = onRequstUpdate;
    const requestArray = {
        from: dateFrom.value,
        to: dateTo.value,
        place: place.value,
        description: description.value,
    };
    request.send(JSON.stringify(requestArray));
}












