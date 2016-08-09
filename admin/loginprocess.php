<?php
require("init.php");
if(isset($_SESSION['sessUserId'])){		//User authentication
	header("Location: index.php");
	exit();
}

if(isset($_POST['btnUserLogin']))
{
	$uname = $_POST['uname'];
	$pswd = $_POST['pswd'];
	$userExists = $users -> validate($uname,$pswd);
	if($userExists)
	{
		
		header("Location: index.php");
		exit();
	}
	else
	{
		$errMsg = "Login failed!! Try again";
	}
}
?>
