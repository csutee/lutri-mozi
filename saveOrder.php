<?php

    if(count($_POST) > 2) {
        $films = json_decode(file_get_contents("Assets/Datas/films.json"),true);
        $films = $films["films"];
        $tickets = json_decode(file_get_contents("Assets/Datas/tickets.json"), true);
        //$tickets = $tickets["tickets"];
        $filmId = $_POST["id"];
        $name = $_POST["name"];
        $ticketNumber = $_POST["ticketNumber"];


        for($i = 0; $i<count($films); $i++) {
            if($films[$i]["id"] == intval($filmId)) {
                for($j = 0; $j < intval($ticketNumber); $j++) {
                    $films[$i]["freeSeats"] = $films[$i]["freeSeats"] - 1;
                }
            }
        }
        $existingTicket = false;

        for($i = 0; $i<count($tickets["tickets"]); $i++) {
            if($tickets["tickets"][$i]["filmId"] == $filmId && $tickets["tickets"][$i]["userId"] == $name) {
                $existingTicket = true;
                $tickets["tickets"][$i]["count"] += intval($ticketNumber);
            }
        }
        if(!$existingTicket) {
            $tmp = array();
            $tmp["userId"] = $name;
            $tmp["filmId"] = intval($filmId);
            $tmp["count"] = intval($ticketNumber);

            array_push($tickets["tickets"], $tmp);
        }
        

        file_put_contents("Assets/Datas/tickets.json",json_encode($tickets, JSON_PRETTY_PRINT));
        file_put_contents("Assets/Datas/films.json",json_encode(array("films" => $films), JSON_PRETTY_PRINT));

        echo "success";
    }
    else {
        echo "error";
    }

?>