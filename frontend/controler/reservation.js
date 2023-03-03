const dateFrom = document.querySelector("#date-from");
const timeFrom = document.querySelector("#time-from");
const dateTo = document.querySelector("#date-to");
const timeTo = document.querySelector("#time-to");

const place = document.querySelector("#place");
const description = document.querySelector("#description");

const confirmT = document.querySelector("#confirm");
const cancel = document.querySelector("#cancel");

const host = document.querySelector("#host");

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
        if (
            request.status == 400 ||
            request.status == 401 ||
            request.status == 404 ||
            request.status == 403
          ) {
            MessageUI("Error", "Daten Konnten Nicht Gespeichert werden oder Es Gibt keine: " + JSON.parse(request.responseText).error);
        } else {
            MessageUI("Success", "Succesfuly Created a new Reservation");
        }
    }
    var request = new XMLHttpRequest();
    request.open("POST", "../../../../API/V1/Login");
    request.onreadystatechange = onRequstUpdate;

    const requestArray = {
        from_date: dateFrom.value + " " + timeFrom.value + ":00",
        to_date: dateTo.value + " " + timeTo.value + ":00",
        place_name: place.value,
        description: description.value,
    };
    request.send(JSON.stringify(requestArray));
}

/**
 * "from_date": "2023-03-02 14:43:00",
 * "to_date": "2023-03-02 14:55:00",
 * "place_name": "Rubin",
 * "host": "mouayad",
 * "descriptopm": ""
 */

let userName;
let userType;

/**
 * here will be the validation of the result
 * @returns if the server didn't responde corectly
 */
const onRequstUpdateWhoami = function() {
    if (requestWhoami.readyState < 4) {
        return;
    }
    const response = JSON.parse(requestWhoami.responseText);
    userName = response.name;
    userType = response.type;

    if (userType == "A" || userType == "S") {

        const onRequstUpdateSelectThing = function() {
            if (requestSelectThing.readyState < 4) {
                return;
            }
            const retunedData = JSON.parse(requestSelectThing.responseText);

            const selectThing = document.createElement("select");

            retunedData.foreach(Element => {
                console.log(Element.name);
            });

            retunedData.foreach(Element => {
                const newSelectThing = document.createElement("option");

                newSelectThing.innerText = Element.name;
                newSelectThing.value = Element.name;

                selectThing.appendChild(newSelectThing);
            });

            host.appendChild(selectThing);
        }
        const requestSelectThing = new XMLHttpRequest();
        requestSelectThing.open("GET", "../../../../API/V1/Users");
        requestSelectThing.onreadystatechange = onRequstUpdateSelectThing;
        requestSelectThing.send();
    } else {
        host.innerText = userName;
    }
}

const requestWhoami = new XMLHttpRequest();
requestWhoami.open("GET", "../../../../API/V1/WhoAmI");
requestWhoami.onreadystatechange = onRequstUpdateWhoami;
requestWhoami.send();