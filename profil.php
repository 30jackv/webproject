<?php
    session_start();
    $usersfile = "fiokok/fiokok.json";

    $adminfile = "fiokok/admin.json";

    $kosarakfile = "fiokok/kosarak.json";

    $elfogadott_kiterjesztesek = ["jpg", "jpeg", "png"];

    $adminok = json_decode(file_get_contents($adminfile), true); // adminok tömb

    $admine = false;

    if (!isset($_SESSION["felhasznalo"])) {
        header("Location: index.php");
    }

    // ha admin
    foreach ($adminok["adminok"] as $admin) {
        if ($admin["felhasznalonev"] === $_SESSION["felhasznalo"]["felhasznalonev"]) {
            $admine = true;
            break;
        }
    }

    $fiokok = json_decode(file_get_contents($usersfile), true);


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

                        $fiok["felhasznalonev"] = $_POST["fnev-valtoztatas"];

                        file_put_contents($usersfile, json_encode($fiokok, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        $_SESSION["felhasznalo"]["felhasznalonev"] = $_POST["fnev-valtoztatas"];


                        break;
                    }
                }
                unset($fiok);
            }
        }

        // jelszo valtoztatas
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
        <?php if (isset($admine) && ($admine === true)) {?>
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
              $kosarak = json_decode(file_get_contents($kosarakfile), true);

              $diakjegyar = 3500;
              $felnottjegyar = 4800;
              $csaladijegyar = 11600;
              $nyugdijasjegyar = 3500;

              $osszesar = 0;
              $osszesdarab = 0;

              foreach ($kosarak["kosarak"] as $kosar) {
                  if ($kosar["felhasznalonev"] === $_SESSION["felhasznalo"]["felhasznalonev"]) {
                      foreach ($kosar["jegyek"] as $jegyTipus => $jegyOsszeg) {
                          if ($jegyTipus === "diakjegy") {
                              $osszesdarab += $jegyOsszeg;
                              $osszesar += $jegyOsszeg*$diakjegyar;
                          }
                          if ($jegyTipus === "felnottjegy") {
                              $osszesdarab += $jegyOsszeg;
                              $osszesar += $jegyOsszeg*$felnottjegyar;
                          }
                          if ($jegyTipus === "csaladijegy") {
                              $osszesdarab += $jegyOsszeg;
                              $osszesar += $jegyOsszeg*$csaladijegyar;
                          }
                          if ($jegyTipus === "nyugdijasjegy") {
                              $osszesdarab += $jegyOsszeg;
                              $osszesar += $jegyOsszeg*$nyugdijasjegyar;
                          }
                      }
                  }
              }

              echo '<td>' . $osszesdarab . '</td>';
              echo '<td>' . $osszesar . '</td>';

              ?>
          </tr>
          </thead>
        </table>
          <?php
          $kosarak = json_decode(file_get_contents($kosarakfile), true);
          if (!empty($kosarak["kosarak"])) { ?>
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
          $kosarak = json_decode(file_get_contents($kosarakfile), true);

          $diakjegyar = 3500;
          $felnottjegyar = 4800;
          $csaladijegyar = 11600;
          $nyugdijasjegyar = 3500;

          foreach ($kosarak["kosarak"] as $kosar) {
              if ($kosar["felhasznalonev"] === $_SESSION["felhasznalo"]["felhasznalonev"]) {
                  // A jegyek kiírása
                  if (isset($kosar["jegyek"]["diakjegy"])) {
                      echo '<tr>';
                      echo '<td>' . 'Diákjegy' . '</td>';
                      echo '<td>' . $kosar["kiszallitas-mod"] . '</td>';
                      echo '<td>' . $kosar["jegyek"]["diakjegy"] . '</td>';
                      echo '<td>' . $kosar["jegyek"]["diakjegy"]*$diakjegyar . '</td>';
                      echo '</tr>';

                  }
                  if (isset($kosar["jegyek"]["felnottjegy"])) {
                      echo '<tr>';

                      echo '<td>' . 'Felnőttjegy' . '</td>';
                      echo '<td>' . $kosar["kiszallitas-mod"] . '</td>';
                      echo '<td>' . $kosar["jegyek"]["felnottjegy"] . '</td>';
                      echo '<td>' . $kosar["jegyek"]["felnottjegy"]*$felnottjegyar . '</td>';
                      echo '</tr>';

                  }
                  if (isset($kosar["jegyek"]["csaladijegy"])) {
                      echo '<tr>';

                      echo '<td>' . 'Családijegy' . '</td>';
                      echo '<td>' . $kosar["kiszallitas-mod"] . '</td>';
                      echo '<td>' . $kosar["jegyek"]["csaladijegy"] . '</td>';
                      echo '<td>' . $kosar["jegyek"]["csaladijegy"]*$csaladijegyar . '</td>';
                      echo '</tr>';

                  }
                  if (isset($kosar["jegyek"]["nyugdijasjegy"])) {
                      echo '<tr>';

                      echo '<td>' . 'Nyugdíjasjegy' . '</td>';
                      echo '<td>' . $kosar["kiszallitas-mod"] . '</td>';
                      echo '<td>' . $kosar["jegyek"]["nyugdijasjegy"] . '</td>';
                      echo '<td>' . $kosar["jegyek"]["nyugdijasjegy"]*$nyugdijasjegyar . '</td>';
                      echo '</tr>';


                  }
              }
          }
          ?>
          </tbody>
        </table>
          <?php } ?>
      </div>
    </fieldset>
  </form>
</main>

</body>
</html>