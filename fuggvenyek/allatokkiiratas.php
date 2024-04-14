<?php
function allatKiiratas(): void {
    include 'fuggvenyek/adatmentestoltes.php';

    $allatkepekutvonala = "allatkepek/";
    $allatokutvonala = "fiokok/allatok.json";

    $allattomb = loadData($allatokutvonala);
    if (count($allattomb["allatok"]) === 0) {
        echo '<h1>Nincsen állatunk! :(</h1>';
    } else {
        foreach ($allattomb["allatok"] as $value) {
            if (file_exists($allatkepekutvonala . $value["allat-kep"])) {
                echo '<section>';
                echo '<img src="' . $allatkepekutvonala . $value["allat-kep"] . '" alt="' . $value["allat-fajta"] . '" >';
                echo '<h2>' . $value["allat-fajta"] . '</h2>';
                echo '<p>' . $value["allat-leiras"] . '</p>';
                echo '</section>';

            }
        }
        echo '<table id="allatok-tablazat">';
        echo '<caption>' . 'Állatok információi' .'</caption>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>' . 'Állat' . '</th>';
        echo '<th>' . 'Kor' . '</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($allattomb["allatok"] as $value) {
            echo '<tr>';
            echo '<td>' . $value["allat-fajta"] .'</td>';
            echo '<td>' . $value["allat-kor"] .'</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }
}

