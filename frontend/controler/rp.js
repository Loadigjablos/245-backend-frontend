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

  /**
   * This Algorith is rendering all Places and all the Reservations that ar Set to this place
   */
  dataPlaces.forEach((ElementPlace, index) => {
    if(ElementPlace == undefined) {
      MessageUI("Error", "Places Data Is Corupted");
    } else {
      let AllReservationsFromThisPlace = [];

      // Checks if there is a Reservation for this Place
      dataReservated.forEach((ElementReservation) => {
        if(ElementReservation == undefined) {
          MessageUI("Error", "Reservation Data Is Corupted");
        } else if(ElementReservation.from_date == undefined || ElementReservation.to_date == undefined || ElementReservation.host == undefined) {
          MessageUI("Error", "Data Is Incomplete");
        } else {
          // Reservations For this place are added to array to list them later up
          if (ElementReservation.place_name == ElementPlace.name) {
            const reservation = {
              from: ElementReservation.from_date,
              to: ElementReservation.to_date,
              host: ElementReservation.host,
              description: ElementReservation.description
            }
            AllReservationsFromThisPlace.push(reservation);
          }
        }
      });

      const x = parseInt(JSON.parse(ElementPlace.position).x);
      const y = parseInt(JSON.parse(ElementPlace.position).y);
      const width = parseInt(JSON.parse(ElementPlace.position).width);
      const height = parseInt(JSON.parse(ElementPlace.position).height);

      ctx.fillStyle = "#000000";
      if (AllReservationsFromThisPlace == null) {
        ctx.fillStyle = "#00AF00";
      } else {
        ctx.fillStyle = "#AF0000";
      }

      ctx.fillRect(x, y, width, height);

      let type = "";
      if ((Element.type == "r")) {
        type = "Raum";
      } else if ((Element.type == "p")) {
        type = "Parkplatz";
      } else {
        type = "UnIdentified Thing";
      }
      /*
            ctx.fillText(
              index + ": " + Element.name + ", " + type,
              x + (width / 2) - (index + " :" + Element.name + ", " + type).length * 2,
                y + (height / 2) - 10
            );

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
    */
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
    if (requestPlace.readyState < 4) {
      return;
    }
    const response = JSON.parse(requestPlace.responseText);
    if (
      requestPlace.status == 401 ||
      requestPlace.status == 404 ||
      requestPlace.status == 403
    ) {
      MessageUI("Error", "Daten konnten nicht gelöscht werden: " + response);
    }
  };

  var requestPlace = new XMLHttpRequestPlace();
  requestPlace.open("DELETE", "../../API/V1/Reservation/" + name);
  requestPlace.onreadystatechange = onRequstUpdate;
  requestPlace.send();
}

function requestPlace() {
  /**
   * here will be the validation of the result
   * @returns if the server didn't responde corectly
   */
  const onRequstUpdatePlaces = function () {
    if (requestPlace.readyState < 4) {
      return;
    }
    if (
      requestPlace.status == 400 ||
      requestPlace.status == 401 ||
      requestPlace.status == 404 ||
      requestPlace.status == 403
    ) {
      MessageUI("Error", "Daten Konnten Nicht Geholt werden");
    }
    dataPlaces = JSON.parse(requestPlace.responseText);
  };

  /**
   * here will be the validation of the result
   * @returns if the server didn't responde corectly
   */
  const onRequstUpdateReservations = function () {
    if (requestReservation.readyState < 4) {
      return;
    }
    if (
      requestReservation.status == 400 ||
      requestReservation.status == 401 ||
      requestReservation.status == 404 ||
      requestReservation.status == 403
    ) {
      MessageUI("Error", "Daten Konnten Nicht Geholt werden");
    }
    dataPlaces = JSON.parse(requestReservation.responseText);
  };

  let requestPlace = new XMLHttpRequest();
  requestPlace.open("GET", "../../../../API/V1/Places");
  requestPlace.onreadystatechange = onRequstUpdatePlaces;
  requestPlace.send();

  let requestReservation = new XMLHttpRequest();
  requestReservation.open("GET", "../../../../API/V1/Reservations");
  requestReservation.onreadystatechange = onRequstUpdateReservations;
  requestReservation.send();
}

requestPlace();

let dataPlaces = []; // Alle The Places that can be Reserved
let dataReservated = []; // All Reservations That Are Set.

// These functions are played every 30 Seconds
setInterval(requestPlace, 30000);
setInterval(RenderAll, 30000);
