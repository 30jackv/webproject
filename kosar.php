<?php
    session_start();
    include 'fuggvenyek/adminellenorzes.php';
    include 'fuggvenyek/kosarellenorzes.php';
    include 'fuggvenyek/adatmentestoltes.php';

    $kosarfile = "fiokok/kosarak.json";

    if (!isset($_SESSION["felhasznalo"])) {
        header("Location: index.php");
    }

    $isAdmin = isAdmin();

    $kosar = [
        "felhasznalonev" => $_SESSION["felhasznalo"]["felhasznalonev"],
    ];

    $hibak = [];

    if (isset($_POST["rendeles"])) {
        // diakjegy, felnottjegy, csaladijegy, nyugdijasjegy

        if ((isset($_POST["diakjegy"]) && (!is_numeric($_POST["diakjegy"]) && trim($_POST["diakjegy"]) !== ""))) {
            $hibak[] = "Nem megfelelő érték lett megadva itt: Diákjegy!";
        } elseif (isset($_POST["diakjegy"]) && ($_POST["diakjegy"] >= 1 && $_POST["diakjegy"] <= 5)) {
            $kosar["jegyek"]["Diákjegy"]["darab"] = $_POST["diakjegy"];
        }

        if ((isset($_POST["felnottjegy"]) && (!is_numeric($_POST["felnottjegy"]) && trim($_POST["felnottjegy"]) !== ""))) {
            $hibak[] = "Nem megfelelő érték lett megadva itt: Felnőttjegy!";
        } elseif (isset($_POST["felnottjegy"]) && ($_POST["felnottjegy"] >= 1 && $_POST["felnottjegy"] <= 5)) {
            $kosar["jegyek"]["Felnőttjegy"]["darab"] = $_POST["felnottjegy"];
        }

        if ((isset($_POST["csaladijegy"]) && (!is_numeric($_POST["csaladijegy"]) && trim($_POST["csaladijegy"]) !== ""))) {
            $hibak[] = "Nem megfelelő érték lett megadva itt: Családijegy!";
        } elseif (isset($_POST["csaladijegy"]) && ($_POST["csaladijegy"] >= 1 && $_POST["csaladijegy"] <= 5)) {
            $kosar["jegyek"]["Családijegy"]["darab"] = $_POST["csaladijegy"];
        }

        if ((isset($_POST["Nyugdíjasjegy"]) && (!is_numeric($_POST["nyugdijasjegy"]) && trim($_POST["nyugdijasjegy"]) !== ""))) {
            $hibak[] = "Nem megfelelő érték lett megadva itt: Nyugdíjasjegy!";
        } elseif (isset($_POST["nyugdijasjegy"]) && ($_POST["nyugdijasjegy"] >= 1 && $_POST["nyugdijasjegy"] <= 5)) {
            $kosar["jegyek"]["Nyugdíjasjegy"]["darab"] = $_POST["nyugdijasjegy"];
        }

        if ((!isset($_POST["kiszallitas-mod"]) && (!isset($_POST["express"]) || !isset($_POST["normal"])) )) {
            $hibak[] = "Rossz kiszállítás mód lett kiválasztva!";
        } else {
            $kosar["kiszallitas-mod"] = $_POST["kiszallitas-mod"];
        }

        if (isset($_POST["szallito-megjegyzes"]) && (strlen($_POST["szallito-megjegyzes"]) > 200)) {
            $hibak[] = "A szállító megjegyzés nem lépheti túl a 200 karakter limitet!";
        } else if (isset($_POST["szallito-megjegyzes"]) && trim($_POST["szallito-megjegyzes"]) !== "") {
            $kosar["szallito-megjegyzes"] = $_POST["szallito-megjegyzes"];
        }

        if ((isset($_POST["promokod"]) && (strlen($_POST["promokod"]) !== 6) && trim($_POST["promokod"]) !== "")) {
            $hibak[] = "Promociós kódok csak 6 betűsek lehetnek!";
        } else if (isset($_POST["promokod"]) && trim($_POST["promokod"]) !== "") {
            if (promokodEllenorzes($_POST["promokod"]) != -1) {
                $szazalek = promokodEllenorzes($_POST["promokod"]);
                $kosar["promokod"] = $_POST["promokod"];
            } else {
                $hibak[] = "Nincs ilyen promóciós kód!";
            }
        }

        if (count($hibak) === 0 && isset($kosar["jegyek"])) {
            $siker = true;

            $osszesar = 0;
            if (isset($szazalek) ) {
                foreach ($kosar["jegyek"] as $key => &$value) {
                    $value["ar"] = jegyAra($key)*$value["darab"]*$szazalek;
                    $osszesar += $value["ar"];
                }
            } else {
                foreach ($kosar["jegyek"] as $key => &$value) {
                    $value["ar"] = jegyAra($key)*$value["darab"];
                    $osszesar += $value["ar"];
                }
            }
            unset($value);

            $kosar["osszeg"] = $osszesar + kiszallitasEllenorzes($_POST["kiszallitas-mod"]);

            $kosarak = loadData($kosarfile);

            $kosarak["kosarak"][] = $kosar;

            saveData($kosarfile, $kosarak);
        } else {
            if (!isset($kosar["jegyek"])) {
                $hibak[] = "Nem adtál meg jegyet!";
            }
            $siker = false;
        }
    }
?>

<!DOCTYPE html>

<html lang="hu">

<head>
    <meta charset="UTF-8">
    <title>ECO Állatkert</title>

    <meta name="author" content="Csávás Levente Zsolt, és Tuza Tibor">
    <meta name="description" content="ECO Állatkert honlapja">
    <meta name="keywords" content="állatkert,szeged">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="img/giraffe.png">
    <link rel="stylesheet" href="css/kosar.css">
    <link rel="stylesheet" href="css/stylesheet.css">
</head>
<body>
<nav>
    <a href="index.php">Kezdőlap</a>
    <a href="allatok.php">Állatok</a>
    <?php if (!isset( $_SESSION["felhasznalo"])) { ?>
         <a href="bejelentkezes.php">Bejelentkezés</a>
    <?php } else { ?>
        <a class="active">Kosár</a>
        <a href="profil.php">Profil</a>
        <?php if (isset($isAdmin) && ($isAdmin === true)) {?>
            <a href="admin.php">Admin</a>
        <?php } ?>
        <a href="kijelentkezes.php">Kijelentkezés</a>
    <?php } ?>
</nav>

<form id="kosar" method="POST" autocomplete="off">
<main id="main-kosar">
    <div id="jegyek-container">
        <div class="jegyek">
            <img class="kepek" src="img/giraffe.png" alt="Zsiráf">

            <h3>Diák jegy</h3>
            <div class="leiras-container">
                <label for="diakjegy">14 év felett, diákigazolvánnyal.</label>
                <p class="ar">3500 Ft</p>
                <input type="number" name="diakjegy" id="diakjegy" min="1" max="5">
            </div>
        </div>
        <div class="jegyek">
            <img class="kepek" src="img/giraffe.png" alt="Zsiráf">
            <h3>Felnőtt jegy</h3>
            <div class="leiras-container">
                <label for="felnottjegy">18 év felett (kivéve a diákok és nyugdíjasok).</label>
                <p class="ar">4800 Ft</p>
                <input type="number" name="felnottjegy" id="felnottjegy" min="1" max="5">
            </div>
        </div>
        <div class="jegyek">
            <img class="kepek" src="img/giraffe.png" alt="Zsiráf">

            <h3>Családi jegy</h3>
            <div class="leiras-container">
                <label for="csaladijegy">2 felnőtt és 1-3 gyermek esetén.</label>
                <p class="ar">11 600 Ft</p>
                <input type="number" name="csaladijegy" id="csaladijegy" min="1" max="5">
            </div>
        </div>
        <div class="jegyek">
            <img class="kepek" src="img/giraffe.png" alt="Zsiráf">

            <h3>Nyugdíjas jegy</h3>
            <div class="leiras-container">
                <label for="nyugdijasjegy">Idősek részére, igazolvánnyal.</label>
                <p class="ar">3500 Ft</p>
                <input type="number" name="nyugdijasjegy" id="nyugdijasjegy" min="1" max="5">
            </div>
        </div>
    </div>
    <div id="checkout-container">
        <h2>Összegzés</h2>
        <hr>

        <label for="kiszallitas">Kiszállítás:</label> <br>
        <select id="kiszallitas" name="kiszallitas-mod">
            <option value="Expressz" selected>Expressz: +3500 Ft (1-2 nap várakozási idő)</option>
            <option value="Normál">Normál +1500 Ft (3-5 nap várakozási idő)</option>
        </select> <br>

        <label for="promo">Promóciós kód: </label> <br>
        <input id="promo" type="text" name="promokod" placeholder="Promóciós kód..."> <br>

        <hr>
        <label for="szallitonak">Megjegyzés hagyása a szállítónak:</label> <br>
        <textarea id="szallitonak" name="szallito-megjegyzes" maxlength="200" placeholder="Megjegyzés..." autocomplete="on"></textarea> <br>

        <input name="rendeles" type="submit" value="Rendelés">
        <input type="reset" value="Reset">
        <?php
        if (isset($siker) && $siker === TRUE) {  // ha nem volt hiba, akkor a regisztráció sikeres
            echo "<p style='text-align: center; font-size: 20px'>Sikeres rendelés!</p>";
            echo "<p>" . "Összeg: " . $kosar["osszeg"] . " Ft" . "</p>";

        } else {                                // az esetleges hibákat kiírjuk egy-egy bekezdésben
            foreach ($hibak as $hiba) {
                echo "<p style='text-align: center; font-size: 12px'>" . $hiba . "</p>";
            }
        }
        ?>
    </div>
</main>
</form>

</body>
</html>