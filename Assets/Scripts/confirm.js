let confirmButton = document.querySelector("#confirmButton");

confirmButton.addEventListener("click", saveDatas);

function saveDatas(e) {
    let urlParams = new URLSearchParams(window.location.search);
    let filmId = urlParams.get("id");
    let name = urlParams.get("name");
    let ticketNumber = urlParams.get("ticketNumber");
    //console.log(filmId + " " + name + " " + ticketNumber);
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200 && this.responseText == "success") {
            document.querySelector(".center").innerHTML = "";
            let p = document.createElement("p");
            p.style.textAlign = "center";
            p.innerHTML = "Sikeres jegyvásárlás! Köszönjük, hogy minket választott.";
            let a = document.createElement("a");
            a.href = "index.php";
            a.style.textAlign = "center";
            a.style.marginleft = "auto";
            a.style.marginRight = "auto";
            a.style.display = "block";
            a.innerHTML = "Vissza a főoldalra!";

            document.body.appendChild(p);
            document.body.appendChild(a);

        }
    };
    xhttp.open("POST", "saveOrder.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("id=" + filmId + "&name=" + name + "&ticketNumber=" + ticketNumber);
}