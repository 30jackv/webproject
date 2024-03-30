<?php
    $hibak = [];

    if (isset($_POST["regisztracio"])) { // ha regisztráció gombra rá kattintott
        if (!isset($_POST["teljes-nev"])) { // ha nincs kitöltve
            $hibak[] = "Kötelező megadni a teljes nevet!";
        }
        if (!isset($_POST["lakcim"]) || trim($_POST["lakcim"] === "")) {
            $hibak[] = "Kötelező megadni a lakcímet!";
        }
        if (!isset($_POST["felhasznalonev-regisztracio"]) || trim($_POST["felhasznalonev-regisztracio"] === "")) {
            $hibak[] = "Kötelező megadni a felhasználónevet!";
        }
        if (!isset($_POST["jelszo-regisztracio"])  || trim($_POST["jelszo-regisztracio"] === "")) {
            $hibak[] = "Kötelező megadni a jelszót!";
        }
        if (!isset($_POST["jelszo-regisztracio-megerosites"]) || trim($_POST["jelszo-regisztracio-megerosites"] === "")) {
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

        if (count($hibak) === 0) {
            $siker = true;
        } else {
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
  <link rel="stylesheet" href="css/bejelentkezes.css">
  <link rel="stylesheet" href="css/stylesheet.css">
</head>

<body>
<nav>
  <a href="index.php">Kezdőlap</a>
  <a href="allatok.php">Állatok</a>
  <a class="active">Bejelentkezés</a>
  <a href="kosar.php">Kosár</a>
  <a href="profil.php">Profil</a>
  <a href="admin.php">Admin</a>
</nav>

<div id="bejelentkezes-form">
  <form id="bejelentkezes" method="POST" autocomplete="on">
    <fieldset>
      <legend>Bejelentkezés</legend>
      <label for="username">Felhasználónév:</label>
      <input type="text" id="username" name="felhasznalonev" placeholder="Felhasználónév" required> <br>
      <label for="jelszo">Jelszó: </label>
      <input type="password" id="jelszo" name="jelszo" placeholder="*******" required> <br>
      <input type="submit" value="Bejelentkezés" name="bejelentkezes"> <br>
    </fieldset>
  </form>

  <form id="register" method="POST" autocomplete="off">
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
            echo "<p style='text-align: center; font-size: 20px'>Sikeres rendelés!</p>";
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