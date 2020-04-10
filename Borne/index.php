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
    <?php include ("includes/head.php"); ?>
    <!-- Slick CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick-theme.min.css" rel="stylesheet" />
</head>
<body>
    <!-- Navbar -->
    <?php include ("includes/navbar.php") ?>
    <div class="container-fluid d-flex justify-content-center">
        <div class="col-lg-8 col-12 pb-5 pt-5">
            <!-- A l'affiche -->
            <div class="col-12 p-0 m-0">
                <div class="col-12 p-0 m-0 d-flex justify-content-center flex-wrap">
                    <?php
                    $allFilm = $managerFilm->selectStatusFilm('Diffuse');

                    while($filmInfo = $allFilm->fetch())
                    {
                        ?>
                        <a href='/film.php?id=<?php if(isset($filmInfo['ID'])) echo htmlspecialchars($filmInfo['ID']) ?>' class="text_decoration_none affiche-box affiche-anim col-6 col-lg-4 col-xl-2 bg-white p-2 m-0 mb-3">
                            <div >
                                <img class="img-fluid" src="<?php if(isset($filmInfo['PhotosPath'])) echo htmlspecialchars($filmInfo['PhotosPath']) ?>">
                            </div>
                            <div class="d-flex justify-content-center align-items-center p-2">
                                <span><?php if(isset($filmInfo['Nom'])) echo htmlspecialchars($filmInfo['Nom']) ?></span>
                            </div>
                        </a>
                        <?php
                    }
                    ?>
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
