<?php

function vasarlasiElozmenyekKiiratas($kosarakfile): void {
    $kosarak = loadData($kosarakfile);
    foreach($kosarak["kosarak"] as $kosar) {
        if ($kosar["felhasznalonev"] === $_SESSION["felhasznalo"]["felhasznalonev"]) {
            foreach($kosar["jegyek"] as $jegyTipus => $value) {
                echo '<tr>';
                echo '<td>' . $jegyTipus . '</td>';
                echo '<td>' . $kosar["kiszallitas-mod"] . '</td>';
                echo '<td>' . $value["darab"] . '</td>';
                echo '<td>' . $value["ar"] . '</td>';
                echo '</tr>';
            }
        }
    }
}