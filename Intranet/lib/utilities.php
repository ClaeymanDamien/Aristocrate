<?php

/**
 * @return mixed|null
 */

function getSessionUser()
{
    if(empty($_SESSION['user']))
    {
        return NULL;
    }
    else
    {
        return unserialize($_SESSION['user']);
    }
}

/** Push */
/**
 * @param $uri
 */
function pushImage($uri)
{
    header("Link: <{$uri}>; rel=preload; as=image", false);
}

/**
 * @param $uri
 */
function pushScript($uri)
{
    header("Link: <{$uri}>; rel=preload; as=scripts", false);
}

/**
 * @param $uri
 */
function pushStyle($uri)
{
    header("Link: <{$uri}>; rel=preload; as=style", false);
}
/*
/**
 * @param array $uri
 * array arg : path and type
 */
/*
function multiPush($uri = [])
{
    $pushString = "Link: ";
    foreach ($uri as $pushData){
            $pushString .= "<".$pushData['path'].">; rel=preload; as=".$pushData['type']
    }
    header("Link: <{$uri}>; rel=preload; as=style", false);
}
*/
/**
 * @param $idSeance
 * @param $idSalle
 * @param $db
 * @return int
 */
function nombrePlaceRestant($idSeance, $idSalle, $db){
    $managerBillet = new BilletManagerPDO($db);
    $managerSalle = new SalleManagerPDO($db);
    $managerPlace = new PlaceManagerPDO($db);

    $nombreDeBillet = 0;

    $reqBillet = $managerBillet->billetDeLaSeance($idSeance);
    while ($billetInfo = $reqBillet->fetch()){
        if(!empty($billetInfo)){
            $nombreDeBillet += $managerPlace->nbrDePlace($billetInfo['IDBillet'])->fetch()[0];
        }
    }
    $salleInfo = $managerSalle->selectSalle($idSalle)->fetch();
    $nombreDePlace = $salleInfo['NbrPlaces'];

    return $nombreDePlace - $nombreDeBillet;
}

/**
 * @return mixed|string
 */
function getURI(){
    $adresse = $_SERVER['PHP_SELF'];
    $i = 0;
    foreach($_GET as $cle => $valeur){
        $adresse .= ($i == 0 ? '?' : '&').$cle.($valeur ? '='.$valeur : '');
        $i++;
    }
    return $adresse;
}

/**
 * @param $lenght
 * @return string
 */
function getToken($lenght){
    try {
        return bin2hex(random_bytes($lenght));
    } catch (Exception $e) {
        echo "Une erreur est survenue";
    }
}

/**
 * @param $tokenTime
 * @return bool
 */
function checkTokenExpiration($tokenTime){
    if(strtotime($tokenTime) > time()){
        return true;
    }
    else{
        return false;
    }
}

/**
 * @param $expirationTime
 * @return false|string
 */
function getTokenExpirationTime($expirationTime){
    return date('H:i:s', strtotime('+'. $expirationTime .' minutes'));
    //return date('H:i:s', time());
}

/** load every class */
/**
 * @param $classname
 */
function autoload($classname)
{

    if (file_exists($file = dirname(__DIR__).'/class/'. $classname . '.php'))
    {
        require $file;
    }
}

spl_autoload_register('autoload');



