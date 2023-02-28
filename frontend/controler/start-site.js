const tabelReservations = document.querySelector("#tabel-reservations");


let data = [];


data.forEach((Element) => {
    
    if (Element.bocked !== null) {
        tabelReservations.innerHTML += `
        <tr>
            <td>${Element.name}</td>
            <td>${Element.bocked.from + " Bis " + Element.bocked.to}</td>
        </tr>
        `;
    }
});
