<?php
session_set_cookie_params(['lifetime' => 0,'path' => '/', 'domain' => 'www.aristocrate.me', 'secure' => TRUE, 'httponly' => TRUE, 'samesite' => 'strict']);
session_start();
require_once(__DIR__ . '/lib/utilities.php');
include_once (__DIR__ . '/includes/basic.php');

if(empty($userSession) || !($userSession->getStatus() == "admin" || $userSession->getStatus() == "manager")){
    header('Location: /');
}

$managerFilm = new FilmManagerPDO($dbExtranet);
$managerSalle = new SalleManagerPDO($dbExtranet);
$managerSeance = new SeanceManagerPDO($dbExtranet);

$id = isset($_GET['id']) ? $_GET['id'] : NULL;

if(isset($id)) {
    $reqFilm = $managerFilm->selectFilm($id);
    $filmInfo = $reqFilm->fetch();
    if ($filmInfo <= 0 || empty($id) || !($filmInfo['Status'] == "Diffuse" || $filmInfo['Status'] == "Prochainement")) {
        $errorMessages[] = "ID invalide";
        $idTest = false;
    }
    else{
        $idTest = true;
    }
}

if(isset($_POST['addMovieShow'])){
    if($_POST['token'] == $_SESSION['token']) {

        $seance = new Seance(array(
                "idFilm" => $_POST['idFilm'],
                "idSalle" => $_POST['idSalle'],
                "date" => $_POST['date'],
                "horaire" => $_POST['horaire']
            ));
        $reqSalle = $managerSalle->selectSalle($seance->getIdSalle());
        $salleInfo = $reqSalle->fetch();
        $reqFilm = $managerFilm->selectFilm($id);
        $filmInfo = $reqFilm->fetch();

        $valid = true;

        if (!$seance->isValid())
        {
            $valid = false;
            $errorMessages[] = "Une erreur est survenue";
        }
        elseif($salleInfo <= 0){
            $errorMessages[] = "Numéro de salle invalide";
            $valid = false;
        }
        elseif ($filmInfo <= 0 || empty($id) || !($filmInfo['Status'] == "Diffuse" || $filmInfo['Status'] == "Prochainement")) {
            $errorMessages[] = "ID du film invalide";
            $valid = false;
        }

        if ($valid){
            $managerSeance->addSeance($seance);
            $_POST = array();
            $successMessages[] = "La séance a été ajouté.";
        }
        else{
            $errors = $seance->getErrors();
        }

        if (isset($errors)){
            if (in_array(Seance::INVALID_HOUR, $errors)){
                $errorMessages[] = "Heure invalide";
            }
            if (in_array(Seance::INVALID_DATE, $errors)){
                $errorMessages[] = "Date invalide";
            }
            if (in_array(Seance::INVALID_ID_FILM, $errors)){
                $errorMessages[] = "Film invalide";
            }
            if (in_array(Seance::INVALID_ID_SALLE, $errors)){
                $errorMessages[] = "Salle invalide";
            }
        }
    }
    else{
        $errorMessages[] = JETON_EXPIRE;
    }
}

$pusher->push();
?>

<!doctype html>
<html lang="en">
<head>
    <?php include ("includes/head.php"); ?>
    <link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.6/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<!-- Navbar -->
<?php include ("includes/navbar.php") ?>


<div class="d-flex justify-content-center align-items-center mt-4 mb-4 mt-4 p-0">
    <div class="col-12 col-md-8 col-lg-6">
        <a href="index.php" class="p-0 m-0 btn btn-link text-dark">
            <img class="mr-2" src="/images/style/ic_arrow_back_black_24dp.png" alt="return_row">admin</a>
    </div>
</div>
<?php
if (isset($id) && $idTest){
    ?>
    <div class="d-flex flex-column justify-content-center align-items-center pt-4 pb-5 mb-5">
        <div class="pb-3">
            <span class="display-4">Ajouter une séance</span>
        </div>
        <div class=" col-12 col-md-6 col-lg-5">
            <div class="border p-2">
                <div class="modal-body">
                    <form action="<?php echo getURI() ?>" enctype="multipart/form-data" method="post">
                        <div class="form-group">
                            <label for="salle">Salle :</label>
                            <select class="custom-select" id="salle" name="idSalle">
                                <?php
                                    $reqSalle = $managerSalle->selectAllSalle();
                                    while ($salleInfo = $reqSalle->fetch()){
                                        ?>
                                            <option value="<?php echo $salleInfo['IDSalle'];?>"><?php echo $salleInfo['IDSalle'];?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group pr-3">
                            <input class="border border-top-0 border-right-0 border-bottom-0 rounded-0" name="date" id="datepicker" placeholder="Date: Année-Mois-Jour" />
                        </div>
                        <div class="form-group pr-1">
                            <input class="border border-top-0 border-right-0 border-bottom-0 rounded-0" name="horaire" type="time" id="timepicker" placeholder="Heure: Heure:Minutes" />
                        </div>
                        <input type="hidden" name="token" id="token" value="<?php if(isset($_SESSION['token'])) echo $_SESSION['token']; ?>" />
                        <input type="hidden" name="idFilm" value="<?php if(isset($id)) echo $id; ?>" />
                        <button type="submit" class="btn btn-dark btn-lg btn-block" name="addMovieShow">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
}
else{
    ?>
    <div class="d-flex justify-content-center align-items-center flex-column">
        <div class="col-12 col-md-8 col-lg-6">
            <h1 class="display-3 mb-3"> Choisir le film à ajouter</h1>
            <input class="form-control mb-4" id="myInput" type="text" placeholder="Search..">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Film</th>
                    <th>Réalisateur</th>
                    <th>Satuts</th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="myTable">
                <?php
                $allFilm = $managerFilm->selectStatusFilm("Diffuse");
                while($filmInfo = $allFilm->fetch())
                {
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($filmInfo['Nom'])?></td>
                        <td><?php echo htmlspecialchars($filmInfo['Realisateur'])?></td>
                        <td><?php echo htmlspecialchars($filmInfo['Status'])?></td>
                        <td class="d-flex justify-content-around">
                            <a href="addmovieshow.php?id=<?php echo $filmInfo['ID']?>">Ajouter une séance</a>
                        </td>
                    </tr>
                    <?php
                }
                $allFilm = $managerFilm->selectStatusFilm("Prochainement");
                while($filmInfo = $allFilm->fetch())
                {
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($filmInfo['Nom'])?></td>
                        <td><?php echo htmlspecialchars($filmInfo['Realisateur'])?></td>
                        <td><?php echo htmlspecialchars($filmInfo['Status'])?></td>
                        <td class="d-flex justify-content-around">
                            <a href="addmovieshow.php?id=<?php echo $filmInfo['ID']?>">Ajouter une séance</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}
?>


<?php
    include ("includes/footer.php");
    include ("includes/scripts.php");
    include("scripts/searchBootstrap.html");
    include ("scripts/clockdatepicker.html");
?>
</body>
</html>

