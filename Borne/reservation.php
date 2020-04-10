<?php
session_set_cookie_params(['lifetime' => 0,'path' => '/', 'domain' => 'www.aristocrate.me', 'secure' => TRUE, 'httponly' => TRUE, 'samesite' => 'strict']);
session_start();
require_once(__DIR__ . '/lib/utilities.php');
include_once (__DIR__ . '/includes/basic.php');

$pusher->push();

$managerFilm = new FilmManagerPDO($db);
$managerSeance = new SeanceManagerPDO($db);
$managerTarif = new TarifManagerPDO($db);
$managerBillet = new BilletManagerPDO($db);
$managerSalle = new SalleManagerPDO($db);
$managerPlace = new PlaceManagerPDO($db);

/**
 * @param $idSeance
 * @param $idSalle
 * @param $db
 * @return int
 */


if (isset($_GET['id'])){
    $checkFilm = $managerFilm->selectFilm($_GET['id']);

    if($filmInfo = $checkFilm->fetch()){
        if(!($filmInfo['Status'] == "Diffuse" || $filmInfo['Status'] == "Prochainement" || $filmInfo['Status'] == "Evenement")){
            header ("Location: /reservation.php?error=2");
        }
    }
    else{
        header ("Location: /index.php");
    }
}

if(isset($_POST['filmchoice'])){
    $_SESSION['idFilmReservation'] = $_POST['film'];
    $filmReq = $managerFilm->selectFilm($_POST['film']);
    $filmInfo = $filmReq->fetch();
}

if(isset($_POST['daychoice'])){
    $_SESSION['dayFilmReservation'] = $_POST['day'];
    $reqTime = $managerSeance->selectDaySeances($_SESSION['idFilmReservation'],$_SESSION['dayFilmReservation']);
}


if(isset($_POST['timechoice'])){

    $_SESSION['idSeance'] = $_POST['time'];

    $reqSeance = $managerSeance->selectSeanceID($_POST['time']);

    if($seanceData = $reqSeance->fetch()){
        $_SESSION['idFilmReservation'] = $seanceData['IDFilm'];
        $_SESSION['timeFilmReservation'] = $seanceData['Horaire'];
        $_SESSION['dayFilmReservation'] = $seanceData['Date'];
        $_SESSION['salleFilmReservation'] = $seanceData['IDSalle'];
    }
    else{
        header('Location: reservation.php?error=4');
    }
}

if(isset($_POST['placechoice'])){
    if($_POST['token'] == $_SESSION['token']) {
        if (!$seanceInfo = $managerSeance->selectSeance($_SESSION['idFilmReservation'], $_SESSION['salleFilmReservation'], $_SESSION['dayFilmReservation'], $_SESSION['timeFilmReservation'])->fetch()) {
            header('Location: reservation.php?error=5');
        }

        $reqAllTarif = $managerTarif->selectAllTarif();
        $i = 0;
        $nombreDePlace = 0;
        $checkTarif = false;
        $total = 0;
        while ($tarifInfo = $reqAllTarif->fetch()) {
            $facture[$i][0] = $tarifInfo['ID'];
            $tarifNbr = 'tarif' . $tarifInfo['ID'];
            $facture[$i][1] = $_POST[$tarifNbr];
            $nombreDePlace += intval($_POST[$tarifNbr]);
            $total += intval($_POST[$tarifNbr]) * $tarifInfo['Prix'];
            if ($_POST[$tarifNbr] > 0) {
                $checkTarif = true;
            }
            $i++;
        }

        $salleInfo = $managerSalle->selectSalle($_SESSION['salleFilmReservation'])->fetch();

        if ($salleInfo['NbrPlaces'] - $nombreDePlace < 0) {
            header('Location: reservation.php?error=3');
        }
        if (!$checkTarif) {
            header('Location: reservation.php?error=1');
        }

        $_SESSION["billet"] = Array(
            "idSeance" => $seanceInfo['IDSeance'],
            "idFilm" => $_SESSION['idFilmReservation'],
            "idSalle" => $_SESSION['salleFilmReservation'],
            "date" => $_SESSION['dayFilmReservation'],
            "horaire" => $_SESSION['timeFilmReservation'],
            "facture" => $facture,
            "total" => $total,
            "paye" => 0,
        );
    }
    else{
        $errorMessages[] = JETON_EXPIRE;
    }
}

if(isset($_POST['payerchoice'])){
    if($_POST['token'] == $_SESSION['token']) {
        $_SESSION['billet']['paye'] = 1;
        $billet = new Billet(array(
            "idSeance" => $_SESSION['billet']['idSeance'],
            "idUser" => "0"
        ));

        $managerBillet->add($billet);
        $lastId = $managerBillet->getLastInsertId();

        for ($i = 0; $i < sizeof($_SESSION['billet']['facture']); $i++) {
            $place = new Place(array(
                "idBillet" => $lastId,
                "idTarif" => $_SESSION['billet']['facture'][$i][0],
                "quantite" => $_SESSION['billet']['facture'][$i][1]
            ));

            if ($_SESSION['billet']['facture'][$i][1] > 0) {
                $managerPlace->add($place);
            }
        }
        unset($_SESSION['token']);
        header ("Refresh: 8;URL=/");
    }
    else{
        $errorMessages[] = JETON_EXPIRE;
    }
}

if(isset($_GET['error'])){
    if($_GET['error'] == 1){
        $errorMessages[] = "Sélectionner au moins une place.";
    }
    if($_GET['error'] == 2){
        $errorMessages[] = "Ce film n'est pas diffusé.";
    }
    if($_GET['error'] == 3){
        $errorMessages[] = "Il n'y a pas assez de place disponnible.";
    }
    if($_GET['error'] == 4){
        $errorMessages[] = "Cette séance n'est pas disponible";
    }
    if($_GET['error'] == 5){
        $errorMessages[] = "Une erreur est survenue.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <?php include ("includes/head.php"); ?>
</head>
<body>
<!-- Navbar -->
<?php include ("includes/navbar.php") ?>
<div class="container-fluid m-0 p-0 height-80-vh">
    <div class="col-12 offset-md-2 col-md-8 offset-lg-3 col-lg-6 p-0 pt-5 text-center">
        <span class="display-4">Réservation</span>
        <div class="progress mt-4"style=" height: 10px">
            <div class="progress-bar bg-danger" style="<?php

            if(isset($_POST['filmchoice'])){
                echo "width: 20%";
            }
            elseif(isset($_POST['daychoice'])){
                echo "width: 40%";
            }
            elseif(isset($_POST['timechoice'])){
                echo "width: 60%";
            }
            elseif(isset($_POST['placechoice'])){
                echo "width: 80%";
            }
            elseif(isset($_POST['payerchoice'])){
                echo "width: 100%";
            }
            else{
                echo "width: 1%";
            }

            ?>; height: 10px"></div>
        </div>
    </div>
    <div class="col-12 offset-md-2 col-md-8 offset-lg-3 col-lg-6 border rounded-lg mt-4 mb-5 p-4">
        <?php
        if (isset($_POST['payerchoice'])) {
            $filmInfo = $managerFilm->selectFilm($_SESSION['billet']['idFilm'])->fetch();
            ?>
            <!-- Billet -->
            <!-- On génère le billet ici -->
            <div class="col-12 m-0 p-0 d-flex justify-content-between">
                <div class="col-8 m-0 p-0 d-flex align-items-center height-100">
                    <span class="h3">Votre billet :</span>
                </div>
                <div class="col-4 m-0 p-0 d-flex justify-content-end">
                    <img class="img-fluid height-100" src="/images/style/logo_aristocrate.png" alt="logo_aristocrate">
                </div>
            </div>
            <div class="col-12 p-3 mt-3 m-0 d-flex border rounded">
                <div class="col-4 col-lg-3 m-0 p-0">
                    <img class="img-fluid" src="<?php echo htmlspecialchars($filmInfo['PhotosPath'])?>" alt="Photo_film">
                </div>
                <div class="col-8 col-lg-9">
                    <div class="col-12 m-0 p-0">
                        <span>Film: <?php echo htmlspecialchars($filmInfo['Nom'])?></span>
                    </div>
                    <div class="col-12 m-0 p-0">
                        <span>Durée: <?php echo htmlspecialchars($filmInfo['Duree'])?></span>
                    </div>
                    <div class="col-12 m-0 p-0 pt-3">
                        <span class="h3">Salle : <?php echo htmlspecialchars($_SESSION['billet']['idSalle'])?></span>
                    </div>
                    <div class="col-12 m-0 p-0 pt-3">
                    <?php
                    for($i=0; $i < sizeof($_SESSION['billet']['facture']); $i++){
                        if($_SESSION['billet']['facture'][$i][1] > 0) {
                            $nomTarif = $managerTarif->selectTarif($_SESSION['billet']['facture'][$i][0])->fetch()['Nom'];
                            ?>
                            <div class="col-12 m-0 p-0 pt-1">
                                <span><?php echo htmlspecialchars($nomTarif." x ". $_SESSION['billet']['facture'][$i][1]) ?></span>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    </div>
                </div>
            </div>
            <div class="col-12 m-0 p-0 mt-4">
                <div class="col-12 m-0 p-0 mt-2">
                    <span class="h3">Facture :</span>
                </div>
                <div class="col-12 m-0 p-0 pt-3 pb-3 mb-3 border-bottom">
                    <?php
                    for($i=0; $i < sizeof($_SESSION['billet']['facture']); $i++){
                        if($_SESSION['billet']['facture'][$i][1] > 0) {
                            $tarifInfo = $managerTarif->selectTarif($_SESSION['billet']['facture'][$i][0])->fetch();
                            ?>
                            <div class="col-12 m-0 p-0 pt-1">
                                <span><?php echo htmlspecialchars(
                                    $tarifInfo['Nom'].": ".
                                    $_SESSION['billet']['facture'][$i][1] ." * ".
                                    $tarifInfo['Prix']." = ".
                                    $_SESSION['billet']['facture'][$i][1] * $tarifInfo['Prix'])?></span>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <div class="col-12 m-0 p-0">
                    <span>Total: <?php echo htmlspecialchars($_SESSION['billet']['total'])?></span>
                </div>
                <div class="col-12 mt-5 m-0 p-0 text-center text-justify">
                    <span class="h3"> Bonne séance ! N'oubliez pas de récupérer votre billet !</span>
                </div>
                <div class="col-12 mt-5 m-0 p-0">
                    <span class="text-muted"><small>Ce billet est ni échangeable ni remboursable. Pour toutes questions veuillez contacter le service client.</small></span>
                </div>
            </div>



            <?php
        }
        elseif (isset($_POST['placechoice'])) {
            $filmInfo = $managerFilm->selectFilm($_SESSION['idFilmReservation'])->fetch();

            ?>
            <!-- Payement -->
            <form action="/reservation.php" enctype="multipart/form-data" method="post">
                <div class="container-fluid m-0 p-0 mb-4">
                    <span class="h3">Récapitulatif:</span>
                </div>
                <div>
                    <div class="mb-2">
                        <span>Film: <?php echo htmlspecialchars($filmInfo['Nom'])?></span>
                    </div>
                    <div class="mb-5">
                        <span>
                            Séance du
                            <?php echo htmlspecialchars(date('d-m-Y', strtotime($seanceInfo['Date']))); ?>
                            à
                            <?php echo htmlspecialchars(date('H:i', strtotime($seanceInfo['Horaire']))); ?>
                        </span>
                    </div>
                    <?php
                    $total = 0;
                    for ($i=0; $i<sizeof($facture); $i++){
                        $reqTarif = $managerTarif->selectTarif($facture[$i][0]);
                        $tarifInfo = $reqTarif->fetch();
                        if($facture[$i][1] != 0){
                            $total += $facture[$i][1] * $tarifInfo['Prix'];
                            ?>
                            <div class="mb-2">
                                <span>
                                    <?php echo htmlspecialchars($tarifInfo['Nom'])?> :
                                    <?php echo htmlspecialchars($facture[$i][1])?> *
                                    <?php echo htmlspecialchars($tarifInfo['Prix'])?> &emsp; = &emsp;
                                    <?php echo htmlspecialchars($facture[$i][1] * $tarifInfo['Prix']) ?>€
                                </span>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <div class="border-top pt-2">
                    <span>Total: <?php echo htmlspecialchars($total) ?>€</span>
                </div>
                <input type="hidden" name="token" value="<?php if(isset($_SESSION['token'])) echo $_SESSION['token']; ?>" />
                <div class="container-fluid p-0 m-0 mt-4 pt-2 d-flex">
                    <div class="col-12 col-md-6 pr-2 m-0 p-0">
                        <a href="reservation.php" class="btn btn-light btn-block m-0">Recommencer</a>
                    </div>
                    <div class="col-12 col-md-6 pl-2 p-0 m-0">
                        <button type="submit" class="btn btn-danger btn-block m-0" name="payerchoice">Payer</button>
                    </div>
                </div>
            </form>
            <?php
        }
        else if (isset($_POST['timechoice'])) {
            ?>
            <!-- Nombre de place -->
            <form action="/reservation.php" enctype="multipart/form-data" method="post">
                <div class="container-fluid m-0 p-0 mb-4">
                    <div class="col-12 m-0 p-0 text-right">
                        <span>Nombre de places restantes : <?php echo htmlspecialchars(nombrePlaceRestant($_SESSION['idSeance'],$_SESSION['salleFilmReservation'], $db)) ?></span>
                    </div>
                    <div class="col-12 m-0 p-0">
                        <span>Choix de vos places :</span>
                    </div>
                </div>
                <?php

                $reqTarif = $managerTarif->selectAllTarif();

                while ($tarifInfo = $reqTarif->fetch()){
                    ?>
                    <div class="form-group d-flex align-items-center">
                        <label for="tarif<?php echo htmlspecialchars($tarifInfo['ID']) ?>" class="col-5 col-md-7 col-lg-5 col-xl-4 m-0 p-0 pr-4">
                            <?php echo htmlspecialchars($tarifInfo['Nom'])?>
                            (<?php echo htmlspecialchars($tarifInfo['Prix']) ?>€) :
                        </label>
                        <input type="number" class="form-control width-30" id="tarif<?php echo htmlspecialchars($tarifInfo['ID'])?>" name="tarif<?php echo htmlspecialchars($tarifInfo['ID']) ?>" min="0" placeholder="0">
                    </div>
                    <?php
                }
                ?>
                <input type="hidden" name="token" id="token" value="<?php if(isset($_SESSION['token'])) echo $_SESSION['token']; ?>" />
                <div class="container-fluid p-0 m-0 mt-4 pt-2 d-flex">
                    <div class="col-12 col-md-6 pr-2 m-0 p-0">
                        <a href="reservation.php" class="btn btn-light btn-block m-0">Recommencer</a>
                    </div>
                    <div class="col-12 col-md-6 pl-2 p-0 m-0">
                        <button type="submit" class="btn btn-danger btn-block m-0" name="placechoice">Suivant</button>
                    </div>
                </div>
            </form>
            <?php
        }
        elseif (isset($_POST['daychoice'])){
            ?>
            <!-- Choix de l'heure -->
            <form action="/reservation.php" enctype="multipart/form-data" method="post">
                <div class="form-group">
                    <label for="timechoice">Choix de l'heure :</label>
                    <select class="form-control" id="timechoice" name="time">
                        <?php
                        $reqTime = $managerSeance->selectDaySeances($_SESSION['idFilmReservation'],$_SESSION['dayFilmReservation']);

                        while($seanceInfo = $reqTime->fetch()){
                            $dayCheck = true;
                            $placeRestantes = nombrePlaceRestant($seanceInfo['IDSeance'], $seanceInfo['IDSalle'],$db);
                            ?>
                            <option name="time" value="<?php echo htmlspecialchars($seanceInfo['IDSeance']) ?>" <?php
                            if($placeRestantes <= 0){
                                echo "disabled";
                            }
                            elseif(isset($_SESSION['timeFilmReservation']) && $_SESSION['timeFilmReservation'] == $seanceInfo['Horaire'])
                                echo "selected";
                            ?>>
                                <?php if(isset($seanceInfo['Horaire'])) echo htmlspecialchars(date('H : i', strtotime($seanceInfo['Horaire']))) ?>
                                &nbsp; il reste <?php echo htmlspecialchars($placeRestantes) ?> place(s)
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="container-fluid p-0 m-0 d-flex">
                    <div class="col-12 col-md-6 pr-2 m-0 p-0">
                        <a href="reservation.php" class="btn btn-light btn-block m-0">Recommencer</a>
                    </div>
                    <div class="col-12 col-md-6 pl-2 p-0 m-0">
                        <button type="submit" class="btn btn-danger btn-block m-0" name="timechoice">Suivant</button>
                    </div>
                </div>
            </form>
            <?php
        }
        elseif(isset($_POST['filmchoice'])){
            ?>
            <!-- Choix du jour -->
            <form action="/reservation.php" enctype="multipart/form-data" method="post">
                <div class="form-group">
                    <label for="daychoice">Choix du jour :</label>
                    <select class="form-control" id="daychoice" name="day">
                    <?php
                    $dayCheck = false;

                    for($j=0;$j<7;$j++){
                        $reqDay = $managerSeance->selectDaySeances(htmlspecialchars($filmInfo['ID']),date("Y-m-d",mktime(0,0,0, date("m"), date("d")+$j, date("Y"))));
                        if($seanceInfo = $reqDay->fetch()){
                            $dayCheck = true;
                            ?>
                            <option name="day" value="<?php echo htmlspecialchars($seanceInfo['Date']) ?>" <?php
                            if(isset($_SESSION['dayFilmReservation']) && $_SESSION['dayFilmReservation'] == $seanceInfo['Date'])
                                echo "selected";
                            ?>>
                                <?php if(isset($seanceInfo['Date'])) echo htmlspecialchars(date('d - m - Y', strtotime($seanceInfo['Date']))) ?>
                            </option>
                            <?php
                        }

                    }

                    ?>
                    </select>
                    <?php
                    if (!$dayCheck){
                        ?>
                        <div class="mt-3">
                            <span class="text-muted h6">Il n'y a aucune séance pour les 7 prochains jours à venir.</span>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="container-fluid p-0 m-0 d-flex">
                    <?php
                    if (!$dayCheck){
                        ?>
                        <a href="reservation.php" class="col-12 btn btn-light btn-block p-2">Recommencer</a>
                        <?php
                    }else{
                        ?>
                        <div class="col-12 col-md-6 pr-2 m-0 p-0">
                            <a href="reservation.php" class="btn btn-light btn-block m-0">Recommencer</a>
                        </div>
                        <div class="col-12 col-md-6 pl-2 p-0 m-0">
                            <button type="submit" class="btn btn-danger btn-block m-0" name="daychoice">Suivant</button>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </form>
            <?php
        }
        else{
            ?>
            <!-- Default -->
            <form action="/reservation.php" enctype="multipart/form-data" method="post">
                <div class="form-group">
                    <label for="filmchoice">Choix du film :</label>
                    <select class="form-control" id="filmchoice" name="film">

                        <?php
                        $reqFilm = $managerFilm->selectStatusFilm('Diffuse');
                        $selectedCheck = false;
                        while ($filmInfo = $reqFilm->fetch()){
                            ?>
                            <option name="film" value="<?php echo htmlspecialchars($filmInfo['ID']) ?>"
                            <?php
                                if (isset($_GET['id']) && $_GET['id'] == $filmInfo['ID']){
                                    echo "selected";
                                    $selectedCheck = true;
                                }elseif (isset($_SESSION['idFilmReservation']) && $_SESSION['idFilmReservation'] == $filmInfo['ID'] && $selectedCheck == false){
                                    echo "selected";
                                }
                            ?>
                            >
                                <?php echo htmlspecialchars($filmInfo['Nom'])?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-danger btn-block" name="filmchoice">Suivant</button>
            </form>
            <?php
        }
        ?>
    </div>
</div>




<?php include ("includes/footer.php"); ?>

<?php include ("includes/scripts.php"); ?>

</body>
</html>