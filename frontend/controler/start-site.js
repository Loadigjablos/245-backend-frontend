const tabelReservations = document.querySelector("#tabel-reservations");

let data = [];

function request() {
    /**
     * here will be the validation of the result
     * @returns if the server didn't responde corectly
     */
    const onRequstUpdate = function() {
        if (request.readyState < 4) {
            return;
        }
        if (request.status == 401 || request.status == 404 || request.status == 403) {
            MessageUI("Error", "Daten Konnten Nicht Geholt werden");
        }
        data = JSON.parse(request.responseText);
    }

    let request = new XMLHttpRequest();
    request.open("GET", "../../../../API/V1/Reservations");
    request.onreadystatechange = onRequstUpdate;
    request.send();
}

request();

data.forEach((Element) => {
    
    if (Element.bocked !== null) {
        if (Element.typeThing == "r") {
            tabelReservations.innerHTML += `
            <tr>
                <td>${"Raum: " + Element.name}</td>
                <td>${Element.bocked.from + " Bis " + Element.bocked.to}</td>
            </tr>
            `;
        } else if(Element.typeThing == "p") {
            tabelReservations.innerHTML += `
            <tr>
                <td>${"Parkplatz: " + Element.name}</td>
                <td>${Element.bocked.from + " Bis " + Element.bocked.to}</td>
            </tr>
            `;
        }
    }
});
