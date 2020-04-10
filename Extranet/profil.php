<?php
session_set_cookie_params(['lifetime' => 0,'path' => '/', 'domain' => 'www.aristocrate.me', 'secure' => TRUE, 'httponly' => TRUE, 'samesite' => 'strict']);
session_start();
require_once(__DIR__ . '/lib/utilities.php');
include_once (__DIR__ . '/includes/basic.php');

$pusher->push();

if(empty($userSession) || empty($_SESSION))
{
    header('Location: index.php?error=login');
}

$managerBillet = new BilletManagerPDO($db);
$managerPlace = new PlaceManagerPDO($db);
$managerFilm = new FilmManagerPDO($db);
$managerSeance = new SeanceManagerPDO($db);
$managerTarif = new TarifManagerPDO($db);

$reqUser = $manager->selectUser($userSession->getId());
$userData = $reqUser->fetch();

$delete = isset($_GET['delete']) ? $_GET['delete'] : NULL;
$edit = isset($_GET['edit']) ? $_GET['edit'] : NULL;
$userList = isset($_GET['userList']) ? $_GET['userList'] : NULL;


if(isset($_GET['deleted']))
{
    $messages[] = "User is deleted";
}
if(isset($_GET['added']))
{
    $messages[] = "User is added";
}

if(isset($_GET['errors']))
{
    $errorMessage = $_GET['errors'];

    $messages[] = ($errorMessage == "np") ? "Patient is not find" : NULL;
}


/** Delete an User */
if(isset($_POST['deleteSubmit']))
{
    if($_POST['token'] == $_SESSION['token']){
        $User = new User();
        $manager->deleteUser($userData['ID']);
        $_SESSION = array();
        unset($userSession);
        session_destroy();
        header('Location: index.php');
    }
    else{
        $errorMessages[] = JETON_EXPIRE;
    }
}

/** Edit an User */
if(isset($edit))
{

    $UserInfoReq = $manager->selectUser($userData['ID']);
    $UserInfo = $UserInfoReq->fetch();

    if(isset($_POST['editSubmit'])) {
        if ($_POST['token'] == $_SESSION['token']) {

            $User = new User(array(
                'id' => $userData['ID'],
                'fName' => $_POST['name'],
                'lName' => $_POST['surname'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'passwordConfirmed' => $_POST['passwordConfirmed'],
                'status' => $userData['Status'],
            ));

            $valid = true;
            $password = false;
            if (!$User->isValidUpdate()) {
                $valid = false;
            }

            if (!empty($User->getPassword())) {
                $password = true;
                if (!$User->checkSamePassword())
                    $valid = false;
            }


            if ($valid) {
                $manager->updateUser($User);
                $successMessages[] = "La modification a été effectué";
            } else {
                $errors = $User->getErrors();
            }

            if (isset($errors)) {
                if (in_array(User::NOT_SAME_PASSWORD, $errors)) {
                    $errorMessages[] = "Les mots de passe sont différents";
                }
                if (in_array(User::INVALID_EMAIL, $errors)) {
                    $errorMessages[] = "L'email est invalide";
                }
                if (in_array(User::INVALID_F_NAME, $errors)) {
                    $errorMessages[] = "Le prénom est invalide";
                }
                if (in_array(User::INVALID_L_NAME, $errors)) {
                    $errorMessages[] = "Le nom est invalide";
                }
                if ($password) {
                    if (in_array(User::INVALID_PASSWORD, $errors)) {
                        $messages[] = "Password is invalid";
                    }
                }
            }
        }
    }
    else{
        $errorMessages[] = JETON_EXPIRE;
    }
}


?>

<!doctype html>
<html lang="en">
<head>
    <?php include ("includes/head.php"); ?>
</head>
<body>
<?php include ("includes/navbar.php"); ?>

<?php

if (isset($delete))
{
    ?>
    <div class="d-flex justify-content-center align-items-center height-80-vh">
        <div class="col-6">
            <h4 class="p-3">Êtes-vous sûr de vouloir supprimer votre profil?</h4>
            <form action="profil.php" method="post">
                <div class="row">
                    <div class="col-6 pl-3 pr-3">
                        <input type="hidden" name="token" id="token" value="<?php if(isset($_SESSION['token'])) echo $_SESSION['token']; ?>" />
                        <input type="submit" name="deleteSubmit" class="btn btn-block btn-outline-dark" value="Oui">
                    </div>
                    <div class="col-6 pl-3 pr-3">
                        <a href="profil.php" class="btn btn-block btn-dark">Non</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
}
elseif (isset($edit))
{
    ?>

    <div class="d-flex justify-content-center align-items-center p-0 mt-4 mb-4">
        <div class="col-12 col-md-8 col-lg-6">
            <a href="/profil.php" class="p-0 m-0 btn btn-link text-dark">
                <img class="mr-2" src="/images/style/arrow_left.png" alt="return_row">revenir au profil</a>
        </div>
    </div>
    <div class="container-fluid">
        <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
            <h1 class="display-4">Edition du profil</h1>
            <form class="mt-3 mb-3 " action="profil.php?edit" enctype="multipart/form-data" method="post">
                <input class="form-control mb-3" type="text" name="name" value="<?php
                if(isset($_POST['name']))
                    echo htmlspecialchars($_POST['name']);
                elseif (isset($userData['FName']))
                    echo htmlspecialchars($userData['FName']);
                ?>" placeholder="Prénom">
                <input class="form-control mb-3" type="text" name="surname" value="<?php
                if(isset($_POST['surname']))
                    echo htmlspecialchars($_POST['surname']);
                elseif (isset($userData['LName']))
                    echo htmlspecialchars($userData['LName']);
                ?>" placeholder="Nom">
                <input class="form-control mb-3" type="email" name="email" value="<?php
                if(isset($_POST['email']))
                    echo htmlspecialchars($_POST['email']);
                elseif (isset($userData['Email']))
                    echo htmlspecialchars($userData['Email']);
                ?>" placeholder="Adresse mail">
                <input class="form-control mb-3" type="password" name="password" value="<?php
                if(isset($_POST['password']))
                    echo htmlspecialchars($_POST['password']);
                ?>" placeholder="Mot de passe">
                <input class="form-control mb-3" type="password" name="passwordConfirmed" value="<?php
                if(isset($_POST['passwordConfirmed']))
                    echo htmlspecialchars($_POST['passwordConfirmed']);
                ?>" placeholder="Confirmation du mot de passe">
                <input type="hidden" name="token" id="token" value="<?php if(isset($_SESSION['token'])) echo $_SESSION['token']; ?>" />
                <input class="btn btn-outline-dark btn-block" type="submit" name="editSubmit" value="Modifier">
            </form>
        </div>
    </div>
    <?php
}
else
{
    ?>
<div class="container-fluid d-flex justify-content-center">
    <div class="col-lg-8 col-12">
        <div class="container-fluid p-0 pt-3 m-0">
            <div class="col-12 p-0 m-0 border-bottom">
                <span class="h1">Profil</span>
            </div>
            <div class="col-12 p-0 m-0 mt-2 d-flex flex-column flex-md-row mb-3">
                <div class="col-12 col-md-8 m-0 p-0">
                    <ul class="list-unstyled">
                        <li><span class="font-weight-bold">Prénom : </span><?php if(isset($userData['FName'])) echo htmlspecialchars($userData['FName'])?></li>
                        <li><span class="font-weight-bold">Nom : </span><?php if(isset($userData['LName'])) echo htmlspecialchars($userData['LName'])?></li>
                        <li><span class="font-weight-bold">Adresse mail : </span><?php if(isset($userData['Email'])) echo htmlspecialchars($userData['Email'])?></li>
                    </ul>
                </div>
                <div class="col-12 col-md-4 p-0 m-0 mb-3">
                    <a href="profil.php?edit" class="btn btn-light btn-block">Modifié le profil</a>
                    <a href="profil.php?delete" class="btn btn-danger btn-block">Supprimer le profil</a>
                </div>
            </div>
        </div>
        <div class="container-fluid p-0 pt-1 m-0">
            <div class="col-12 p-0 m-0 border-bottom">
                <span class="h1">Mes billets</span>
            </div>
            <div class="col-12 p-0 m-0 mt-2 d-flex flex-column flex-md-row flex-wrap mb-3">
                <?php

                $reqBillet = $managerBillet->selectBilletUser($userData['ID']);
                $checkBillet = false;
                while ($infoBillet = $reqBillet->fetch()) {
                    $seanceInfo = $managerSeance->selectSeanceID($infoBillet['IDSeance'])->fetch();
                    $filmInfo = $managerFilm->selectFilm($seanceInfo['IDFilm'])->fetch();
                    /**
                     * Check de la date ici
                     */
                    $finDufilm = date('H:i:s', strtotime($seanceInfo['Horaire']) + strtotime($filmInfo['Duree']));

                    //On compare la date actuel avec la date de la fin du film
                    if(time() <= strtotime($seanceInfo['Date'].' '.$finDufilm)) {
                        $checkBillet = true;
                        ?>
                        <div class="col-12 col-md-6 m-0 p-0 d-flex align-self-stretch">
                            <div class="col-12 p-3 mt-3 m-0 d-flex border rounded">
                                <div class="col-4 col-lg-3 m-0 p-0">
                                    <img class="img-fluid"
                                         src="<?php echo htmlspecialchars($filmInfo['PhotosPath'])
                                         ?>" alt="Photo_film">
                                </div>
                                <div class="col-8 col-lg-9">
                                    <div class="col-12 m-0 p-0">
                                    <span>Billet: <?php echo htmlspecialchars($infoBillet['IDBillet'])
                                        ?></span>
                                    </div>
                                    <div class="col-12 m-0 p-0">
                                    <span>Film: <?php echo htmlspecialchars($filmInfo['Nom'])
                                        ?></span>
                                    </div>
                                    <div class="col-12 m-0 p-0">
                                    <span>Seance du : <?php echo htmlspecialchars(date('d-m-Y', strtotime($seanceInfo['Date'])))." à ". htmlspecialchars($seanceInfo['Horaire'])
                                        ?></span>
                                    </div>
                                    <div class="col-12 m-0 p-0">
                                    <span>Durée: <?php echo htmlspecialchars($filmInfo['Duree'])
                                        ?></span>
                                    </div>
                                    <div class="col-12 m-0 p-0 pt-3">
                                    <span class="h3">Salle : <?php echo htmlspecialchars($seanceInfo['IDSalle']);
                                        ?></span>
                                    </div>
                                    <div class="col-12 m-0 p-0 pt-3">
                                        <?php
                                        $placeReq = $managerPlace->selectPlaceBillet($infoBillet['IDBillet']);
                                        while ($placeInfo = $placeReq->fetch()) {
                                            $tarifInfo = $managerTarif->selectTarif($placeInfo['IDTarif'])->fetch();
                                            ?>
                                            <div class="col-12 m-0 p-0 pt-1">
                                                <span><?php echo htmlspecialchars($tarifInfo['Nom'] . " x " . $placeInfo['Quantite']) ?></span>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                if(!$checkBillet){
                    ?>
                    <div class="col-12 m-0 p-0 pt-3 pb-3 d-flex">
                        <span class="text-muted h5"> Vous n'avez aucun billets.</span>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="container-fluid p-0 pt-1 m-0">
            <div class="col-12 p-0 m-0 border-bottom">
                <span class="h1">Mes anciens billets</span>
            </div>
            <div class="col-12 p-0 m-0 mt-2 d-flex flex-column flex-md-row flex-wrap mb-3">
                <?php

                $reqBillet = $managerBillet->selectBilletUser($userData['ID']);
                $checkBillet = false;

                while ($infoBillet = $reqBillet->fetch()) {
                    $seanceInfo = $managerSeance->selectSeanceID($infoBillet['IDSeance'])->fetch();
                    $filmInfo = $managerFilm->selectFilm($seanceInfo['IDFilm'])->fetch();
                    /**
                     * Check de la date ici
                     */
                    $finDufilm = date('H:i:s', strtotime($seanceInfo['Horaire']) + strtotime($filmInfo['Duree']));

                    //On compare la date actuel avec la date de la fin du film
                    if(time() > strtotime($seanceInfo['Date'].' '.$finDufilm)) {
                        $checkBillet = true;
                        ?>
                        <div class="col-12 col-md-6 m-0 p-0 d-flex align-self-stretch">
                            <div class="col-12 p-3 mt-3 m-0 d-flex border rounded">
                                <div class="col-4 col-lg-3 m-0 p-0">
                                    <img class="img-fluid"
                                         src="<?php echo htmlspecialchars($filmInfo['PhotosPath'])
                                         ?>" alt="Photo_film">
                                </div>
                                <div class="col-8 col-lg-9">
                                    <div class="col-12 m-0 p-0">
                                    <span>Billet: <?php echo htmlspecialchars($infoBillet['IDBillet'])
                                        ?></span>
                                    </div>
                                    <div class
                                    <div class="col-12 m-0 p-0">
                                    <span>Film: <?php echo htmlspecialchars($filmInfo['Nom'])
                                        ?></span>
                                    </div>
                                    <div class="col-12 m-0 p-0">
                                    <span>Seance du : <?php echo htmlspecialchars(date('d-m-Y', strtotime($seanceInfo['Date'])))." à ". htmlspecialchars($seanceInfo['Horaire'])
                                        ?></span>
                                    </div>
                                    <div class="col-12 m-0 p-0">
                                    <span>Durée: <?php echo htmlspecialchars($filmInfo['Duree'])
                                        ?></span>
                                    </div>
                                    <div class="col-12 m-0 p-0 pt-3">
                                    <span class="h3">Salle : <?php echo htmlspecialchars($seanceInfo['IDSalle']);
                                        ?></span>
                                    </div>
                                    <div class="col-12 m-0 p-0 pt-3">
                                        <?php
                                        $placeReq = $managerPlace->selectPlaceBillet($infoBillet['IDBillet']);
                                        while ($placeInfo = $placeReq->fetch()) {
                                            $tarifInfo = $managerTarif->selectTarif($placeInfo['IDTarif'])->fetch();
                                            ?>
                                            <div class="col-12 m-0 p-0 pt-1">
                                                <span><?php echo htmlspecialchars($tarifInfo['Nom'] . " x " . $placeInfo['Quantite']) ?></span>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                if(!$checkBillet) {
                    ?>
                    <div class="col-12 m-0 p-0 pt-3 pb-3 d-flex">
                        <span class="text-muted h5"> Vous n'avez aucun billets.</span>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>
    <?php
}
?>

<?php include ("includes/footer.php"); ?>
<?php include ("includes/scripts.php"); ?>

</body>
</html>
