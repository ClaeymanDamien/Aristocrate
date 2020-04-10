<?php
session_set_cookie_params(['lifetime' => 0,'path' => '/', 'domain' => 'www.aristocrate.me', 'secure' => TRUE, 'httponly' => TRUE, 'samesite' => 'strict']);
session_start();
require_once(__DIR__ . '/lib/utilities.php');
include_once (__DIR__ . '/includes/basic.php');

$pusher->push();
?>

<!doctype html>
<html lang="fr">
<head>
    <?php include ("includes/head.php"); ?>
</head>
<body>
<!-- Navbar -->
<?php include ("includes/navbar.php") ?>

<div class="height-90-vh d-flex justify-content-center align-items-center">
    <div>
        <p class="h2">Excusez nous, mais vous n'êtes pas autorisé a accéder a cette partie du site.<br/>
        <small>Merci d'avoir l'obligeance de revenir à l'<a class="text-primary" href="/">accueil</a>.</small>
        </p>
    </div>
</div>

<?php include ("includes/footer.php"); ?>

<?php include ("includes/scripts.php"); ?>
</body>
</html>
