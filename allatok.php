<?php
    session_start();
    include 'fuggvenyek/adminellenorzes.php';

    $isAdmin = isAdmin();
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
  <link rel="stylesheet" href="css/allatok.css">
  <link rel="stylesheet" href="css/stylesheet.css">
</head>

<body>
<nav>
  <a href="index.php">Kezdőlap</a>
  <a class="active">Állatok</a>
    <?php if (!isset($_SESSION["felhasznalo"])) { ?>
        <a href="bejelentkezes.php">Bejelentkezés</a>
    <?php } else { ?>
        <a href="kosar.php">Kosár</a>
        <a href="profil.php">Profil</a>
        <?php if ($isAdmin === true) {?>
            <a href="admin.php">Admin</a>
        <?php } ?>
        <a href="kijelentkezes.php">Kijelentkezés</a>
    <?php } ?>

</nav>

<main id="main-allat">
    <?php
    include 'fuggvenyek/allatokkiiratas.php';
    allatKiiratas();
    ?>
</main>

</body>
</html>