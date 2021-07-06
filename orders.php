<?php
session_start();

$weekNumber = date('W');
//echo "<span id='weekNumber' hidden>" . $weekNumber . "</span>";

$films = json_decode(file_get_contents("Assets/Datas/films.json"),true);
$films = $films["films"];
$tickets = json_decode(file_get_contents("Assets/Datas/tickets.json"),true);
$tickets = $tickets["tickets"];
$dateNow = new DateTime();

function listFilms() {
    global $weekNumber, $films, $dateNow, $tickets;
    $haveFilm = false;

    for($i = 0; $i < count($tickets); $i++) {
        if($tickets[$i]["userId"] == $_SESSION["username"]) {
            $haveFilm = true;
        }
    }


    if($haveFilm) {
        $returnedValue = "<table class='center'>";
        $returnedValue = $returnedValue . "<tr>
                    <th>Vetítés dátuma</th>
                    <th>Film címe</th>
                    <th>Terem</th>
                    <th>Jegyek száma</th>
                    <th>Művelet</th>
                </tr>";

        for($i = 0; $i<count($tickets); $i++) {
            if($_SESSION["username"] == $tickets[$i]["userId"]) {
                        
                for($j = 0; $j<count($films); $j++) {
                    if($films[$j]["id"] == $tickets[$i]["filmId"]) {
                        $returnedValue = $returnedValue . "<tr>";
                        $returnedValue = $returnedValue . "<td>" . $films[$j]["date"] . " " . $films[$j]["time"] . "</td>";
                        $returnedValue = $returnedValue . "<td>" . $films[$j]["name"] . "</td>";
                        $returnedValue = $returnedValue . "<td>" . $films[$j]["room"] . "</td>";
                        $returnedValue = $returnedValue . "<td>" . $tickets[$i]["count"] . "</td>";
                        $filmDate = new DateTime($films[$j]["date"] . " " . $films[$j]["time"]);
                        $interval = $filmDate->diff($dateNow);
                        $hours = $interval->h;
                        $hours = $hours + ($interval->days * 24) - 1;
                        if($hours >= 24) {

                            $returnedValue = $returnedValue . "<td><button class='btn btn-success' onclick=cancelFilm(";
                            $returnedValue = $returnedValue . "'{$_SESSION['username']}'";
                            $returnedValue = $returnedValue . ",";
                            $returnedValue = $returnedValue . "{$tickets[$i]['filmId']},";
                            $returnedValue = $returnedValue . "{$tickets[$i]['count']}";
                            $returnedValue = $returnedValue . ")>";
                            $returnedValue = $returnedValue . "Lemondás</button></td>";
                            
                            
                        }
                        else {
                            $returnedValue = $returnedValue . "<td class='not-available'>Lemondás nem lehetséges!</td>";
                        }
                        $returnedValue = $returnedValue . "</tr>";
                    }
                }
            
            }
        }
            
        $returnedValue = $returnedValue . "</table>";
        echo $returnedValue;
    }
    else {
        echo "<h1 style='text-align: center'>Még nem vásároltál egy jegyet sem.</h1>";
    }
    

    
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="Assets/Stylesheets/style.css">
    
    <title>LutriMozi - Vetítések</title>
</head>
<body>
    
    <div class="header-wrapper">
        <div class="header-container">
            <div class="container-fluid header">
                <div class="row">
                    <h1 class="logo">LUTRI-Mozi</h1>
                </div>
            </div>
        </div>

        <div class="menu-bar">
            <div class="container-fluid">
                <nav role="navigation" class="navbar">
                    <div id="defaultmenu" class="navbar-collapse collapse no-transition">
                        <ul class="nav navbar-justified">
                            <li class="dropdown">
                                <a href="/" title="Főoldal">Főoldal</a>
                                <?php
                                    if(isset($_SESSION["loggedIn"])) {
                                        if(!$_SESSION["loggedIn"]) {
                                            echo "<a href='/register.php' title='Regisztráció'>Regisztráció</a>";
                                            echo "<a href='/login.php' title='Bejelentkezés'>Bejelentkezés</a>";
                                        }
                                    }
                                    else {
                                        echo "<a href='/register.php' title='Regisztráció'>Regisztráció</a>";
                                        echo "<a href='/login.php' title='Bejelentkezés'>Bejelentkezés</a>";
                                    }
                                ?>
                                
                                <?php
                                    if(isset($_SESSION["loggedIn"])) {
                                        if($_SESSION["loggedIn"]) {
                                            echo "<a title='{$_SESSION['username']}'>{$_SESSION['username']}</a>";
                                            echo "<a href='/orders.php' title='Korábbi rendeléseim'>Korábbi rendeléseim</a>";
                                            if(isset($_SESSION["isAdmin"])) {
                                                if($_SESSION["isAdmin"]) {
                                                    echo "<a href='/newFilm.php' title='Új vetítés felvitele'>Új vetítés felvitele</a>";
                                                }
                                            }
                                            echo "<a href='/logout.php' title='Kijelentkezés'>Kijelentkezés</a>";
                                        }
                                    }
                                ?>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>

        <div class="table-container">

            <?= listFilms(); ?>
            
        </div>
    </div>

</body>

<script>

    function cancelFilm(username, filmId, ticketCount) {
        if(confirm("Biztosan visszamondod a jegyedet?")) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200 && this.responseText == "success") {
                    alert("Sikeres jegy visszamondás!");
                    location.reload();
                }
            };
            xhttp.open("POST", "cancelEvent.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("id=" + filmId + "&username=" + username + "&ticketCount=" + ticketCount);
        }
    }

</script>

</html>