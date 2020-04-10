<?php
session_set_cookie_params(['lifetime' => 0,'path' => '/', 'domain' => 'www.aristocrate.me', 'secure' => TRUE, 'httponly' => TRUE, 'samesite' => 'strict']);
session_start();
require_once(__DIR__ . '/lib/utilities.php');
include_once (__DIR__ . '/includes/basic.php');

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

<div class="height-90-vh d-flex justify-content-center align-items-center">
    <div>
        <p class="h2">Êtes-vous perdu? <br/>
	Il n'y a rien d'autre que cet humble mur ici.<br/>
        <small>
		si oui:<br/>
		* <----- vous vous trouvez ici.<br/>
		Et l'<a class="text-primary" href="/">accueil</a> est de ce côté-ci -----> <a class="text-primary" href="/">*</a>.
	</small>
        </p>
    </div>
</div>

<?php include ("includes/footer.php"); ?>

<?php include ("includes/scripts.php"); ?>
</body>
</html>
