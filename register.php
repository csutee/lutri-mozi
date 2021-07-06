<?php
    session_start();

    $username = $_POST["name"] ?? "";
    $phoneNumber = $_POST["phone"] ?? "";
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";
    $passwordConfirm = $_POST["passwordConfirm"] ?? "";
    $errors = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if($username == "") {
            $errors["username"] = "A felhasználónév nem lehet üres!";
        }
        if($phoneNumber == "") {
            $errors["phoneNumber"] = "A telefonszám nem lehet üres!";
        }
        else {
            if(!filter_var($phoneNumber, FILTER_VALIDATE_INT) || !($phoneNumber > 0)) {
                $errors["phoneNumber"] = "A telefonszámban csak egész számok szerepelhetnek!";
            }
            else {
                if(strlen($phoneNumber) != 9) {
                    $errors["phoneNumber"] = "A telefonszám pontosan 9 karakterből állhat!";
                }
            }
        }
        if($email == "") {
            $errors["email"] = "Az email nem lehet üres!";
        }
        else {
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors["email"] = "Az email nem helyes formátumú!";
            }
        }
        if($password == "") {
            $errors["password"] = "A jelszó nem lehet üres!";
        }
        if($passwordConfirm == "") {
            $errors["passwordConfirm"] = "A jelszó megerősítő nem lehet üres!";
        }
        else {
            if($password != $passwordConfirm) {
                $errors["passwordConfirm"] = "A két jelszó nem egyezik.";
            }
        }
        
    
        if(count($errors) == 0) {
            $users = json_decode(file_get_contents("Assets/Datas/users.json"),true);
            $tmp = array();
            $tmp["username"] = $username;
            $tmp["email"] = $email;
            $tmp["phone"] = $phoneNumber;
            $tmp["password"] = md5($password);
            array_push($users["users"], $tmp);
            file_put_contents("Assets/Datas/users.json",json_encode($users, JSON_PRETTY_PRINT));
            header("Location: login.php");
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

            <h1 style="text-align: center">Regisztráció</h1>

            <form action="register.php" method="POST" novalidate>

                <div class="form-group">
                    <label for="name">Teljes név:</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Teljes név" value="<?= $username ?>"> <?= $errors["username"] ?? "" ?>
                </div>

                <div class="form-group">
                    <label for="phone">Telefonszám (+36):</label>
                    <input type="number" class="form-control" name="phone" id="phone" placeholder="Telefonszám" value="<?= $phoneNumber ?>"> <?= $errors["phoneNumber"] ?? "" ?>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" class="form-control" name="email" id="email" placeholder="E-mail" value="<?= $email ?>"> <?= $errors["email"] ?? "" ?>
                </div>

                <div class="form-group">
                    <label for="password">Jelszó:</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Jelszó" value="<?= $password ?>"> <?= $errors["password"] ?? "" ?>
                </div>
                <div class="form-group">
                    <label for="passwordConfirm">Jelszó megerősítése:</label>
                    <input type="password" class="form-control" name="passwordConfirm" id="passwordConfirm" placeholder="Jelszó megerősítése" value="<?= $passwordConfirm ?>"> <?= $errors["passwordConfirm"] ?? "" ?>
                </div>

                <button type="submit" class="btn btn-success" style="margin: auto; display: block">Regisztráció</button>

            </form>
            

        </div>
    </div>

</body>

</html>