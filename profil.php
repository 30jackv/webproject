<?php
    session_start();
    $usersfile = "fiokok/fiokok.json";

    $adminfile = "fiokok/admin.json";

    $adminok = json_decode(file_get_contents($adminfile), true);

    $admine = false;

    foreach ($adminok["adminok"] as $admin) {
        if ($admin["felhasznalonev"] === $_SESSION["felhasznalo"]["felhasznalonev"]) {
            $admine = true;
            break;
        }
    }

    $fiokok = json_decode(file_get_contents($usersfile), true);

    if (isset($_POST["fnev-valtoztatas"]) && (trim($_POST["fnev-valtoztatas"]) !== "")) {
        foreach ($fiokok["users"] as &$fiok) {
            if ($fiok["felhasznalonev"] === $_SESSION["felhasznalo"]["felhasznalonev"]) {
                $fiok["felhasznalonev"] = $_POST["fnev-valtoztatas"];

                file_put_contents($usersfile, json_encode($fiokok, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $_SESSION["felhasznalo"]["felhasznalonev"] = $_POST["fnev-valtoztatas"];
                $nev_valtoztatas_siker = true;
                break;
            }
        }
        unset($fiok);
    }

    if (isset($_POST["jelszo-valtoztatas"]) && (trim($_POST["jelszo-valtoztatas"]) !== "")) {
        foreach ($fiokok["users"] as &$fiok) {
            if ($fiok["felhasznalonev"] === $_SESSION["felhasznalo"]["felhasznalonev"]) {
                $jelszo = password_hash($_POST["jelszo-valtoztatas"], PASSWORD_DEFAULT);
                $fiok["jelszo"] = $jelszo;

                file_put_contents($usersfile, json_encode($fiokok, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $_SESSION["felhasznalo"]["jelszo"] = $jelszo;
                $jelszo_valtoztatas_siker = true;
                break;
            }
        }
        unset($fiok);
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
  <link rel="stylesheet" href="css/profil.css">
  <link rel="stylesheet" href="css/stylesheet.css">
</head>

<body>
<nav>
  <a href="index.php">Kezdőlap</a>
  <a href="allatok.php">Állatok</a>
    <?php if (!isset($_SESSION["felhasznalo"])) { ?>
        <a href="bejelentkezes.php">Bejelentkezés</a>
    <?php } else { ?>
        <a href="kosar.php">Kosár</a>
        <a class="active">Profil</a>
        <?php if (isset($admine) && ($admine === true)) {?>
            <a href="admin.php">Admin</a>
        <?php } ?>
        <a href="kijelentkezes.php">Kijelentkezés</a>
    <?php } ?>
</nav>

<main>
  <form id="profil-szerkesztes-urlap" method="POST" autocomplete="off">
    <fieldset>
      <legend>Profil szerkesztése</legend>

      <img src="img/giraffe.png" alt="Zsiráf">
        <?php
        echo '<h2>'.$_SESSION["felhasznalo"]["felhasznalonev"] . '</h2>';
        ?>
      <hr>

      <label for="felhasznalonev">Felhasználónév megváltoztatása: </label>
      <input type="text" placeholder="Felhasználónév..." id="felhasznalonev" name="fnev-valtoztatas"> <br>

      <label for="jelszo">Jelszó megváltoztatása:</label>
      <input type="password" placeholder="*******" id="jelszo" name="jelszo-valtoztatas"> <br>

      <label for="profilkep">Profilkép megváltoztatása:</label>
      <input type="file" id="profilkep" name="profilkep-valtoztatas"> <br>

      <input type="submit" value="Változtatás" name="profil-szerkersztes">
      <input type="reset" value="Reset">
        <?php
        if (isset($nev_valtoztatas_siker)) {
            if ($nev_valtoztatas_siker === TRUE) {
                echo "<p style='text-align: center; font-size: 20px'>Sikeres név változtatás!</p>";
            } else {
                echo "<p style='text-align: center; font-size: 20px'>Sikertelen név változtatás!</p>";
            }
        }

        if (isset($jelszo_valtoztatas_siker)) {
            if ($jelszo_valtoztatas_siker === TRUE) {
                echo "<p style='text-align: center; font-size: 20px'>Sikeres jelszó változtatás!</p>";
            } else {
                echo "<p style='text-align: center; font-size: 20px'>Sikertelen jelszó változtatás!</p>";
            }
        }
        ?>
    </fieldset>
    <fieldset>
      <legend>Vásárlási előzmények</legend>
      <div class="jegyek">
        <table>
          <thead>
          <tr>
            <th>Megrendelt jegyek száma</th>
            <th>Összeg</th>
          </tr>
          <tr>
            <td>10</td>
            <td>2 millió</td>
          </tr>
          </thead>
        </table>
        <table>
          <thead>
          <tr>
            <th>Jegy</th>
            <th>Szállítás</th>
            <th>Darab</th>
            <th>Ár</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>Felnőtt</td>
            <td>Expressz</td>
            <td>2</td>
            <td>1000</td>
          </tr>
          <tr>
            <td>Diák</td>
            <td>Expressz</td>
            <td>1</td>
            <td>10000</td>
          </tr>
          <tr>
            <td>Diák</td>
            <td>Expressz</td>
            <td>1</td>
            <td>10000</td>
          </tr>
          <tr>
            <td>Diák</td>
            <td>Expressz</td>
            <td>1</td>
            <td>10000</td>
          </tr>
          <tr>
            <td>Diák</td>
            <td>Expressz</td>
            <td>1</td>
            <td>10000</td>
          </tr>
          <tr>
            <td>Diák</td>
            <td>Expressz</td>
            <td>1</td>
            <td>10000</td>
          </tr>
          <tr>
            <td>Diák</td>
            <td>Expressz</td>
            <td>1</td>
            <td>10000</td>
          </tr>
          <tr>
            <td>Diák</td>
            <td>Expressz</td>
            <td>1</td>
            <td>10000</td>
          </tr>
          <tr>
            <td>Diák</td>
            <td>Expressz</td>
            <td>1</td>
            <td>10000</td>
          </tr>
          <tr>
            <td>Diák</td>
            <td>Expressz</td>
            <td>1</td>
            <td>10000</td>
          </tr>
          </tbody>
        </table>
      </div>
    </fieldset>
  </form>
</main>

</body>
</html>