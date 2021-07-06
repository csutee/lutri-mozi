<?php
    session_start();
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";
    $errors = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if($email == "") {
            $errors["email"] = "Az e-mail cím nem lehet üres!";
        }
        else {
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors["email"] = "Az e-mail cím formátuma helytelen!";
            }
        }
        if($password == "") {
            $errors["password"] = "A jelszó nem lehet üres!";
        }

        if(count($errors) == 0) {
            $users = json_decode(file_get_contents("Assets/Datas/users.json"),true);
            $users = $users["users"];
            //var_dump($users);
            for($i = 0; $i < count($users); $i++) {
                if($users[$i]["email"] == $email && $users[$i]["password"] == md5($password)) {
                    $_SESSION["username"] = $users[$i]["username"];
                    $_SESSION["email"] = $users[$i]["email"];
                    $_SESSION["loggedIn"] = true;
                    $_SESSION["isAdmin"] = $users[$i]["isAdmin"];
                    header("Location: index.php");
                    break;
                }
            }
            $errors["email"] = "Nem létezik ilyen email/jelszó kombináció!";
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

            <h1 style="text-align: center">Bejelentkezés</h1>

            <?php

                foreach ($errors as $key => $value) {
                    echo "<span style='margin: auto; display: block; text-align: center'>{$value}</span>";
                }

            ?>

            <form action="login.php" method="POST" novalidate>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" class="form-control" name="email" id="email" placeholder="E-mail" value="<?= $email ?>">
                </div>

                <div class="form-group">
                    <label for="password">Jelszó:</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Jelszó" value="<?= $password ?>">
                </div>

                <button type="submit" class="btn btn-success" style="margin: auto; display: block">Bejelentkezés</button>

            </form>
            

        </div>
    </div>

</body>

</html>