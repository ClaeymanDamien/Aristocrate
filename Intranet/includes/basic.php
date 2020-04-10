<?php
$db = PDOConnection::getMysqlConnexion();
$dbExtranet = PDOConnection::getMysqlConnexionExtranet();
$manager = new UserManagerPDO($db);

$userSession = getSessionUser();

const JETON_EXPIRE = "Le jeton a expiré";

if(!(isset($_SESSION['token']) && isset($_SESSION['tokenExpirationTime'])&& checkTokenExpiration($_SESSION['tokenExpirationTime']))){
    $_SESSION['token'] = getToken(126);
    $_SESSION['tokenExpirationTime'] = getTokenExpirationTime(15);
}

$pusher = Pusher::getInstance();

$pusher->link('/css/style.css')
    ->src('/scripts/scripts.js')
    ->img('/images/style/account.png')
    ->img("/images/style/arrow_left.png")
    ->img("/images/style/favicon.jpg")
    ->img("/images/style/fb.png")
    ->img("/images/style/instagram.png")
    ->img("/images/style/lock.png")
    ->img("/images/style/logo_aristocrate.png")
    ->img("/images/style/mail.png")
    ->img("/images/style/menu.png")
    ->img("/images/style/tw.png");




