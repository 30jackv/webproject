<?php

function isAdmin(): bool {
    $adminfile = "fiokok/admin.json";
    $adminok = json_decode(file_get_contents($adminfile), true);

    $isAdmin = false;
    if (isset($_SESSION["felhasznalo"])) {
        foreach ($adminok["adminok"] as $admin) {
            if ($admin["felhasznalonev"] === $_SESSION["felhasznalo"]["felhasznalonev"]) {
                $isAdmin = true;
                break;
            }
        }
    }
    return $isAdmin;
}