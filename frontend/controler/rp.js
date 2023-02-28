
const etageNumber = document.querySelector("#etage-number");
const etageUp = document.querySelector("#etage-up");
const etageDown = document.querySelector("#etage-down");

let etage = 0;

etageDown.addEventListener("click", function(e) {
    if (!(etage < -99)) {
        etage -= 1
    }
    placeEtageNumber();
    RenderAll();
});
etageUp.addEventListener("click", function(e) {
    if (!(etage > 99)) {
        etage += 1;
    }
    placeEtageNumber();
    RenderAll();
});
function placeEtageNumber() {
    etageNumber.innerHTML = etage;
    if(etage === 0) {
        etageNumber.innerHTML = "EG";
    }
}

// THIS IS TO DISPLAY ALL THE ROOMS AND PARKINGSPACES

const tabelReservations = document.querySelector("#tabel-reservations");

const canvas = document.querySelector("#canvas");
const ctx = canvas.getContext("2d");

const CANVAS_WIDTH = canvas.width = 900;
const CANVAS_HEIGHT = canvas.height = 900;

function RenderAll() {
    tabelReservations.innerHTML = `
    <tr>
        <th>Name</th>
        <th>Besetzt?</th>
        <th>Beschreibung</th>
        <th>Löschen</th>
        <th>Bearbeiten</th>
        <th>Reservieren</th>
    </tr>
    `;
    ctx.clearRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);

    data.forEach((Element, index) => {
        if (Element.position.etage == etage) {
            ctx.fillStyle = "#000000";
            if (Element.bocked == null) {
                ctx.fillStyle = "#00AF00";
            } else {
                ctx.fillStyle = "#AF0000";
            }
            ctx.fillRect(Element.position.x, Element.position.y, Element.position.width, Element.position.height);
            
            // WHITE
            ctx.fillStyle = "#FFFFFF";

            let type = "";
            if (Element.typeThing = "r") {
                type = "Raum";
            } else if(Element.typeThing = "p") {
                type = "Parkplatz";
            }
            ctx.fillText(index + ": " + Element.name + ", " + type, Element.position.x + (Element.position.width / 2) - ((index + " :" + Element.name + ", " + type).length * 2), Element.position.y + (Element.position.height / 2) - 10);
            if (Element.bocked != null) {
                ctx.fillText("Host: " + Element.bocked.host, Element.position.x + (Element.position.width / 2) - (("Host: " + Element.bocked.host).length * 2), Element.position.y + (Element.position.height / 2));
                ctx.fillText("From: " + Element.bocked.from, Element.position.x + (Element.position.width / 2) - (("From: " + Element.bocked.from).length * 2), Element.position.y + (Element.position.height / 2) + 10);
                ctx.fillText("To:" + Element.bocked.to, Element.position.x + (Element.position.width / 2) - (("To:" + Element.bocked.to).length * 2), Element.position.y + (Element.position.height / 2) + 20);
            }
        }
        if (Element.bocked == null) {
            var bocked = "nicht besetzt";
            tabelReservations.innerHTML += `
            <tr>
                <td>${Element.name}</td>
                <td>${bocked}</td>
                <td>${Element.description}</td>
                <td>-</td>
                <td>-</td>
                <td><a href="reservation.html#${Element.name}">Reservieren</a></td>
            </tr>
            `;
        } else {
            var bocked = "besetzt";
            tabelReservations.innerHTML += `
            <tr>
                <td>${Element.name}</td>
                <td>${bocked}</td>
                <td>${Element.description}</td>
                <td><button onclick="reservationDelete('${Element.name}')">Löschen</button></td>
                <td><a href="reservation-edit.html#${Element.name}">Editieren</a></td>
                <td>-</td>
            </tr>
            `;
        }
    });

}

/**
 * 
 * @param {*} name 
 */
function reservationDelete(name) {
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

    var request = new XMLHttpRequest();
    request.open("GET", "../../API/V1/Reservation/" + name);
    request.onreadystatechange = onRequstUpdate;
    request.send();
}

let data = [
    {
        name: "thing",
        position: {
            width: 200,
            height: 100,
            x: 20,
            y: 20,
            etage: 0,
        },
        typeThing: "p",
        description: "wertzui87654 567897654345678987 6t54rertzu8i90",
        bocked: {
            host: "james",
            from: "20.02.2022 20:30",
            to: "20.02.2022 21:00",
        }
    },
    {
        name: "hallo",
        position: {
            width: 600,
            height: 400,
            x: 20,
            y: 420,
            etage: 0,
        },
        description: "wertzui87 654567897654345678987 6t54rertzu8i90",
        typeThing: "p",
        bocked: null,
    },
    {
        name: "thing",
        position: {
            width: 200,
            height: 200,
            x: 230,
            y: 20,
            etage: 0,
        },
        description: "wertzui876 '-_'_-' 876t54rertzu8i90",
        typeThing: "r",
        bocked: {
            host: "jeffry",
            from: "20.02.2022 20:30",
            to: "20.02.2022 21:00",
        }
    },
];

RenderAll();
// This function makes that every 30 Seconds The Whole screen will get renderd
setInterval(RenderAll, 30000);