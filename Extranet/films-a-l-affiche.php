<?php
session_set_cookie_params(['lifetime' => 0,'path' => '/', 'domain' => 'www.aristocrate.me', 'secure' => TRUE, 'httponly' => TRUE, 'samesite' => 'strict']);
session_start();
require_once(__DIR__ . '/lib/utilities.php');
include_once (__DIR__ . '/includes/basic.php');

$pusher->push();

$managerFilm = new FilmManagerPDO($db);

?>

<!doctype html>
<html lang="en">
<head>
    <?php include ("includes/head.php"); ?>
</head>
<body>
<!-- Navbar -->
<?php include ("includes/navbar.php") ?>

<div class="container-fluid d-flex justify-content-center">
    <div class="col-lg-8 col-12">
        <div class="container-fluid border-bottom text-center p-2 mt-2">
            <h1>Les films à l'affiche</h1>
        </div>
        <!-- A l'affiche -->
        <div class="col-12 p-0 m-0">
            <div class="d-flex m-4 mt-5 justify-content-center">
                <h2>Toujours à l'affiche</h2>
            </div>
            <div class="col-12 p-0 m-0 d-flex justify-content-center flex-wrap">
                <?php
                $allFilm = $managerFilm->selectStatusFilm('Diffuse');

                while($filmInfo = $allFilm->fetch())
                {
                    ?>
                    <a href='/film.php?id=<?php if(isset($filmInfo['ID'])) echo htmlspecialchars($filmInfo['ID']) ?>' class="text_decoration_none affiche-box affiche-anim col-6 col-lg-4 col-xl-3 bg-white p-2 m-0 mb-3">
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

        <!-- Prochainement -->
        <div class="col-12 p-0 m-0" id="prochainement">
            <div class="d-flex m-2 m-4 mt-5 justify-content-center">
                <h2>Prochainement</h2>
            </div>
            <div class="col-12 p-0 m-0 d-flex justify-content-center flex-wrap">
                <?php
                $allFilm = $managerFilm->selectStatusFilm('Prochainement');

                while($filmInfo = $allFilm->fetch())
                {
                    ?>
                    <a href='/film.php?id=<?php if(isset($filmInfo['ID'])) echo htmlspecialchars($filmInfo['ID']) ?>' class="text_decoration_none affiche-box affiche-anim col-6 col-lg-4 col-xl-3 bg-white p-2 m-0 mb-3">
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

        <!-- Les évènements -->
        <div class="col-12 p-0 m-0 mb-5" id="evenement">
            <div class="d-flex m-4 mt-5 justify-content-center">
                <h2>Évènements</h2>
            </div>
            <div class="col-12 p-0 m-0 d-flex justify-content-center flex-wrap">
                <?php
                $allFilm = $managerFilm->selectIndexFilm('Evenement');

                while($filmInfo = $allFilm->fetch())
                {
                    ?>
                    <a href='/film.php?id=<?php if(isset($filmInfo['ID'])) echo htmlspecialchars($filmInfo['ID']) ?>' class="text_decoration_none affiche-box affiche-anim col-6 col-lg-4 col-xl-3 bg-white p-2 m-0 mb-3">
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

</body>
</html>
