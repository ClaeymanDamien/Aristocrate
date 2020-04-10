<?php

/** Connection to the database */

class PDOConnection{
    public static function getMysqlConnexion()
    {
    	$db = new PDO('mysql:host=localhost; dbname=aristocratedb; charset=utf8','root','');
       	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $db;
    }
}
