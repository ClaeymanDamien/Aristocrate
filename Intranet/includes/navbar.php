<?php
/**
 *Il faut inclure /lib/utilities.php et basic.php avant.
 */
const TENTATIVE = 15;

if(!isset($_SESSION['tentative'])){
    $_SESSION['tentative'] = 0;
    $_SESSION['ban'] = false;
}
else{
    if(!isset($_SESSION['tentative_ban']) && $_SESSION['tentative'] > TENTATIVE){
        $_SESSION['tentative_ban'] = date('H:i:s', strtotime('+3 minutes'));
        $_SESSION['ban'] = true;
    }
    elseif(isset($_SESSION['tentative_ban']) && strtotime($_SESSION['tentative_ban']) < time()){
        $_SESSION['tentative'] = 0;
        $_SESSION['ban'] = false;
        unset($_SESSION['tentative_ban']);
    }
}

if (isset($_GET['logout']) && !empty($userSession)){
    $_SESSION = array();
    unset($userSession);
    session_destroy();
    header('Location: /');
}

if(isset($_GET['error'])){
    if($_GET['error'] == "login"){
        $errorMessages[] = "Veuillez vous connecter.";
    }
}


?>
<?php
/** display the error message */
if(isset($errorMessages))
{
    foreach($errorMessages as $errorMessage)
    {
        ?>
        <div class="container-fluid m-0 p-0">
            <div class="alert alert-danger alert-dismissible m-0">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo "<div>".$errorMessage."</div>"; ?>
            </div>
        </div>
        <?php
    }
}
if(isset($successMessages))
{
    foreach($successMessages as $successMessage)
    {
        ?>
        <div class="container-fluid m-0 p-0">
            <div class="alert alert-success alert-dismissible m-0">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo "<div>".$successMessage."</div>"; ?>
            </div>
        </div>
        <?php
    }
}
?>
<header class="p-0 m-0 container-fluid d-flex justify-content-center bg-light border-red-bottom">
    <div class="col-lg-8 col-12 ">
        <nav class="navbar navbar-expand-md navbar-light min-height-100">
            <a class="navbar-brand mb-0 h1" href="/">
                <img class="height-50 mr-2" src="/images/style/logo_aristocrate.png" alt="Logo">
                Aristocrate
            </a>
            <button class="border-0 navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#collapsibleNavId"
                    aria-controls="collapsibleNavId" aria-expanded="false" aria-label="Toggle navigation">
                <img src="/images/style/menu.png" alt="menu" class="img-fluid height-40">
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="collapsibleNavId">
                <ul class="navbar-nav mt-2 mt-lg-0">
                    <?php
                    if(!empty($userSession)) {
                        ?>
                        <li class="nav-item text-center">
                            <a class="nav-link" href="?logout">Déconnexion</a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </nav>
    </div>
</header>
