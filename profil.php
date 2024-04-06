<?php
    session_start();
    include 'fuggvenyek/adminellenorzes.php';
    include 'fuggvenyek/kosarellenorzes.php';
    include 'fuggvenyek/adatmentestoltes.php';

    $usersfile = "fiokok/fiokok.json";

    $kosarakfile = "fiokok/kosarak.json";

    $promokodfile = "fiokok/promokodok.json";

    $elfogadott_kiterjesztesek = ["jpg", "jpeg", "png"];

    if (!isset($_SESSION["felhasznalo"])) {
        header("Location: index.php");
    }

    $isAdmin = isAdmin();


    $fiokok = loadData($usersfile);
    $kosarak = loadData($kosarakfile);

    // név változtatás
        if (isset($_POST["fnev-valtoztatas"]) && (trim($_POST["fnev-valtoztatas"]) !== "")) {
            $nev_valtoztatas_siker = true;
            $_POST["fnev-valtoztatas"] = trim($_POST["fnev-valtoztatas"]);
            foreach ($fiokok["users"] as $fiok) {
                if ($fiok["felhasznalonev"] === $_POST["fnev-valtoztatas"]) {
                    $nev_valtoztatas_siker = false;
                }
            }

            if (count(explode(" ", $_POST["fnev-valtoztatas"])) !== 1) {
                $nev_valtoztatas_siker = false;
            }

            if ($nev_valtoztatas_siker === TRUE) {
                foreach ($fiokok["users"] as &$fiok) {
                    if ($fiok["felhasznalonev"] === $_SESSION["felhasznalo"]["felhasznalonev"]) {

                        foreach ($elfogadott_kiterjesztesek as $value) {
                            if (file_exists("profilkepek/" . $_SESSION["felhasznalo"]["felhasznalonev"] . "." . $value)) {
                                rename("profilkepek/" . $_SESSION["felhasznalo"]["felhasznalonev"] . "." . $value,
                                    "profilkepek/" . $_POST["fnev-valtoztatas"] . "." . $value);
                            }
                        }

                        foreach ($kosarak["kosarak"] as $item => &$value) {
                            if ($value["felhasznalonev"] === $_SESSION["felhasznalo"]["felhasznalonev"]) {
                                $value["felhasznalonev"] = $_POST["fnev-valtoztatas"];
                            }
                        }
                        unset($value);

                        $_SESSION["felhasznalo"]["felhasznalonev"] = $_POST["fnev-valtoztatas"];

                        saveData($kosarakfile, $kosarak);

                        $fiok["felhasznalonev"] = $_POST["fnev-valtoztatas"];

                        saveData($usersfile, $fiokok);

                        unset($fiok);
                        break;
                    }
                }
            }
        }

        // jelszo valtoztatas
        if (isset($_POST["jelszo-valtoztatas"]) && (trim($_POST["jelszo-valtoztatas"]) !== "")) {
            foreach ($fiokok["users"] as &$fiok) {
                if ($fiok["felhasznalonev"] === $_SESSION["felhasznalo"]["felhasznalonev"]) {
                    $jelszo = password_hash($_POST["jelszo-valtoztatas"], PASSWORD_DEFAULT);
                    $fiok["jelszo"] = $jelszo;
                    saveData($usersfile, $fiokok);
                    $_SESSION["felhasznalo"]["jelszo"] = $jelszo;
                    $jelszo_valtoztatas_siker = true;
                    break;
                }
            }
            unset($fiok);
        }

        // profilkep fajlkezeles
        $profilkephibak = [];
        $profilkeputvonal = "profilkepek/" . $_SESSION["felhasznalo"]["felhasznalonev"];
        $profilkepsiker = false;

        if (isset($_FILES["profilkep-valtoztatas"]) && $_FILES["profilkep-valtoztatas"]["error"] === 0) {
            $profilkepfile = $_FILES["profilkep-valtoztatas"];
            $fajlkiterjesztes = explode(".", $profilkepfile["name"]);
            $fajlkiterjesztes = end($fajlkiterjesztes);

            $megfelelokiterjesztes = false;
            foreach ($elfogadott_kiterjesztesek as $value) {
                if ($value === $fajlkiterjesztes) {
                    $megfelelokiterjesztes = true;
                    break;
                }
            }

            if ($profilkepfile["size"] > 2097152) {
                $profilkephibak[] = "A kép nem lépheti túl a 2 mb méretet!";
            }

            if ($megfelelokiterjesztes === false) {
                $profilkephibak[] = ".jpeg .jpg és .png fájl kiterjesztés megengedett!";
            }

            if (count($profilkephibak) === 0 && $megfelelokiterjesztes) {
                if (move_uploaded_file($_FILES["profilkep-valtoztatas"]["tmp_name"], $profilkeputvonal . "." . $fajlkiterjesztes)) {
                    $ujletrehozottfajl = $_SESSION["felhasznalo"]["felhasznalonev"] . "." . $fajlkiterjesztes;
                    $profilkepsiker = true;
                } else {
                    $profilkephibak[] = "Nem sikerült feltölteni a fájlt!";
                }
            }
        }
        $tobbfajloklistaja = [];
        foreach ($elfogadott_kiterjesztesek as $value) {
            if (file_exists("profilkepek/" . $_SESSION["felhasznalo"]["felhasznalonev"] . "." . $value)) {
                $tobbfajloklistaja[] = $_SESSION["felhasznalo"]["felhasznalonev"] . "." . $value;
            }
        }
        if ($profilkepsiker && count($tobbfajloklistaja) >= 2) {
            foreach($tobbfajloklistaja as $value) {
                if (isset($ujletrehozottfajl)) {
                    if ($value !== $ujletrehozottfajl) {
                        unlink("profilkepek/" . $value);
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
        <?php if (isset($isAdmin) && ($isAdmin === true)) {?>
            <a href="admin.php">Admin</a>
        <?php } ?>
        <a href="kijelentkezes.php">Kijelentkezés</a>
    <?php } ?>
</nav>

<main>
  <form id="profil-szerkesztes-urlap" action="profil.php" method="POST" autocomplete="off" enctype="multipart/form-data">
    <fieldset>
      <legend>Profil szerkesztése</legend>

        <?php
        $letezikfelhasznalokep = false;
        foreach ($elfogadott_kiterjesztesek as $value) {
            if (file_exists("profilkepek/" . $_SESSION["felhasznalo"]["felhasznalonev"] . "." . $value)) {
                $letezikfelhasznalokep = true;
                break;
            }
        }
        if ($letezikfelhasznalokep) {
            echo '<img src="profilkepek/' . $_SESSION["felhasznalo"]["felhasznalonev"] . "." . $value . '" alt ="Profilkép">';
        } else {
            echo "<img src='img/giraffe.png' alt='Profilkép'>";
        }
        ?>

        <?php
        echo '<h2>'.$_SESSION["felhasznalo"]["felhasznalonev"] . '</h2>';
        ?>
      <hr>

      <label for="felhasznalonev">Felhasználónév megváltoztatása: </label>
      <input type="text" placeholder="Felhasználónév..." id="felhasznalonev" name="fnev-valtoztatas"> <br>

      <label for="jelszo">Jelszó megváltoztatása:</label>
      <input type="password" placeholder="*******" id="jelszo" name="jelszo-valtoztatas"> <br>

      <label for="profilkep">Profilkép megváltoztatása:</label>
      <input type="file" id="profilkep" name="profilkep-valtoztatas" accept="image/*"> <br>

      <input type="submit" value="Változtatás" name="profil-szerkesztes">
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

        if (isset($profilkepsiker)) {
            if ($profilkepsiker === TRUE) {
                echo "<p style='text-align: center; font-size: 20px'>Sikeres profilkép változtatás!</p>";
            } else {
                if (isset($profilkephibak)) {
                    foreach ($profilkephibak as $hiba) {
                        echo "<p style='text-align: center; font-size: 12px'>" . $hiba . "</p>";
                    }
                }
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
              <?php
              $kosarak = loadData($kosarakfile);
              $osszesosszeg = 0;
              $osszesdarab = 0;
              foreach($kosarak["kosarak"] as $kosar) {
                  if ($kosar["felhasznalonev"] === $_SESSION["felhasznalo"]["felhasznalonev"]) {
                      $osszesosszeg += $kosar["osszeg"];
                      foreach ($kosar["jegyek"] as $value) {
                          $osszesdarab+=$value["darab"];
                      }
                  }
              }
              echo '<td>' . $osszesdarab . '</td>';
              echo '<td>' . $osszesosszeg . '</td>';
              ?>
          </tr>
          </thead>
        </table>
          <?php
          $kosarak = loadData($kosarakfile);
          $talaltkosarat = false;
          foreach ($kosarak["kosarak"] as $kosar) {
              if ($kosar["felhasznalonev"] === $_SESSION["felhasznalo"]["felhasznalonev"]) {
                  $talaltkosarat = true;
                  break;
              }
          }
          if ($talaltkosarat) {
          ?>
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
          <?php
            include 'fuggvenyek/tablazatkiiratas.php';
            vasarlasiElozmenyekKiiratas($kosarakfile);
          ?>
          </tbody>
        </table>
          <?php } else {
              echo '<h1>' . 'Üres kosár!' . '</h1>';
          } ?>
      </div>
    </fieldset>
  </form>
</main>

</body>
</html>