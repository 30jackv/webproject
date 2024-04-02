<?php
    session_start();

    $usersfile = "fiokok/fiokok.json";

    // regisztracio
    $hibak = [];
    if (isset($_POST["regisztracio"])) { // ha regisztráció gombra rá kattintott
        if (!isset($_POST["teljes-nev"]) || trim($_POST["teljes-nev"]) === "") { // ha nincs kitöltve
            $hibak[] = "Kötelező megadni a teljes nevet!";
        }
        if (!isset($_POST["lakcim"]) || trim($_POST["lakcim"]) === "") {
            $hibak[] = "Kötelező megadni a lakcímet!";
        }
        if (!isset($_POST["felhasznalonev-regisztracio"]) || trim($_POST["felhasznalonev-regisztracio"]) === "") {
            $hibak[] = "Kötelező megadni a felhasználónevet!";
        }
        if (isset( $_POST["felhasznalonev-regisztracio"]) && (count(explode(" ", $_POST["felhasznalonev-regisztracio"])) > 1)) {
            $hibak[] = "Felhasználónévben nem lehet szóköz!";
        }
        if (!isset($_POST["jelszo-regisztracio"])  || trim($_POST["jelszo-regisztracio"]) === "") {
            $hibak[] = "Kötelező megadni a jelszót!";
        }
        if (!isset($_POST["jelszo-regisztracio-megerosites"]) || trim($_POST["jelszo-regisztracio-megerosites"]) === "") {
            $hibak[] = "Kötelező megadni a jelszót kétszer!";
        }

        $teljesnev_tomb = explode(" ", $_POST["teljes-nev"]);
        $lakcim_tomb = explode(", ", $_POST["lakcim"]);
        $teljesnev = $_POST["teljes-nev"];
        $lakcim = $_POST["lakcim"];
        $felhasznalonev_regisztracio = $_POST["felhasznalonev-regisztracio"];
        $jelszo1 = $_POST["jelszo-regisztracio"];
        $jelszo2 = $_POST["jelszo-regisztracio-megerosites"];

        if ($jelszo1 !== $jelszo2) {
            $hibak[] = "Nem egyeznek meg a jelszavak!";
        }

        // ha teljesneve 1 elemből áll
        if (count($teljesnev_tomb) === 1) {
            $hibak[] = "Teljes nevet kell megadni!";
        }

        // ha ", " mentén lakcím hossza nem 3
        if (count($lakcim_tomb) !== 3) {
            $hibak[] = "Város, Utcát, Házszámot kell megadni! Rossz formátum.";
        }

        if (count(file($usersfile)) > 4) {
            $fiokok = json_decode(file_get_contents($usersfile), true);

            foreach ($fiokok["users"] as $fiok) {
                if ($fiok["felhasznalonev"] === $felhasznalonev_regisztracio) {
                    $hibak[] = "Már létezik ilyen felhasználónév!";
                    break;
                }
            }
        }

        if (count($hibak) === 0) {
            $jelszo = password_hash($jelszo1, PASSWORD_DEFAULT);
            $fiok = [
              "felhasznalonev" => $felhasznalonev_regisztracio,
                "jelszo" => $jelszo,
                "lakcim" => $lakcim_tomb,
                "teljesnev" => $teljesnev_tomb
            ];

            $fiokok = json_decode(file_get_contents($usersfile), true);
            $fiokok["users"][] = $fiok;
            $json_data = json_encode($fiokok, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            file_put_contents($usersfile, $json_data);

            $siker = true;
        } else {
            $siker = false;
        }
    }


    // bejelentkezés
    if (isset($_POST["bejelentkezes"])) {

        if (!isset($_POST["felhasznalonev"]) || !isset($_POST["jelszo"]) || trim($_POST["felhasznalonev"]) === "" || trim($_POST["jelszo"]) === "") {
            $uzenet = "Kötelező kitölteni a mezőket!";
        } else {
            $felhasznalonev = $_POST["felhasznalonev"];
            $jelszo = $_POST["jelszo"];

            $uzenet = "Az adatok nem megfelelők";

            $fiokok = json_decode(file_get_contents($usersfile), true);

            foreach ($fiokok["users"] as $fiok) {
                if ($fiok["felhasznalonev"] === $felhasznalonev && password_verify($jelszo, $fiok["jelszo"])) {
                    $uzenet = "Sikeres belépés!";
                    $_SESSION["felhasznalo"] = $fiok;
                    header("Location: profil.php");
                    break;
                }
            }
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
  <link rel="stylesheet" href="css/bejelentkezes.css">
  <link rel="stylesheet" href="css/stylesheet.css">
</head>

<body>
<nav>
  <a href="index.php">Kezdőlap</a>
  <a href="allatok.php">Állatok</a>
    <?php if (!isset($_SESSION["felhasznalo"])) { ?>
        <a class="active">Bejelentkezés</a>
    <?php } else { ?>
        <a href="kosar.php">Kosár</a>
        <a href="profil.php">Profil</a>
        <a href="admin.php">Admin</a>
        <a href="kijelentkezes.php">Kijelentkezés</a>
    <?php } ?>
</nav>

<div id="bejelentkezes-form">
  <form id="bejelentkezes" action="bejelentkezes.php" method="POST" autocomplete="on">
    <fieldset>
      <legend>Bejelentkezés</legend>
      <label for="username">Felhasználónév:</label>
      <input type="text" id="username" name="felhasznalonev" placeholder="Felhasználónév" required> <br>
      <label for="jelszo">Jelszó: </label>
      <input type="password" id="jelszo" name="jelszo" placeholder="*******" required> <br>
      <input type="submit" value="Bejelentkezés" name="bejelentkezes"> <br>
        <?php
        if (isset($uzenet)) {
            echo "<p style='text-align: center; font-size: 20px'>" . $uzenet ."</p>";
        }
        ?>
    </fieldset>
  </form>

  <form id="register" action="bejelentkezes.php" method="POST" autocomplete="off">
    <fieldset>
      <legend>Regisztráció</legend>
      <label for="full-name">Teljes név*</label>
      <input type="text" id="full-name" name="teljes-nev" placeholder="Teljes név" required> <br>

      <label for="lakcim">Lakcím*</label>
      <input type="text" id="lakcim" name="lakcim" placeholder="Város, Utca, Házszám" required> <br>

      <label for="username-register">Felhasználónév*</label>
      <input type="text" id="username-register" name="felhasznalonev-regisztracio" placeholder="Felhasználónév" required> <br>

      <label for="password-register">Jelszó*</label>
      <input type="password" id="password-register" name="jelszo-regisztracio" placeholder="*******" required> <br>

      <label for="password-register-confirm">Jelszó mégerősítése*</label>
      <input type="password" id="password-register-confirm" name="jelszo-regisztracio-megerosites" placeholder="*******" required> <br>

      <input type="submit" value="Regisztráció" name="regisztracio"> <br>
      <input type="reset" value="Reset" name="reset">
        <?php
        if (isset($siker) && $siker === TRUE) {  // ha nem volt hiba, akkor a regisztráció sikeres
            echo "<p style='text-align: center; font-size: 20px'>Sikeres regisztráció!</p>";
        } else {                                // az esetleges hibákat kiírjuk egy-egy bekezdésben
            foreach ($hibak as $hiba) {
                echo "<p style='text-align: center; font-size: 12px'>" . $hiba . "</p>";
            }
        }
        ?>
    </fieldset>
    </form>
</div>


</body>
</html>