<?php
session_start();
ini_set("register_globals", "off");
ini_set("upload_max_filesize", "20M");
ini_set("post_max_size", "40M");
ini_set("memory_limit", "80M");

require_once("../data/conn.php");
require_once("../data/users.php");

$conn 					= new Dbconn();		
$users	 				= new Users();	

//define (ADMIN_GALLERY_LIMIT,20);


require_once("../data/constants.php");
?>