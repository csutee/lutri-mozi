<?php
session_start();
    
$weekNumber = date('W');
echo "<span id='weekNumber' hidden>" . $weekNumber . "</span>";

$films = json_decode(file_get_contents("Assets/Datas/films.json"),true);
$films = $films["films"];
$dateNow = new DateTime();

function listFilms() {
    global $weekNumber, $films, $dateNow;

    $returnedValue = "<table class='center'>";
    $returnedValue = $returnedValue . "<tr>
                <th>Vetítés dátuma</th>
                <th>Film címe</th>
                <th>Terem</th>
                <th>Helyek</th>
                <th>Művelet</th>
            </tr>";

    for ($i=0; $i < count($films); $i++) {
        
        $date = new DateTime($films[$i]["date"] . " " . $films[$i]["time"]);
        $dateWeek = $date->format('W');

        if($dateWeek == $weekNumber) {
            
            //var_dump($dateNow);
            if($dateNow > $date) {
                //Expired
                
                $returnedValue = $returnedValue . "<tr style='background-color: grey'>";
                $returnedValue = $returnedValue . "<td>" . $films[$i]["date"] . " " . $films[$i]["time"] . "</td>";
                $returnedValue = $returnedValue . "<td>" . $films[$i]["name"] . "</td>";
                $returnedValue = $returnedValue . "<td>" . $films[$i]["room"] . "</td>";
                $returnedValue = $returnedValue . "<td>" . $films[$i]["freeSeats"] . "/" . $films[$i]["maxSeat"] . " szabad hely</td>";
                $returnedValue = $returnedValue . "<td class='not-available'>Lejárt!</td>";
                $returnedValue = $returnedValue . "</tr>";

                //var_dump("Expired");
                
            }
            else {
                $differential = $dateNow->diff($date);
                $minutes = $differential->days * 24 * 60;
                $minutes += $differential->h * 60;
                $minutes += $differential->i;
                $minutes = $minutes-120;
                if($minutes > 60) {
                    //Foglalhato
                    //var_dump($films[$i]["name"]);
                    if($films[$i]["freeSeats"] > 0) {
                        $returnedValue = $returnedValue . "<tr>";
                        $returnedValue = $returnedValue . "<td>" . $films[$i]["date"] . " " . $films[$i]["time"] . "</td>";
                        $returnedValue = $returnedValue . "<td>" . $films[$i]["name"] . "</td>";
                        $returnedValue = $returnedValue . "<td>" . $films[$i]["room"] . "</td>";
                        $returnedValue = $returnedValue . "<td>" . $films[$i]["freeSeats"] . "/" . $films[$i]["maxSeat"] . " szabad hely</td>";
                        $returnedValue = $returnedValue . "<td><a href='ticketPurchase.php?id={$films[$i]['id']}' class='btn btn-success'>Jegyvásárlás</a></td>";
                        $returnedValue = $returnedValue . "</tr>";
                    }
                    else {
                        $returnedValue = $returnedValue . "<tr style='background-color: grey'>";
                        $returnedValue = $returnedValue . "<td>" . $films[$i]["date"] . " " . $films[$i]["time"] . "</td>";
                        $returnedValue = $returnedValue . "<td>" . $films[$i]["name"] . "</td>";
                        $returnedValue = $returnedValue . "<td>" . $films[$i]["room"] . "</td>";
                        $returnedValue = $returnedValue . "<td>" . $films[$i]["freeSeats"] . "/" . $films[$i]["maxSeat"] . " szabad hely</td>";
                        $returnedValue = $returnedValue . "<td class='not-available'>Teltház!</td>";
                        $returnedValue = $returnedValue . "</tr>";
                    }
                }
                else {
                    $returnedValue = $returnedValue . "<tr style='background-color: grey'>";
                    $returnedValue = $returnedValue . "<td>" . $films[$i]["date"] . " " . $films[$i]["time"] . "</td>";
                    $returnedValue = $returnedValue . "<td>" . $films[$i]["name"] . "</td>";
                    $returnedValue = $returnedValue . "<td>" . $films[$i]["room"] . "</td>";
                    $returnedValue = $returnedValue . "<td>" . $films[$i]["freeSeats"] . "/" . $films[$i]["maxSeat"] . " szabad hely</td>";
                    $returnedValue = $returnedValue . "<td class='not-available'>Lejárt!</td>";
                    $returnedValue = $returnedValue . "</tr>";
                    //Nem foglalhato
                }
            }
        }
    }

    $returnedValue = $returnedValue . "</table>";
    echo $returnedValue;
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

            <a href="" style="text-align: center; display: block" id="previousWeek">Előző hét</a>
            <a href="" style="text-align: center; display: block" id="nextWeek">Következő hét</a>
            
        </div>
    </div>

</body>

<script src="Assets/Scripts/index.js">
</script>

</html>