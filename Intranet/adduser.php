<?php
session_set_cookie_params(['lifetime' => 0,'path' => '/', 'domain' => 'www.aristocrate.me', 'secure' => TRUE, 'httponly' => TRUE, 'samesite' => 'strict']);
session_start();
require_once(__DIR__ . '/lib/utilities.php');
include_once (__DIR__ . '/includes/basic.php');

if(empty($userSession) || $userSession->getStatus() != "admin"){
    header('Location: /');
}




if (isset($_POST['submit_signup']) && empty($userSession)) {
    if($_POST['token'] == $_SESSION['token']) {
        $user = new User(array(
            'fName' => $_POST['name'],
            'lName' => $_POST['surname'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'passwordConfirmed' => $_POST['passwordConfirmed'],
            'status' => $_POST['status']
        ));


        #$secret = "";

        $response = $_POST['g-recaptcha-response'];

        $remoteip = $_SERVER['REMOTE_ADDR'];

        $api_url = "https://www.google.com/recaptcha/api/siteverify?secret="
            . $secret
            . "&response=" . $response
            . "&remoteip=" . $remoteip;
        //stream_context_set_default();
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
<div class="height-90-vh d-flex flex-column justify-content-center align-items-center mb-5">
    <div class="pb-3">
        <span class="display-4">Créer un utilisateur</span>
    </div>
    <div class=" col-12 col-md-6 col-lg-5">
        <div class="border p-2">
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
                    <div class="form-group">
                        <select class="custom-select" name="status">
                            <option value="user">User</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="g-recaptcha mb-3" data-sitekey="6LdCEdwUAAAAAOD_spHy5LEH4pffm_3t6aQBA0t9"></div>
                    <input type="hidden" name="token" id="token" value="<?php if(isset($_SESSION['token'])) echo $_SESSION['token']; ?>" />
                    <button type="submit" class="btn btn-dark btn-lg btn-block" name="submit_signup">Inscription</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include ("includes/footer.php"); ?>

<?php include ("includes/scripts.php"); ?>
</body>
</html>

