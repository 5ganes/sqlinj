<?php 
namespace PHPSQLParser;
require("init.php");
if(isset($_SESSION['sessUserId'])){   //User authentication
  header("Location: index.php");
  exit();
}

require_once dirname(__FILE__) . './PHP-SQL-Parser/vendor/autoload.php';

$parser = new PHPSQLParser();
if(isset($_POST['btnUserLogin']))
{
  $uname = $_POST['uname'];
  $pswd = $_POST['pswd'];
  
  echo '<pre>';
  
  $sql = "SELECT * FROM users where username = '' AND password = ''";
  $parsed_before = $parser->parse($sql);
  //print_r($parsed_before);

  $sql = "SELECT username,password FROM users where username = '$uname' AND password = '$pswd'";
  $parsed_after = $parser->parse($sql);
  print_r($parsed_after); die();
  
  //print count(array_diff_assoc($parsed_after[2],$parsed_before[2]));
  //print_r($diff);

  foreach($parsed_before as $key=>$pb){
    if($key=='WHERE')
      $pb_new=$pb;
  }

  foreach($parsed_after as $key=>$pa){
    if($key=='WHERE')
      $pa_new=$pa;
  }
  // print count(array_diff_assoc($pa_new,$pb_new));
  // print_r($pb_new);print_r($pa_new);

  // die();


  $result=$conn->exec($sql);
  $rows=$conn->numRows($result);
  $diff=count(array_diff_assoc($pa_new,$pb_new));

  if($rows>0 and $diff==0)
  {
    $row = $conn -> fetchArray($result);
    $_SESSION['sessUserId'] = $row['id'];
    $_SESSION['sessUsername'] = $row['username'];
    
    header("Location: index.php");
    exit();
  }
  else
  {
    if ($diff>0) {
      $errMsg = "Login failed!! Attempting SQL Injection. Try again";
    }
    else
      $errMsg = "Login failed!! Wrong username or password. Try again";
  }
} 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo ADMIN_TITLE; ?></title>

<link href="../css/admin.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="<?php echo ADMIN_PAGE_WIDTH; ?>" border="0" align="center" cellpadding="0" cellspacing="5" bgcolor="#FFFFFF">
  <tr>
    <td><?php include("header.php"); ?></td>
  </tr>
  <tr>
    <td width="100%" height="300" align="center" valign="middle"><table width="42%"  border="0" align="center" cellpadding="0" cellspacing="3">
      <tr>
        <td><table width="100%"  border="0" cellpadding="4" cellspacing="0" class="tahomabold11">
              <form action="" method="post" name="frmUserLogin">
              <tr>
                <td colspan="3"  class="heading2" >&nbsp;Administrator Login Console </td>
              </tr>
              <?php if(!empty($errMsg)){ ?>
              <tr align="center">
                <td colspan="3" class="warning"><?php echo $errMsg; ?></td>
              </tr>
              <?php } ?>
              <tr>
                <td width="11%">&nbsp;</td>
                  <td width="30%" align="left">Username:</td>
                <td width="59%" align="left"><input name="uname" type="text" class="text"></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                  <td align="left">Password:</td>
                <td align="left"><input name="pswd" type="password" class="text"></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="left"><input name="btnUserLogin" type="submit" class="button" value=" Login "></td>
              </tr>
            </form>
        </table></td>
      </tr>
    </table>
    <br>
    <br>
    <br>
    <br></td>
  </tr>
  <tr>
    <td><?php include("footer.php"); ?></td>
  </tr>
</table>
</body>
</html>