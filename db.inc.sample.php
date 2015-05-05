<?php	

	$host 		= 'localhost';
	$database 	= '';
	$db_username= '';
	$db_password= '';

	$db = new PDO("mysql:host=$host;dbname=$database;charset=utf8", "$db_username", "$db_password");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

?>
