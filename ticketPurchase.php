<?php

    session_start();

    if(!isset($_SESSION["username"])) {
        $_SESSION["username"] = null;
    }
    if(!isset($_SESSION["email"])) {
        $_SESSION["email"] = null;
    }

    $selectedFilm = $_GET["id"] ?? "";
    $films = json_decode(file_get_contents("Assets/Datas/films.json"),true);
    $films = $films["films"];
    $name = "";
    $email = "";
    $ticketNumber;
    $checkbox = "off";
    $errors;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $ticketNumber = $_POST["ticketCount"] ?? 1;
        $checkbox = $_POST["checkbox"] ?? "off";
        $errors = [];

        //var_dump($name);
        //var_dump($email);
        //var_dump($ticketNumber);
        //var_dump($checkbox);

        if($name == "") {
            $errors["name"] = "Név megadása kötelező!";
        }

        if($email == "") {
            $errors["email"] = "E-mail megadása kötelező!";
        }
        else {
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors["email"] = "Hibás e-mail cím!";
            }
        }

        if(!filter_var($ticketNumber, FILTER_VALIDATE_INT)) {
            $errors["ticketCount"] = "A jegyek száma nem megfelelő!";
        }
        else {
            $maxCount = 0;
            for($i = 0; $i < count($films); $i++) {
                if($films[$i]["id"] == $selectedFilm) {
                    $maxCount = $films[$i]["maxSeat"];
                    break;
                }
            }

            if(intval($ticketNumber) < 1 || intval($ticketNumber) > $maxCount) {
                $errors["ticketCount"] = "A jegyek száma 1 és {$maxCount} között kell lennie!";
            }

        }

        if($checkbox == "off") {
            $errors["checkbox"] = "A vásárlási feltételek elfogadása kötelező!";
        }

        if(count($errors) == 0) {
            header("Location: confirm.php?id={$selectedFilm}&name={$name}&email={$email}&ticketNumber={$ticketNumber}");
        }

        //var_dump($errors);
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

            <form action="ticketPurchase.php?id=<?= $selectedFilm?>" method="POST" novalidate>
                <div class="form-group">
                    <label for="name">Név:</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Név" value="<?= ($_SESSION["username"] ?? $name) ?? ""?>"> <?= $errors["name"] ?? ""?>
                </div>
                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="E-mail" value="<?= ($_SESSION["email"] ?? $email) ?? ""?>"> <?= $errors["email"] ?? ""?>
                </div>

                <div class="form-group">
                    <label for="ticketCount">Jegyek száma:</label>
                    <input type="number" class="form-control" name="ticketCount" id="ticketCount" value="<?= $ticketNumber ?? ""?>" > <?= $errors["ticketCount"] ?? ""?>
                </div>

                <div class="form-group">
                    <input class="form-check-input" type="checkbox" name="checkbox" id="checkbox" <?php echo ($checkbox == "on" ? "checked" : ""); ?>>
                    <label class="form-check-label" for="checkbox">
                        Elfogadom a vásárlási feltételeket!
                    </label>
                    <?= $errors["checkbox"] ?? ""?>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">Küldés</button>
                </div>
            </form>

            <?php

                if(isset($_SESSION["isAdmin"])) {
                    if($_SESSION["isAdmin"]) {
                        echo "<h1 style='text-align: center'>Jelentkezett felhasználók</h1>";

                        $tickets = json_decode(file_get_contents("Assets/Datas/tickets.json"),true);
                        $tickets = $tickets["tickets"];

                        $counter = 0;
                        for($i=0; $i<count($tickets); $i++) {
                            if($tickets[$i]["filmId"] == $selectedFilm) {
                                $counter += 1;
                            }
                        }

                        if($counter == 0) {
                            echo "<p style='text-align: center; margin:auto; display: block'>Nem jelentkezett még senki</p>";
                        }
                        else {
                            $users = json_decode(file_get_contents("Assets/Datas/users.json"), true);
                            $users = $users["users"];

                            echo "<table class='center'>";
                            echo "<tr>
                                    <th>Név</th>
                                    <th>Telefonszám</th>
                                    <th>Email</th>
                                    <th>Vásárolt jegyek száma</th>
                                </tr>";

                            for($i=0; $i<count($tickets); $i++) {
                                if($tickets[$i]["filmId"] == $selectedFilm) {
                                    $user = $tickets[$i]["userId"];
                                    for($k = 0; $k<count($users); $k++) {
                                        if($users[$k]["username"] == $user) {
                                            echo "<tr>";
                                            echo "<td>" . "{$users[$k]['username']}" . "</td>";
                                            echo "<td>" . "{$users[$k]['phone']}" . "</td>";
                                            echo "<td>" . "{$users[$k]['email']}" . "</td>";
                                            echo "<td>" . "{$tickets[$i]['count']}" . "</td>";
                                            echo "</tr>";
                                        }
                                    }
                                }
                            }

                            echo "</table>";
                        }
                    }
                }

            ?>

        </div>
    </div>

</body>

</html>