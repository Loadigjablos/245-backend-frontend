const etageNumber = document.querySelector("#etage-number");
const etageUp = document.querySelector("#etage-up");
const etageDown = document.querySelector("#etage-down");

let etage = 0;

etageDown.addEventListener("click", function (e) {
  if (!(etage < -99)) {
    etage -= 1;
  }
  placeEtageNumber();
  RenderAll();
});
etageUp.addEventListener("click", function (e) {
  if (!(etage > 99)) {
    etage += 1;
  }
  placeEtageNumber();
  RenderAll();
});
function placeEtageNumber() {
  etageNumber.innerHTML = etage;
  if (etage === 0) {
    etageNumber.innerHTML = "EG";
  }
}

// THIS IS TO DISPLAY ALL THE ROOMS AND PARKINGSPACES

const tabelPlaces = document.querySelector("#tabel-places");

const canvas = document.querySelector("#canvas");
const ctx = canvas.getContext("2d");

const CANVAS_WIDTH = (canvas.width = 900);
const CANVAS_HEIGHT = (canvas.height = 900);

function RenderAll() {
  ctx.clearRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);
  tabelPlaces.innerHTML = `
    <tr>
        <th>Name</th>
        <th>Type</th>
        <th>Löschen</th>
        <th>Bearbeiten</th>
    </tr>
  `

  data.forEach((Element, index) => {
    if(Element == undefined) {
        MessageUI("Error", "Data Is Corupted");
    } else if(Element.position == undefined) {
        MessageUI("Error", "Data Is Incomplete and unable to be displayed");
    } else {
        let type = "";
            if ((Element.type == "r")) {
                type = "Raum";
            } else if ((Element.type == "p")) {
                type = "Parkplatz";
            } else {
                type = "UnIdentified Thing";
            }
        if (JSON.parse(Element.position).etage == etage) {
            ctx.fillStyle = "#000000";
    
            ctx.fillRect(
                JSON.parse(Element.position).x,
                JSON.parse(Element.position).y,
                JSON.parse(Element.position).width,
                JSON.parse(Element.position).height
            );
    
            // WHITE
            ctx.fillStyle = "#FFFFFF";
    
            ctx.fillText(
                index + ": " + Element.name + ", " + type,
                JSON.parse(Element.position).x +
                JSON.parse(Element.position).width / 2 -
                (index + " :" + Element.name + ", " + type).length * 2,
                JSON.parse(Element.position).y + JSON.parse(Element.position).height / 2 - 10
            );
        }
        tabelReservations.innerHTML += `
                  <tr>
                      <td>${Element.name}</td>
                      <td>${type}</td>
                      <td><button onclick="placeDelete('${Element.name}')">Löschen</button></td>
                      <td><a href="place-edit.html#${Element.name}">Reservieren</a></td>
                  </tr>
                  `;
    }
    });
}

/**
 *
 * @param {*} name
 */
function placeDelete(name) {
  /**
   * here will be the validation of the result
   * @returns if the server didn't responde corectly
   */
  const onRequstUpdate = function () {
    if (request.readyState < 4) {
      return;
    }
    console.log(request.responseText);
    const response = JSON.parse(request.responseText);
    if (
      request.status == 401 ||
      request.status == 404 ||
      request.status == 403
    ) {
      MessageUI("Error", "Daten konnten nicht gelöscht werden: " + response);
    }
  };

  var request = new XMLHttpRequest();
  request.open("GET", "../../API/V1/Reservation/" + name);
  request.onreadystatechange = onRequstUpdate;
  request.send();
}

function request() {
  /**
   * here will be the validation of the result
   * @returns if the server didn't responde corectly
   */
  const onRequstUpdate = function () {
    if (request.readyState < 4) {
      return;
    }
    data = JSON.parse(request.responseText);
    if (
      request.status == 401 ||
      request.status == 404 ||
      request.status == 403
    ) {
      MessageUI("Error", "Daten Konnten Nicht Geholt werden");
    }
    RenderAll();
  };

  let request = new XMLHttpRequest();
  request.open("GET", "../../../../API/V1/Places");
  request.onreadystatechange = onRequstUpdate;
  request.send();
}

request();

let data = [];

// This function makes that every 30 Seconds New Data Will get requested
setInterval(request, 30000);
