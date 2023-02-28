const tabelReservations = document.querySelector("#tabel-reservations");



let data = [];


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
