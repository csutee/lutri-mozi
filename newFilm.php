<?php

    session_start();

    if(isset($_SESSION["isAdmin"])) {
        if($_SESSION["isAdmin"] == false) {
            header("Location: index.php");
        }
    }
    else {
        header("Location: index.php");
    }

    $room = "1. terem";

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = $_POST["name"] ?? "";
        $description = $_POST["description"];
        $url = $_POST["url"];
        $date = $_POST["date"] ?? "";
        $room = $_POST["room"] ?? "";

        $ticketCount = $_POST["ticketCount"] ?? "";
        $ticketPrice = $_POST["ticketPrice"] ?? "";
        $errors = [];
        $exploded;
        $time;
        

        

        if($title == "") {
            $errors["title"] = "A cím megadása kötelező!";
        }
        if($date == "") {
            $errors["date"] = "A dátum megadása kötelező!";
        }
        else {
            $exploded = explode("T", $date);
            $date = $exploded[0];
            $time = $exploded[1];
        }
        if($room == "") {
            $errors["room"] = "A terem megadása nem helyes!";
        }
        if($ticketCount == "") {
            $errors["ticketCount"] = "A jegyszám megadása kötelező!";
        }
        else {
            if(!filter_var($ticketCount, FILTER_VALIDATE_INT)) {
                $errors["ticketCount"] = "A jegyszám formátuma nem helyes!";
            }
            else {
                if(!(intval($ticketCount) > 0)) {
                    $errors["ticketCount"] = "A jegyszám pozitív egész szám kell, hogy legyen!";
                }
            }
        }
        if($ticketPrice == "") {
            $errors["ticketPrice"] = "A jegyár megadása kötelező!";
        }
        else {
            if(!filter_var($ticketPrice, FILTER_VALIDATE_INT)) {
                $errors["ticketPrice"] = "A jegyár formátuma nem helyes!";
            }
            else {
                if(!(intval($ticketPrice) > 0)) {
                    $errors["ticketPrice"] = "A jegyár pozitív egész szám kell, hogy legyen!";
                }
            }
        }

        if(count($errors) == 0) {
            $films = json_decode(file_get_contents("Assets/Datas/films.json"),true);

            $maxId = $films["films"][0]["id"];

            for($i = 1; $i<count($films["films"]); $i++) {
                if($films["films"][$i]["id"] > $maxId) {
                    $maxId = $films["films"][$i]["id"];
                }
            }

            $tmp = array();
            $tmp["id"] = $maxId + 1;
            $tmp["date"] = $date;
            $tmp["time"] = $time;
            $tmp["name"] = $title;
            $tmp["room"] = $room;
            $tmp["maxSeat"] = $ticketCount;
            $tmp["freeSeats"] = $ticketCount;
            $tmp["description"] = $description;
            $tmp["optionalLink"] = $url;
            $tmp["price"] = $ticketPrice;

            array_push($films["films"], $tmp);

            file_put_contents("Assets/Datas/films.json",json_encode($films, JSON_PRETTY_PRINT));

            header("Location: index.php");
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

            <h1 style="text-align: center">Új film felvitele</h1>

            <form action="newFilm.php" method="POST" novalidate>
                <div class="form-group">
                    <label for="name">Cím:</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Cím" value="<?= $title ?? ""?>"> <?= $errors["title"] ?? ""?>
                </div>

                <div class="form-group">
                    <label for="description">Leírás:</label>
                    <input type="text" class="form-control" name="description" id="description" placeholder="Leírás" value="<?= $description ?? ""?>"> <?= $errors["description"] ?? ""?>
                </div>

                <div class="form-group">
                    <label for="url">URL:</label>
                    <input type="text" class="form-control" name="url" id="url" placeholder="URL" value="<?= $url ?? ""?>"> <?= $errors["url"] ?? ""?>
                </div>

                <div class="form-group">
                    <label for="date">Időpont:</label>
                    <input type="datetime-local" class="form-control" id="date" name="date" value="<?= $date ?? ""?>"> <?= $errors["date"] ?? ""?>
                </div>

                <div class="form-group">
                    <label for="room">Terem:</label>
                    <select class="form-control" id="room" name="room"> <?= $errors["room"] ?? ""?>
                        <option <?= $room == "1. terem" ? 'selected' : ''?>>1. terem</option>
                        <option <?= $room == "2. terem" ? 'selected' : ''?>>2. terem</option>
                        <option <?= $room == "3. terem" ? 'selected' : ''?>>3. terem</option>
                        <option <?= $room == "4. terem" ? 'selected' : ''?>>4. terem</option>
                        <option <?= $room == "5. terem" ? 'selected' : ''?>>5. terem</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="ticketCount">Jegyek száma:</label>
                    <input type="number" class="form-control" name="ticketCount" id="ticketCount" placeholder="Jegyek száma" value="<?= $ticketCount ?? ""?>"> <?= $errors["ticketCount"] ?? ""?>
                </div>

                <div class="form-group">
                    <label for="ticketPrice">Jegyár:</label>
                    <input type="number" class="form-control" name="ticketPrice" id="ticketPrice" placeholder="Jegyár" value="<?= $ticketPrice ?? ""?>"> <?= $errors["ticketPrice"] ?? ""?>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success" style="margin: auto; display: block">Mentés</button>
                </div>

            </form>

        </div>
    </div>

</body>

</html>