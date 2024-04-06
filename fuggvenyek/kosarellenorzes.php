<?php
function jegyAra($jegyTipus): int {
    switch ($jegyTipus) {
        case "Nyugdíjasjegy":
        case "Diákjegy":
            return 3500;
        case "Felnőttjegy":
            return 4800;
        case "Családijegy":
            return 11600;
        default:
            return -1;
    }
}

function kiszallitasEllenorzes($kapottKiszallitas): int {
    switch ($kapottKiszallitas) {
        case 'Expressz':
            return 3500;
        case 'Normál':
            return 1500;
        default:
            return -1;
    }
}

function promokodEllenorzes($kapottPromokod): float {
    $promokodfile = 'fiokok/promokodok.json';
    $promokodok = loadData($promokodfile);

    if (isset($kapottPromokod)) {
        foreach ($promokodok["promokodok"] as $promokod) {
            if ($promokod["kod"] === $kapottPromokod) {
                return (1-($promokod["szazalek"]/100));
            }
        }
    }
    return -1;
}