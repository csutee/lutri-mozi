<?php

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST["username"];
    $filmId = intval($_POST["id"]);
    $ticketCount = intval($_POST["ticketCount"]);
    $films = json_decode(file_get_contents("Assets/Datas/films.json"),true);
    $tickets = json_decode(file_get_contents("Assets/Datas/tickets.json"),true);

    for($i = 0; $i<count($tickets["tickets"]); $i++) {
        if($tickets["tickets"][$i]["userId"] == $username && $tickets["tickets"][$i]["filmId"] == $filmId) {
            unset($tickets["tickets"][$i]);
        }
    }

    for($i = 0; $i < count($films["films"]); $i++) {
        if($films["films"][$i]["id"] == $filmId) {
            $films["films"][$i]["freeSeats"] += $ticketCount;
        }
    }
    file_put_contents("Assets/Datas/films.json",json_encode($films, JSON_PRETTY_PRINT));
    file_put_contents("Assets/Datas/tickets.json",json_encode($tickets, JSON_PRETTY_PRINT));

    echo "success";
}

?>