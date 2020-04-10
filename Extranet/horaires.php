<?php
session_set_cookie_params(['lifetime' => 0,'path' => '/', 'domain' => 'www.aristocrate.me', 'secure' => TRUE, 'httponly' => TRUE, 'samesite' => 'strict']);
session_start();
require_once(__DIR__ . '/lib/utilities.php');
include_once (__DIR__ . '/includes/basic.php');

$pusher->push();

$managerFilm = new FilmManagerPDO($db);
$managerSeance = new SeanceManagerPDO($db);

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
        <div class="container-fluid text-center p-2 mt-2">
            <h1>Horaires</h1>
        </div>
        <!-- Horaires -->
        <!-- Liste des films -->
        <div class="col-12 p-0 m-0">
            <?php
            $allFilm = $managerFilm->selectStatusFilm('Prochainement');
            $k = 0;
            while($filmInfo = $allFilm->fetch())
            {
                ?>
                <div class="col-12 p-0 pb-5 pt-4 m-0 border-top">
                    <div class="container-fluid p-0 m-0 mb-4">
                        <a class="text_decoration_none" href="/film.php?id=<?php echo htmlspecialchars($filmInfo['ID']) ?>">
                            <h1 class="h3"><span class="badge badge-danger">Prochainement</span> <?php if(isset($filmInfo['Nom'])) echo htmlspecialchars($filmInfo['Nom'])?></h1>
                        </a>
                    </div>
                    <div class="container-fluid p-0 m-0 d-flex">
                        <div class="col-3 col-md-2 m-0 p-0">
                            <div>
                                <a class="text_decoration_none" href="/film.php?id=<?php echo htmlspecialchars($filmInfo['ID']) ?>">
                                    <img class="img-fluid" src="<?php if(isset($filmInfo['PhotosPath'])) echo htmlspecialchars($filmInfo['PhotosPath']) ?>">
                                </a>
                            </div>
                            <div class="mt-3">
                                <a href="reservation.php?id=<?php if(isset($filmInfo['ID'])) echo htmlspecialchars($filmInfo['ID'])?>" class="btn btn-outline-danger btn-block btn-lg">Réserver</a>
                            </div>
                        </div>
                        <div class="col-9 col-md-10 m-0 pl-md-5 pl-2">
                            <div class="pb-2">
                                <ul class="list-unstyled">
                                    <li><span class="font-weight-bold">Genre : </span><?php if(isset($filmInfo['Genre'])) echo htmlspecialchars($filmInfo['Genre']) ?></li>
                                    <li><span class="font-weight-bold">Date de sortie: </span><?php if(isset($filmInfo['DateDeSortie'])) echo htmlspecialchars($filmInfo['DateDeSortie']) ?></li>
                                    <li><span class="font-weight-bold">Réalisé par: </span><?php if(isset($filmInfo['Realisateur'])) echo htmlspecialchars($filmInfo['Realisateur']) ?></li>
                                    <li><span class="font-weight-bold">Durée: </span><?php if(isset($filmInfo['Duree'])) echo htmlspecialchars($filmInfo['Duree']) ?></li>
                                    <li><span class="font-weight-bold">Avec: </span><?php if(isset($filmInfo['Acteur'])) echo htmlspecialchars($filmInfo['Acteur']) ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid mt-4 p-0 m-0">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <?php
                            for($i=0;$i<7;$i++){
                                $day = date("d-m",mktime(0,0,0, date("m"), date("d")+$i, date("Y")));
                                ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php if($i == 0) echo "active"?>" data-toggle="tab" href="#date1<?php echo $k.$i?>"><?php echo $day?></a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">

                            <?php
                            for($j=0;$j<7;$j++){
                                $seanceCheck = false;
                                ?>
                                <div id="date1<?php echo $k.$j?>" class="container p-0 tab-pane <?php if($j==0){echo "active";}else{echo "fade";} ?>">
                                    <div class="d-flex justify-content-start p-0 m-0 mt-3">
                                        <?php
                                        $reqSeance = $managerSeance->selectDaySeances(htmlspecialchars($filmInfo['ID']),date("Y-m-d",mktime(0,0,0, date("m"), date("d")+$j, date("Y"))));
                                        while ($seanceInfo = $reqSeance->fetch()){

                                            $seanceCheck = true;
                                            ?>
                                            <div class="height-50 border rounded d-flex align-items-center justify-content-center p-4 mr-3">
                                                <span><?php if(isset($seanceInfo['Horaire'])) echo htmlspecialchars(date('H : i', strtotime($seanceInfo['Horaire']))) ?></span>
                                            </div>
                                            <?php
                                        }
                                        if(!$seanceCheck){
                                            ?>
                                            <span class="text-muted">Il n'y a pas de séance ce jour-là.</span>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                $k++;
            }

            $allFilm = $managerFilm->selectStatusFilm('Diffuse');
            $k = 0;
            while($filmInfo = $allFilm->fetch())
            {
                ?>
                <div class="col-12 p-0 pb-5 pt-4 m-0 border-top">
                    <div class="container-fluid p-0 m-0 mb-4">
                        <a class="text_decoration_none" href="/film.php?id=<?php echo htmlspecialchars($filmInfo['ID']) ?>">
                            <h1 class="h3"><?php if(isset($filmInfo['Nom'])) echo htmlspecialchars($filmInfo['Nom'])?></h1>
                        </a>
                    </div>
                    <div class="container-fluid p-0 m-0 d-flex">
                        <div class="col-3 col-md-2 m-0 p-0">
                            <div>
                                <a href="/film.php?id=<?php echo htmlspecialchars($filmInfo['ID']) ?>">
                                    <img class="img-fluid" src="<?php if(isset($filmInfo['PhotosPath'])) echo htmlspecialchars($filmInfo['PhotosPath']) ?>">
                                </a>
                            </div>
                            <div class="mt-3">
                                <a href="reservation.php?id=<?php if(isset($filmInfo['ID'])) echo htmlspecialchars($filmInfo['ID'])?>" class="btn btn-outline-danger btn-block btn-lg">Réserver</a>
                            </div>
                        </div>
                        <div class="col-9 col-md-10 m-0 pl-md-5 pl-2">
                            <div class="pb-2">
                                <ul class="list-unstyled">
                                    <li><span class="font-weight-bold">Genre : </span><?php if(isset($filmInfo['Genre'])) echo htmlspecialchars($filmInfo['Genre']) ?></li>
                                    <li><span class="font-weight-bold">Date de sortie: </span><?php if(isset($filmInfo['DateDeSortie'])) echo htmlspecialchars($filmInfo['DateDeSortie']) ?></li>
                                    <li><span class="font-weight-bold">Réalisé par: </span><?php if(isset($filmInfo['Realisateur'])) echo htmlspecialchars($filmInfo['Realisateur']) ?></li>
                                    <li><span class="font-weight-bold">Durée: </span><?php if(isset($filmInfo['Duree'])) echo htmlspecialchars($filmInfo['Duree']) ?></li>
                                    <li><span class="font-weight-bold">Avec: </span><?php if(isset($filmInfo['Acteur'])) echo htmlspecialchars($filmInfo['Acteur']) ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid mt-4 p-0 m-0">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <?php
                            for($i=0;$i<7;$i++){
                                $day = date("d-m",mktime(0,0,0, date("m"), date("d")+$i, date("Y")));
                                ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php if($i == 0) echo "active"?>" data-toggle="tab" href="#date<?php echo $k.$i?>"><?php echo $day?></a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">

                            <?php
                            for($j=0;$j<7;$j++){
                                $seanceCheck = false;
                                ?>
                                <div id="date<?php echo $k.$j?>" class="container p-0 tab-pane <?php if($j==0){echo "active";}else{echo "fade";} ?>">
                                    <div class="d-flex justify-content-start p-0 m-0 mt-3">
                                        <?php
                                        $reqSeance = $managerSeance->selectDaySeances(htmlspecialchars($filmInfo['ID']),date("Y-m-d",mktime(0,0,0, date("m"), date("d")+$j, date("Y"))));
                                        while ($seanceInfo = $reqSeance->fetch()){

                                            $seanceCheck = true;
                                            ?>
                                            <div class="height-50 border rounded d-flex align-items-center justify-content-center p-4 mr-3">
                                                <span><?php if(isset($seanceInfo['Horaire'])) echo htmlspecialchars(date('H : i', strtotime($seanceInfo['Horaire']))) ?></span>
                                            </div>
                                            <?php
                                        }
                                        if(!$seanceCheck){
                                            ?>
                                            <span class="text-muted">Il n'y a pas de séance ce jour-là.</span>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                $k++;
            }
            ?>
        </div>
    </div>
</div>

<?php include ("includes/footer.php"); ?>

<?php include ("includes/scripts.php"); ?>

</body>
</html>
