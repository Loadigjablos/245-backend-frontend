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

const tabelReservations = document.querySelector("#tabel-reservations");

const canvas = document.querySelector("#canvas");
const ctx = canvas.getContext("2d");

const CANVAS_WIDTH = (canvas.width = 900);
const CANVAS_HEIGHT = (canvas.height = 900);

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
    if(Element == undefined) {
        MessageUI("Error", "Data Is Corupted");
    } else if(Element.position == undefined || Element.data_time == undefined) {
        MessageUI("Error", "Data Is Incomplete and unable to be displayed");
    } else {
        if (JSON.parse(Element.position).etage == etage) {
            ctx.fillStyle = "#000000";
            if (Element.bocked == null) {
              ctx.fillStyle = "#00AF00";
            } else {
              ctx.fillStyle = "#AF0000";
            }
            ctx.fillRect(
              JSON.parse(Element.position).x,
              JSON.parse(Element.position).y,
              JSON.parse(Element.position).width,
              JSON.parse(Element.position).height
            );
      
            // WHITE
            ctx.fillStyle = "#FFFFFF";
      
            let type = "";
            if ((Element.type == "r")) {
              type = "Raum";
            } else if ((Element.type == "p")) {
              type = "Parkplatz";
            } else {
              type = "UnIdentified Thing";
            }
            ctx.fillText(
              index + ": " + Element.place_name + ", " + type,
              JSON.parse(Element.position).x +
              JSON.parse(Element.position).width / 2 -
                (index + " :" + Element.place_name + ", " + type).length * 2,
                JSON.parse(Element.position).y + JSON.parse(Element.position).height / 2 - 10
            );
            if (Element.data_time != null) {
              ctx.fillText(
                "Host: " + JSON.parse(Element.data_time).host,
                JSON.parse(Element.position).x +
                JSON.parse(Element.position).width / 2 -
                  ("Host: " + JSON.parse(Element.data_time).host).length * 2,
                  JSON.parse(Element.position).y + JSON.parse(Element.position).height / 2
              );
              ctx.fillText(
                "From: " + JSON.parse(Element.data_time).from,
                JSON.parse(Element.position).x +
                JSON.parse(Element.position).width / 2 -
                  ("From: " + JSON.parse(Element.data_time).from).length * 2,
                  JSON.parse(Element.position).y + JSON.parse(Element.position).height / 2 + 10
              );
              ctx.fillText(
                "To:" + JSON.parse(Element.data_time).to,
                JSON.parse(Element.position).x +
                JSON.parse(Element.position).width / 2 -
                  ("To:" + JSON.parse(Element.data_time).to).length * 2,
                  JSON.parse(Element.position).y + JSON.parse(Element.position).height / 2 + 20
              );
            }
          }
          if (Element.data_time == null) {
            var bocked = "nicht besetzt";
            tabelReservations.innerHTML += `
                  <tr>
                      <td>${Element.place_name}</td>
                      <td>${bocked}</td>
                      <td>${Element.description}</td>
                      <td>-</td>
                      <td>-</td>
                      <td><a href="reservation.html#${Element.place_name}">Reservieren</a></td>
                  </tr>
                  `;
          } else {
            var bocked = "besetzt";
            tabelReservations.innerHTML += `
                  <tr>
                      <td>${Element.place_name}</td>
                      <td>${bocked}</td>
                      <td>${Element.description}</td>
                      <td><button onclick="reservationDelete('${Element.place_name}')">Löschen</button></td>
                      <td><a href="reservation-edit.html#${Element.place_name}">Editieren</a></td>
                      <td>-</td>
                  </tr>
                  `;
          }
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
  const onRequstUpdate = function () {
    if (request.readyState < 4) {
      return;
    }
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
  request.open("GET", "../../../../API/V1/Reservations");
  request.onreadystatechange = onRequstUpdate;
  request.send();
}

request();

let data = [];

// This function makes that every 30 Seconds New Data Will get requested
setInterval(request, 30000);
