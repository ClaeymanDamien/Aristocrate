<?php
session_start();
require_once(__DIR__ . '/lib/utilities.php');
include_once (__DIR__ . '/includes/basic.php');

if(isset($_POST['login_admin']) && empty($userSession)) {
    sleep(1);

    if(!$_SESSION['ban']) {

        #$secret = "";

        $response = $_POST['g-recaptcha-response'];

        $remoteip = $_SERVER['REMOTE_ADDR'];

        $api_url = "https://www.google.com/recaptcha/api/siteverify?secret="
            . $secret
            . "&response=" . $response
            . "&remoteip=" . $remoteip ;
        //stream_context_set_default(['http' => ['proxy' => 'tcp://hermes.aristocrate.lan:3128']]);
        $decode = json_decode(file_get_contents($api_url), true);



        /** check if information are valid */

        if ($decode['success'] != true) {
            $errorMessages[] = "Captcha invalide";
        }
        else{
            $email = $_POST['email'];
            $password = $manager->hash($_POST['password'], $_POST['email']);


            $user = $manager->login($email, $password);


            /** if we find the user, we redirect him to another page and create a session user, else we display an alert message */
            if ($user) {
                $_SESSION['tentative'] = 0;
                $_SESSION['user'] = serialize($user);
                $userSession = $user;
                $_POST = array();
            } else {
                $_SESSION['tentative'] += 1;
                $errorMessages[] = 'L\'adresse mail ou le mot de passe est incorect';
            }
        }
    }
    else{
        $errorMessages[] = "Vous pourrez réessayer dans ".date('i:s', strtotime($_SESSION['tentative_ban']) - time()) ." minutes";
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <?php include (__DIR__ . '/includes/head.php'); ?>
</head>
<body>
<!-- Navbar -->
<?php include (__DIR__ . '/includes/navbar.php') ?>

<div class="container-fluid d-flex justify-content-center">
    <?php
    if(!empty($userSession)) {
        if($userSession->getStatus() == "user"){
            ?>
            <div class="col-lg-8 col-12 height-80-vh d-flex justify-content-center align-items-center">
                <div class="col-12 col-md-6 col-lg-4 p-3">
                    <span class="h1">Il n'y a aucune fonctionnalité pour le moment</span>
                </div>
            </div>
            <?php
        }
        else{
            ?>
            <div class="col-lg-8 col-12 height-80-vh d-flex justify-content-center align-items-center">
                <?php
                if($userSession->getStatus() == "admin") {
                    ?>
                    <div class="col-12 col-md-6 col-lg-4 p-3">
                        <a href="/adduser.php" class="btn p-md-4 p-3 btn-block btn-danger">Ajouter un utilisateur</a>
                    </div>
                    <?php
                }
                if($userSession->getStatus() == "manager" || $userSession->getStatus() == "admin"){
                    ?>
                    <div class="col-12 col-md-6 col-lg-4 p-3">
                        <a href="/addmovieshow.php" class="btn p-md-4 p-3 btn-block btn-danger">Ajouter une séance</a>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
    }else{
        ?>
        <div class="col-lg-8 col-12">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-2">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h6 class="modal-title text-secondary">Connexion Administration</h6>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="/" enctype="multipart/form-data" method="post">
                            <div class="form-group">
                                <input type="email" name="email" class="form-control mail_icon pl-5"
                                       value="<?php if (isset($_POST['email'])) echo htmlspecialchars($_POST['email']) ?>"
                                       placeholder="Adresse e-mail">
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" class="form-control mdp_icon pl-5"
                                       value="<?php if (isset($_POST['password'])) echo htmlspecialchars($_POST['password']) ?>"
                                       placeholder="Mot de passe">
                            </div>
                            <div class="g-recaptcha mb-3" data-sitekey="6LdCEdwUAAAAAOD_spHy5LEH4pffm_3t6aQBA0t9"></div>
                            <button type="submit" class="btn btn-block btn-dark btn-primary" name="login_admin">
                                Connexion
                            </button>
                        </form>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer d-flex flex-column align-items-center">
                        <div>
                            <span>Vous n'êtes pas inscrit? Allez sur l'onglet inscription.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>


<?php include (__DIR__ . '/includes/footer.php'); ?>

<?php include (__DIR__ . '/includes/scripts.php'); ?>

</body>
</html>
