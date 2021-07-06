let previousWeek = document.querySelector("#previousWeek");
let nextWeek = document.querySelector("#nextWeek");
let weekNumber = parseInt(document.querySelector("#weekNumber").innerHTML);

previousWeek.addEventListener("click", showPreviousWeek);
nextWeek.addEventListener("click", showNextWeek);

function showPreviousWeek(e) {
    e.preventDefault();
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("table").innerHTML = this.responseText;
            document.querySelector("#weekNumber").innerHTML = weekNumber;
        }
    };
    xhttp.open("GET", "listFilms.php?weekNumber=" + (--weekNumber), true);
    xhttp.send();
}

function showNextWeek(e) {
    e.preventDefault();
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("table").innerHTML = this.responseText;
            document.querySelector("#weekNumber").innerHTML = weekNumber;
        }
    };
    xhttp.open("GET", "listFilms.php?weekNumber=" + (++weekNumber), true);
    xhttp.send();
}

