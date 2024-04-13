<?php
    session_start();
    include 'fuggvenyek/adminellenorzes.php';

    $programfile = "fiokok/programok.json";
    $ujprogramhibak = [];

    if (!isset($_SESSION["felhasznalo"])) {
        header("Location: index.php");
    }

    $isAdmin = isAdmin();

    if ($isAdmin === true) {
        if (isset($_POST["uj-program-gomb"  ])) {

            if (!isset($_POST["uj-program-nev"]) || trim($_POST["uj-program-nev"]) === "") {
                $ujprogramhibak[] = "Kötelező megadni az új program nevét!";
            }

            if (!isset($_POST["uj-program-ar"]) || trim($_POST["uj-program-ar"]) === "") {
                $ujprogramhibak[] = "Kötelező megadni az új program árát!";
            }

            if (!isset($_POST["uj-program-datum"]) || trim($_POST["uj-program-datum"]) === "") {
                $ujprogramhibak[] = "Kötelező megadni az új program dátumát!";
            }

            if (!isset($_POST["uj-program-datum"])) {
                $ujprogramhibak[] = "Nem megfelelő dátum formátum!";
            }

            $uj_program_nev = $_POST["uj-program-nev"];
            $uj_program_ar = $_POST["uj-program-ar"];
            $uj_program_datum = $_POST["uj-program-datum"];

            if ((isset($_POST["uj-program-ar"]) && trim($_POST["uj-program-ar"]) !== "") && $uj_program_ar < 0 || is_float($uj_program_ar)) {
                $ujprogramhibak[] = "Csak 0 vagy pozitív egész szám lehet az ár!";
            }

            if ($uj_program_ar === "0") {
                $uj_program_ar = "ingyenes";
            }
            $program = [
                    "program-nev" => $uj_program_nev,
                "program-ar" => $uj_program_ar,
                "program-datum" => $uj_program_datum
            ];

            if (count($ujprogramhibak) === 0) {
                $uj_program_siker = true;
                $programok = json_decode(file_get_contents($programfile), true);
                $programok["programok"][] = $program;
                $json_data = json_encode($programok, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                file_put_contents($programfile, $json_data);
            } else {
                $uj_program_siker = false;
            }
        }
        $programfrissiteshibak = [];

        if (isset($_POST["program-frissites-gomb"])) {
            if (!isset($_POST["program-nev"]) || trim($_POST["program-nev"]) === "") {
                $programfrissiteshibak[] = "Kötelező megadni a program nevét!";
            } else {
                $program_nev = $_POST["program-nev"];
                $programoktomb = json_decode(file_get_contents($programfile), true);
            }
            if (count($programfrissiteshibak) === 0) {
                $program_frissites_siker = true;

                if (isset($programoktomb)) {
                    foreach ($programoktomb["programok"] as &$program) {
                        if (isset($program_nev)) {
                            if ($program["program-nev"] === $program_nev) {
                                if (isset($_POST["nev-frissites"]) && (trim($_POST["nev-frissites"]) !== "")) {
                                    $program["program-nev"] = $_POST["nev-frissites"];
                                }
                                if (isset($_POST["ar-frissites"]) && (trim($_POST["ar-frissites"]) !== "")) {
                                    $program["program-ar"] = $_POST["ar-frissites"];
                                }
                                if (isset($_POST["datum-frissites"]) && (trim($_POST["datum-frissites"]) !== "")) {
                                    $program["program-datum"] = $_POST["datum-frissites"];
                                }
                                break;
                            }
                        }
                        unset($program);
                    }
                }

                if (isset($programoktomb)) {
                    file_put_contents($programfile, json_encode($programoktomb, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                }
            } else {
                $program_frissites_siker = false;
            }
        }

        if (isset($_POST["program-torlese-gomb"])) {
            $programok = json_decode(file_get_contents($programfile), true);
            $ujprogramok = [];

            $torlessiker = false;

            if (isset($_POST["torlendo-program-nev"]) && (trim($_POST["torlendo-program-nev"]) !== "")) {
                $program_nev = $_POST["torlendo-program-nev"];
                foreach ($programok["programok"] as $program) {
                    if ($program["program-nev"] !== $program_nev) {
                        $ujprogramok[] = $program;
                    } else {
                        $torlessiker = true;
                    }
                }
            }

            if ($torlessiker === true) {
                $json_data = json_encode(["programok" => $ujprogramok], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                file_put_contents($programfile, $json_data);
            }
        }
    } else {
        header("Location: index.php");
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
  <link rel="stylesheet" href="css/admin.css">
  <link rel="stylesheet" href="css/stylesheet.css">
</head>
<body>
<nav>
    <a href="index.php">Kezdőlap</a>
    <a href="allatok.php">Állatok</a>
    <?php if (!isset( $_SESSION["felhasznalo"])) { ?>
        <a href="bejelentkezes.php">Bejelentkezés</a>
    <?php } else { ?>
        <a href="kosar.php">Kosár</a>
        <a href="profil.php">Profil</a>
        <?php if (isset($isAdmin) && ($isAdmin === true)) {?>
            <a class="active">Admin</a>
        <?php } ?>
        <a href="kijelentkezes.php">Kijelentkezés</a>
    <?php } ?>

</nav>

<main>
  <form id="ar-frissites" method="POST" autocomplete="off">
    <fieldset>
      <legend>Program módosítása</legend>
      <label for="program-nev">Program neve:</label>
        <?php
            $programok = json_decode(file_get_contents($programfile), true);
            echo '<select style="padding: 10px; border-radius: 30px; text-align: center; width: max-content;" name="program-nev" id="program-nev">';
            foreach ($programok["programok"] as $program) {
                echo '<option value="' . $program["program-nev"] . '">' . $program["program-nev"] . ' </option>' ;
            }
            echo '</select>';
        ?>
        <label for="program-nev-frissites">Új program neve:</label>
      <input type="text" id="program-nev-frissites" name="nev-frissites" placeholder="Új program neve"> <br>
      <label for="uj-ar">Új ár:</label>
      <input type="number" id="uj-ar" name="ar-frissites" min="0" placeholder="Új ár"> <br>
      <label for="uj-datum">Új dátum:</label>
      <input type="date" id="uj-datum" name="datum-frissites"> <br>

      <input type="submit" value="Program módosítása" name="program-frissites-gomb">
        <?php
        if (isset($program_frissites_siker) && $program_frissites_siker === TRUE) {  // ha nem volt hiba, akkor a regisztráció sikeres
            echo "<p style='text-align: center; font-size: 20px'>Sikeres frissítés!</p>";
        } else {                                // az esetleges hibákat kiírjuk egy-egy bekezdésben
            if (isset($programfrissiteshibak)) {
                foreach ($programfrissiteshibak as $hiba) {
                    echo "<p style='text-align: center; font-size: 12px'>" . $hiba . "</p>";
                }
            }
        }
        ?>
    </fieldset>
  </form>

  <form id="uj-program" method="POST">
    <fieldset>
      <legend>Új program hozzáadása</legend>
      <label for="uj-program-nev">Új program neve:</label>
      <input type="text" id="uj-program-nev" name="uj-program-nev" placeholder="Új program neve"> <br>
      <label for="uj-program-ar">Ár:</label>
      <input type="number" id="uj-program-ar" name="uj-program-ar" min="0" placeholder="Ár"> <br>
      <label for="uj-program-datum">Dátum:</label>
      <input type="date" id="uj-program-datum" name="uj-program-datum"> <br>

      <input type="submit" value="Program hozzáadása" name="uj-program-gomb">
        <?php
        if (isset($siker) && $siker === TRUE) {
            echo "<p style='text-align: center; font-size: 20px'>Sikeres hozzáadás!</p>";
        } else {
            foreach ($ujprogramhibak as $hiba) {
                echo "<p style='text-align: center; font-size: 12px'>" . $hiba . "</p>";
            }
        }
        ?>
    </fieldset>
  </form>

  <form id="program-torles-urlap" method="POST">
    <fieldset>
      <legend>Program törlése</legend>
      <label for="program-torles">Törlendő program neve:</label>
        <?php
        $programok = json_decode(file_get_contents($programfile), true);
        echo '<select style="padding: 10px; border-radius: 30px; text-align: center; width: max-content;" name="torlendo-program-nev" id="program-nev">';
        foreach ($programok["programok"] as $program) {
            echo '<option value="' . $program["program-nev"] . '">' . $program["program-nev"] . ' </option>' ;
        }
        echo '</select>';
        ?>
        <input type="submit" value="Program törlése" name="program-torlese-gomb">
        <?php
        if (isset($torlessiker)) {
            if ($torlessiker === TRUE) {
                echo "<p style='text-align: center; font-size: 20px'>Sikeres törlés!</p>";
            } else {
                echo "<p style='text-align: center; font-size: 20px'>Sikertelen törlés!</p>";
            }
        }
        ?>
    </fieldset>
  </form>
    <form id="uj-allat-hozzaadasa" method="POST" autocomplete="off" enctype="multipart/form-data">
        <fieldset>
            <legend>Állat hozzáadása</legend>
            <label for="allat-fajta">Állat fajta:</label>
            <input type="text" id="allat-fajta" name="allat-fajta" placeholder="Állat fajtája">
            <label for="allat-kep">Állat képe:</label>
            <input type="file" id="allat-kep" name="allat-kep">
            <label for="allat-leiras">Állat fajta:</label>
            <textarea cols="30" rows="4" id="allat-leiras" name="allat-leiras" placeholder="Állat leírása..."></textarea>
        </fieldset>
    </form>
</main>

</body>
</html>