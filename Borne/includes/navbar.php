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
    header('Location: /index.php');
}

if(isset($_POST['submit_login']) && empty($userSession)) {
    sleep(1);

    if(!$_SESSION['ban']) {

        $secret = "6LdCEdwUAAAAAAOUgqru5jJzvof5v6gvCRcKf1Uf";

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

if(isset($_POST['submit_signup']) && empty($userSession)) {

    $user = new User(array(
        'fName' => $_POST['name'],
        'lName' => $_POST['surname'],
        'email' => $_POST['email'],
        'password' => $_POST['password'],
        'passwordConfirmed' => $_POST['passwordConfirmed'],
        'status' => 'user',
    ));


    $secret = "6LdCEdwUAAAAAAOUgqru5jJzvof5v6gvCRcKf1Uf";

    $response = $_POST['g-recaptcha-response'];

    $remoteip = $_SERVER['REMOTE_ADDR'];

    $api_url = "https://www.google.com/recaptcha/api/siteverify?secret="
        . $secret
        . "&response=" . $response
        . "&remoteip=" . $remoteip ;
    stream_context_set_default(['http' => ['proxy' => 'tcp://hermes.aristocrate.lan:3128']]);
    $decode = json_decode(file_get_contents($api_url), true);



    /** check if information are valid */
    $valid = true;
   if ($decode['success'] != true) {
        $valid = false;
        $errorMessages[] = "Captcha invalide";
    }

    if (!$user->isValid())
        $valid = false;

    if ($manager->ifExists($user)) {
        $valid = false;
        $errorMessages[] = "Cet utilisateur existe déjà";
    }

    /** check is information are valid, if there is picture, if yes we add it */
    if ($valid) {
        $manager->register($user);
        $_POST = array();
        $successMessages[] = "Vous êtes bien inscrit.e";
    } else {
        $errors = $user->getErrors();
    }

    /** create a error message */
    if (isset($errors)) {
        if (in_array(User::NOT_SAME_PASSWORD, $errors)) {
            $errorMessages[] = "Ce n'est pas le même mot de passe";
        }
        if (in_array(User::INVALID_EMAIL, $errors) || in_array(User::INVALID_SIZE_EMAIL_NAME, $errors) || in_array(User::INVALID_EMAIL_FORMAT, $errors)) {
            $errorMessages[] = "L'adresse mail est invalide";
        }
        if (in_array(User::INVALID_F_NAME, $errors) || in_array(User::INVALID_SIZE_F_NAME, $errors)) {
            $errorMessages[] = "Le prénom est invalide";
        }
        if (in_array(User::INVALID_L_NAME, $errors) || in_array(User::INVALID_SIZE_L_NAME, $errors)) {
            $errorMessages[] = "Le nom est invalide";
        }
        if (in_array(User::INVALID_PASSWORD, $errors)) {
            $errorMessages[] = "Le mot de passe est invalide";
        }
        if (in_array(User::INVALID_PASSWORD, $errors)) {
            $errorMessages[] = "Le mot de passe est invalide";
        }
        if (in_array(User::INVALID_PASSWORD, $errors)) {
            $errorMessages[] = "Le mot de passe est invalide";
        }
    }
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
            <a class="navbar-brand mb-0 h1" href="/index.php">
                <img class="height-50 mr-2" src="/images/style/logo_aristocrate.png" alt="Logo">
                Aristocrate
            </a>
        </nav>
    </div>
</header>

<?php
if(empty($userSession)){
?>
<!-- The Modal Inscription -->
<div class="modal fade" id="inscription">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-2">
            <!-- Modal Header -->
            <div class="modal-header">
                <h6 class="modal-title text-secondary">Inscrivez-vous</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form action="<?php echo getURI() ?>" enctype="multipart/form-data" method="post">
                    <div class="form-group">
                        <input type="text" class="form-control pl-5 nom_icon"  name="name" value="<?php if(isset($_POST['name'])) echo htmlspecialchars($_POST['name']) ?>" placeholder="Prénom">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control pl-5 nom_icon" name="surname" value="<?php if(isset($_POST['surname'])) echo htmlspecialchars($_POST['surname']) ?>" placeholder="Nom">
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control pl-5 mail_icon" name="email" value="<?php if(isset($_POST['email'])) echo htmlspecialchars($_POST['email']) ?>" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <input type="password" pattern=".{8,}"   required title="8 caractères minimum" class="form-control pl-5 mdp_icon" name="password" value="<?php if(isset($_POST['password'])) echo htmlspecialchars($_POST['password']) ?>" placeholder="Mot de passe">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control pl-5 mdp_icon" name="passwordConfirmed" value="<?php if(isset($_POST['passwordConfirmed'])) echo htmlspecialchars($_POST['passwordConfirmed']) ?>" placeholder="Confirmez mot de passe">
                    </div>
                    <div class="g-recaptcha mb-3" data-sitekey="6LdCEdwUAAAAAOD_spHy5LEH4pffm_3t6aQBA0t9"></div>
                    <button type="submit" class="btn btn-dark btn-lg btn-block" name="submit_signup">Inscription</button>
                </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer d-flex flex-column align-items-center">
                <div>
                        <span>Vous êtes déjà inscrit? Allez sur l'onglet "Se connecter".</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal connexion -->
<div class="modal fade" id="connexion">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-2">
            <!-- Modal Header -->
            <div class="modal-header">
                <h6 class="modal-title text-secondary">Connexion</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form action="<?php echo getURI() ?>" enctype="multipart/form-data" method="post">
                    <div class="form-group">
                        <input type="email" name="email" class="form-control mail_icon pl-5" value="<?php if(isset($_POST['email'])) echo htmlspecialchars($_POST['email']) ?>" placeholder="Adresse e-mail">
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control mdp_icon pl-5" value="<?php if(isset($_POST['password'])) echo htmlspecialchars($_POST['password']) ?>" placeholder="Mot de passe">
                    </div>
                    <div class="g-recaptcha mb-3" data-sitekey="6LdCEdwUAAAAAOD_spHy5LEH4pffm_3t6aQBA0t9"></div>
                    <button type="submit" class="btn btn-block btn-dark btn-primary" name="submit_login">Connexion</button>
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

