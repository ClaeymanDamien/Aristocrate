<?php
session_set_cookie_params(['lifetime' => 0,'path' => '/', 'domain' => 'www.aristocrate.me', 'secure' => TRUE, 'httponly' => TRUE, 'samesite' => 'strict']);
session_start();
require_once(__DIR__ . '/lib/utilities.php');
include_once (__DIR__ . '/includes/basic.php');

$pusher->img("/images/style/offre3.png");
$pusher->push();

$managerFilm = new FilmManagerPDO($db);

?>

<!doctype html>
<html lang="en">
<head>
    <?php include ("/includes/head.php"); ?>
    <!-- Slick CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick-theme.min.css" rel="stylesheet" />
</head>
<body>
    <!-- Navbar -->
    <?php include ("/includes/navbar.php") ?>
    <div class="container-fluid m-0 p-0">
        <div class="alert alert-primary alert-dismissible m-0">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <div class="text-center">En raison du COVID-19 notre cinéma est pour le moment fermé. Nous vous tiendrons informés dans les jours à venir.</div>
        </div>
    </div>
    <div class="container-fluid d-flex justify-content-center">
        <div class="col-lg-8 col-12">

            <!-- Caroussel -->
            <div class="main_info height-400 mt-3 pr-2 pl-2">
                <div class="height-400">
                    <img src="/images/affiches/banière1.jpg" class="img-slider" alt="">
                </div>

                <div class="height-400">
                    <img src="/images/affiches/banière2.jpg" class="img-slider" alt="">
                </div>

                <div class="height-400">
                    <img src="/images/affiches/banière3.jpg" class="img-slider" alt="">
                </div>
            </div>

            <!-- A l'affiche -->
            <div class="col-12 p-0 m-0">
                <div class="d-flex m-2 justify-content-center border-bottom">
                    <h2 class="display-4">A l'affiche</h2>
                </div>
                <div class="col-12 p-0 m-0 d-flex justify-content-center flex-wrap">
                    <?php
                    $allFilm = $managerFilm->selectIndexFilm('Diffuse');

                    while($filmInfo = $allFilm->fetch())
                    {
                        ?>
                        <a href='/film.php?id=<?php if(isset($filmInfo['ID'])) echo htmlspecialchars($filmInfo['ID']) ?>' class="text_decoration_none affiche-box affiche-anim col-6 col-lg-4 col-xl-3 bg-white p-2 m-0 mb-3">
                            <div >
                                <img alt="affiche de film" class="img-fluid" src="<?php if(isset($filmInfo['PhotosPath'])) echo htmlspecialchars($filmInfo['PhotosPath']) ?>">
                            </div>
                            <div class="d-flex justify-content-center align-items-center p-2">
                                <span><?php if(isset($filmInfo['Nom'])) echo htmlspecialchars($filmInfo['Nom']) ?></span>
                            </div>
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <div class="d-flex mt-2 justify-content-end">
                    <a href="/films-a-l-affiche.php" class="text-primary">Tous les films à l'affiche ></a>
                </div>
            </div>

            <!-- Prochainement -->
            <div class="col-12 p-0 m-0">
                <div class="d-flex m-2 justify-content-center border-bottom">
                    <h2 class="display-4">Prochainement</h2>
                </div>
                <div class="col-12 p-0 m-0 d-flex justify-content-center flex-wrap">
                    <?php
                    $allFilm = $managerFilm->selectIndexFilm('Prochainement');
                    
                    while($filmInfo = $allFilm->fetch())
                    {
                        ?>
                        <a href='/film.php?id=<?php if(isset($filmInfo['ID'])) echo htmlspecialchars($filmInfo['ID']) ?>' class="text_decoration_none affiche-box affiche-anim col-6 col-lg-4 col-xl-3 bg-white p-2 m-0 mb-3">
                            <div >
                                <img alt="affiche de film" class="img-fluid" src="<?php if(isset($filmInfo['PhotosPath'])) echo htmlspecialchars($filmInfo['PhotosPath']) ?>">
                            </div>
                            <div class="d-flex justify-content-center align-items-center p-2">
                                <span><?php if(isset($filmInfo['Nom'])) echo htmlspecialchars($filmInfo['Nom']) ?></span>
                            </div>
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <div class="d-flex mt-2 justify-content-end">
                    <a href="/films-a-l-affiche.php#prochainement" class="text-primary">Tous les prochains films ></a>
                </div>
            </div>

            <!-- Nos offres & actualité -->
            <div class="col-12 m-0 p-0">
                <div class="d-flex m-2 justify-content-center border-bottom">
                    <h2 class="display-4">Nos offres</h2>
                </div>
                <div class="col-12 m-0 p-0 d-flex">
                    <div class="col-12 p-2">
                        <div class="img-responsive">
                            <img src="images/style/offre3.png" alt="carte_pass" class="img-fluid">
                        </div>
                    </div>
                </div>
                <div class="d-flex mt-2 justify-content-end">
                    <a href="/prochainement.php" class="text-primary">Nos offres ></a>
                </div>
            </div>

            <!-- Les évènements -->
            <div class="col-12 p-0 m-0 mb-5">
                <div class="d-flex m-2 justify-content-center border-bottom">
                    <h2 class="display-4">Évènements</h2>
                </div>
                <div class="col-12 p-0 m-0 d-flex justify-content-center flex-wrap">
                    <?php
                    $allFilm = $managerFilm->selectIndexFilm('Evenement');

                    while($filmInfo = $allFilm->fetch())
                    {
                        ?>
                        <a href='/film.php?id=<?php if(isset($filmInfo['ID'])) echo htmlspecialchars($filmInfo['ID']) ?>' class="text_decoration_none affiche-box affiche-anim col-6 col-lg-4 col-xl-3 bg-white p-2 m-0 mb-3">
                            <div >
                                <img alt="affiche de film" class="img-fluid" src="<?php if(isset($filmInfo['PhotosPath'])) echo htmlspecialchars($filmInfo['PhotosPath']) ?>">
                            </div>
                            <div class="d-flex justify-content-center align-items-center p-2">
                                <span><?php if(isset($filmInfo['Nom'])) echo htmlspecialchars($filmInfo['Nom']) ?></span>
                            </div>
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <div class="d-flex mt-2 justify-content-end">
                    <a href="/films-a-l-affiche.php#evenement" class="text-primary">Tous les évènements ></a>
                </div>
            </div>

        </div>
    </div>

    <?php include ("includes/footer.php"); ?>

    <?php include ("includes/scripts.php"); ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.3/jquery.min.js" integrity="sha384-ugqypGWrzPLdx2zEQTF17cVktjb01piRKaDNnbYGRSxyEoeAm+MKZVtbDUYjxfZ6" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.js" integrity="sha384-ZULtytbCZdmL8PeKalcAKnseGOqrCiPBi3DiB7s4JJmS8gjSbfw0w8SPKpt9WemG" crossorigin="anonymous"></script>
    <script src="/script/script.js"></script>
</body>
</html>
