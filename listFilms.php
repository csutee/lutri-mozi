<?php

$weekNumber = $_GET["weekNumber"] ?? date('W');


$films = json_decode(file_get_contents("Assets/Datas/films.json"),true);
$films = $films["films"];
$dateNow = new DateTime();

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

?>