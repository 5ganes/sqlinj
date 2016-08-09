<?php
class Users
{
 function validate($uname,$pswd)
 {
 	global $conn;
	
  $sql = "SELECT * FROM users u where u.username = '$uname' AND u.password = '$pswd'";
  //echo $sql; die();
  //tautology username: "abc' OR '1'='1' -- " or password: "abc' OR '1'='1"
  $result = $conn -> exec($sql);
  //print_r($result); die();
  $numRows = $conn -> numRows($result);
  //echo '<pre>';
  if($numRows)
  {
  	//for union query attack
  	// password= 1' UNION SELECT * FROM users_test where '1'='1
  	// while($row=$conn->fetchArray($result)){
  	// 	//$row = $conn -> fetchArray($result);
  	// 	print_r($row);
  	// }
  	// die();
   $row = $conn -> fetchArray($result);
 
   $_SESSION['sessUserId'] = $row['id'];
   $_SESSION['sessUsername'] = $row['username'];

   return true;
  }
  else
  {
   return false;
  }
 }
 
 function validateUser($uname,$pswd)
 {
 	global $conn;
	
  $sql = "SELECT * FROM users WHERE username='admin' AND password='$pswd'";
  //echo $sql;
  $result = $conn -> exec($sql);
  $numRows = $conn -> numRows($result);
  if($numRows)
  {
   $row = $conn -> fetchArray($result);
   $_SESSION['sessUserId'] = $row['id'];
   $_SESSION['sessUsername'] = $row['username'];
   $_SESSION['sessLastLogin'] = $row['lastLogin'];

   return true;
  }
  else
  {
   return false;
  }
 }

 function updateLastLogin($id)
 {
 	global $conn;
	
  $sql = "UPDATE users SET lastLogin = NOW() WHERE id = '$id'";
  $result = $conn -> exec($sql);
 }

 function updateLoginTimes($id)
 {
 	global $conn;
	
  $sql = "UPDATE users SET loginTimes = (loginTimes + 1) WHERE id = '$id'";
  $result = $conn -> exec($sql);
 }

 function validatePassword($id,$pswd)
 {
 	global $conn;
	
  $sql = "SELECT COUNT(*) cnt FROM users WHERE id = '$id' AND password = '$pswd'";
  //echo $sql;
  $result = $conn -> exec($sql);
  $row = $conn -> fetchArray($result);
  if($row['cnt'] > 0)
   return true;
  else
   return false;
 }

 function updatePassword($id,$pswd)
 {
 	global $conn;
	
  $sql = "UPDATE users SET password = '$pswd' WHERE id = '$id'";
  //echo $sql;
  $result = $conn -> exec($sql);
  $affRows = $conn -> affRows();
  if($affRows)
   return true;
  else
   return false;
 }
 
 function getSubLastWeight()
 {
	global $conn;
	$sql = "SElECT max(weight) FROM usergroups";
	$result = $conn->exec($sql);
	$numRows = $conn -> numRows($result);
	if($numRows > 0)
	{
		$row = $conn->fetchArray($result);
		return $row['max(weight)'] + 10;
	}
	else
		return 10;	 
 }
 
 function saveUser($id, $name, $username, $password, $district, $email, $phone, $website, $user_type, $org_info, $publish, $weight)
	{
		global $conn;
		$id = cleanQuery($id);
		$name = cleanQuery($name);
		$username = cleanQuery($username);
		$password = cleanQuery($password);
		$district = cleanQuery($district);
		$email=cleanQuery($email);
		$phone=cleanQuery($phone);
		$website=cleanQuery($website);
		$user_type = cleanQuery($user_type);
		$org_info=cleanQuery($org_info);
		$publish=cleanQuery($publish);
		$weight=cleanQuery($weight);
		if($id > 0)
		$sql = "UPDATE usergroups
						SET
							name = '$name',
							username = '$username',
							password = '$password',
							district = '$district',
							email = '$email',
							phone = '$phone',
							website = '$website',
							user_type = '$user_type',
							org_info='$org_info',
							publish='$publish',
							weight = '$weight'						
						WHERE
							id = '$id'";
		else
		$sql = "INSERT INTO usergroups SET name = '$name',username = '$username',password = '$password',district = '$district',email = '$email',phone='$phone',website='$website',user_type = '$user_type',org_info = '$org_info',publish = '$publish',weight = '$weight'";
		//echo $sql; die();
		$conn->exec($sql);
		if($id > 0)
			return $conn -> affRows();
		return $conn->insertId();
	}
	
	function saveImage($id)
	{
		global $conn;
		global $_FILES;
		
		if ($_FILES['image']['size'] <= 0)
			return;
		
		$id = cleanQuery($id);
		$filename = $_FILES['image']['name'];
		
		/*$ext = end(explode(".", $filename));
		$image = $id . "." . $ext;*/
		$image = $filename;
		
		copy($_FILES['image']['tmp_name'], "../". CMS_GROUPS_DIR . $image);
		
		$sql = "UPDATE usergroups SET image = '$image' WHERE id = '$id'";
		$conn->exec($sql);
	}
	
	function getById($id)
	{
		global $conn;
		$id = cleanQuery($id);
		$sql = "SElECT * FROM usergroups WHERE id = '$id'";
		$result = $conn->exec($sql);
		return $result;
	}
	
	function deleteImage($id)
	{
		global $conn;
		
		$id = cleanQuery($id);
		$result = $this->getById($id);
		$row = $conn->fetchArray($result);
		$image = "../". CMS_GROUPS_DIR . $row['image'];
		
		if (file_exists($image))
			unlink($image);
		
		$sql = "UPDATE usergroups SET image = '' WHERE id = '$id'";
		$conn->exec($sql);
	}
	
	function delete($id)
	{  
		global $conn;
		
		$id = cleanQuery($id);
		
		$result = $this->getById($id);
		$row = $conn->fetchArray($result);
		
		$file = "../" . CMS_GROUPS_DIR . $row['image'];
		
		if (file_exists($file) && !empty($row['image']))
			unlink($file);
		
		$sql = "DELETE FROM usergroups WHERE id = '$id'";
		$conn->exec($sql);
	}
	
	function validateInfoUser($uname,$pswd)
	{
		global $conn;
		
		$sql = "SELECT * FROM usergroups WHERE username='$uname' AND password='$pswd'";
	  	//echo $sql;
	  	$result = $conn -> exec($sql);
	  	$numRows = $conn -> numRows($result);
	  	if($numRows)
	  	{
	   		$row = $conn -> fetchArray($result);
	   		$_SESSION['userId'] = $row['id'];
	   		$_SESSION['userName'] = $row['username'];
	   		//$_SESSION['sessLastLogin'] = $row['lastLogin'];
	
	   		return true;
	  	}
	  	else
	  	{
	   		return false;
	  	}
	 }
	 
	 function validateMgr($uname,$pswd)
	 {
		global $conn;
		
	  	$sql = "SELECT * FROM users WHERE username='$uname' AND password='$pswd'";
	  	//echo $sql;
	  	$result = $conn -> exec($sql);
	  	$numRows = $conn -> numRows($result);
	  	if($numRows)
	  	{
	   		$row = $conn -> fetchArray($result);
	   		$_SESSION['sessMgrId'] = $row['id'];
	   		$_SESSION['sessMgrname'] = $row['username'];
	   		//$_SESSION['sessLastLogin'] = $row['lastLogin'];
	   		return true;
	  	}
	  	else
	  	{
	   		return false;
	  	}
	 }
	 
	 function validateMgrPassword($id,$pswd)
	 {
		global $conn;
		
	  	$sql = "SELECT COUNT(*) cnt FROM users WHERE id = '$id' AND password = '$pswd'";
	  	//echo $sql;
	  	$result = $conn -> exec($sql);
	  	$row = $conn -> fetchArray($result);
	  	if($row['cnt'] > 0)
	   		return true;
	 	else
	   		return false;
	 }
	 
	 //for vdc municipality
	 function getSubLastWeightVDC()
	 {
		global $conn;
		$sql = "SElECT max(weight) FROM vdcmuncipality";
		$result = $conn->exec($sql);
		$numRows = $conn -> numRows($result);
		if($numRows > 0)
		{
			$row = $conn->fetchArray($result);
			return $row['max(weight)'] + 10;
		}
		else
			return 10;	 
	 }
	 
	 function saveVDC($id, $name, $district, $vdctype, $wards, $weight)
	{
		global $conn;
		$id = cleanQuery($id);
		$name = cleanQuery($name);
		$district = cleanQuery($district);
		$vdctype = cleanQuery($vdctype);
		$wards = cleanQuery($wards);
		$weight=cleanQuery($weight);
		if($id > 0)
		$sql = "UPDATE vdcmuncipality
						SET
							name = '$name',
							district = '$district',
							vdctype = '$vdctype',
							wards = '$wards',
							weight = '$weight'						
						WHERE
							id = '$id'";
		else
		$sql = "INSERT INTO vdcmuncipality SET name = '$name',district = '$district',vdctype = '$vdctype',wards = '$wards',weight = '$weight'";
		//echo $sql; die();
		$conn->exec($sql);
		if($id > 0)
			return $conn -> affRows();
		return $conn->insertId();
	}
	
	function deleteVDC($id)
	{  
		global $conn;
		$id = cleanQuery($id);
		//$result = $this->getById($id);
		//$row = $conn->fetchArray($result);
		//$file = "../" . CMS_GROUPS_DIR . $row['image'];
		//if (file_exists($file) && !empty($row['image']))
		//	unlink($file);
		$sql = "DELETE FROM vdcmuncipality WHERE id = '$id'";
		$conn->exec($sql);
	}
	
	function getByIdVDC($id)
	{
		global $conn;
		$id = cleanQuery($id);
		$sql = "SElECT * FROM vdcmuncipality WHERE id = '$id'";
		$result = $conn->exec($sql);
		return $result;
	}
 	
	//for user type
	function getUserTypeById($id)
	{
		global $conn;
		$id = cleanQuery($id);
		$sql = "SElECT * FROM usertype WHERE id = '$id'";
		$result = $conn->exec($sql);
		return $result;
	}
	
	
}
?>