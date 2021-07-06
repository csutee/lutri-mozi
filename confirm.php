<?php

    session_start();

    $selectedFilm = $_GET["id"] ?? "";
    $films = json_decode(file_get_contents("Assets/Datas/films.json"),true);
    $films = $films["films"];
    $name = $_GET["name"];
    $email = $_GET["email"];
    $ticketNumber = $_GET["ticketNumber"];
    $finalPrice;
    for($i = 0; $i < count($films); $i++) {
        if($films[$i]["id"] == $selectedFilm) {
            $finalPrice = intval($ticketNumber) * intval($films[$i]["price"]);
            break;
        }
    }

    function getPurchaseDetails() {
        global $films, $selectedFilm, $name, $email, $ticketNumber, $finalPrice;
        
        echo "<p style='text-align: center'>Név: {$name}</p>";
        echo "<p style='text-align: center'>Email: {$email}</p>";
        echo "<p style='text-align: center'>Jegyek száma: {$ticketNumber} db</p>";
        echo "<p style='text-align: center'>Fizetendő összeg: {$finalPrice} FT</p>";
    }
    

    function getFilmDetails() {
        global $films, $selectedFilm;
        for($i = 0; $i < count($films); $i++) {
            if($films[$i]["id"] == $selectedFilm) {
                echo "<p style='text-align: center'>Film cime: {$films[$i]['name']}</p>";
                $description = $films[$i]["description"] ?? "Nem elérhető leírás hozzá.";
                echo "<p style='text-align: center'>Leírás: {$description}</p>";
                $portLink = $films[$i]["optionalLink"] ?? "Nem található!";
                echo "<p style='text-align: center'>Port.hu: {$portLink}</p>";
                echo "<ul>Vetítés adatai:";
                echo "<li>Dátum: {$films[$i]['date']}</li>";
                echo "<li>Időpont: {$films[$i]['time']}</li>";
                echo "<li>Terem: {$films[$i]['room']}</li>";
                echo "</ul>";

                echo "<p style='text-align: center'>Jegyek száma(Szabad/Összes): {$films[$i]['freeSeats']}/{$films[$i]['maxSeat']}</p>";

                echo "<p style='text-align: center'>Ár: {$films[$i]['price']} FT</p>";
            }
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
    
    <title>LutriMozi - Jegyvásárlás</title>
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

        <div class="center">

            <h1 style="text-align: center">Jegyvásárlás</h1>
            <?= getFilmDetails() ?>
            <h1 style="text-align: center">Vevő adatai</h1>
            <?= getPurchaseDetails() ?>
            <button class="btn btn-success" id="confirmButton" style="margin-left: auto; margin-right: auto; display: block">Jóváhagyás</button>
        </div>
    </div>

</body>

<script src="Assets/Scripts/confirm.js">
</script>

</html>