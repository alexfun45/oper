<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
	 <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <title>spa</title>
    	
  </head>
  <body>
 <?php
 		session_start();
 		if(!defined('SRC_PATH')) define('SRC_PATH', dirname(__FILE__)."/src/");
 		//ini_set('display_errors', TRUE);
 		include('./src/api.php');
 		if(!isset($_SESSION['auth']))
  			include('./login.php');
  		else
  			include('./main.php');
  		//if(!isset($_SESSION['auth']))
  			//header('Location: ./login.php');
  		//else
  			//header('./main.php');
  			//header("Location: ./login.php");
  		//else
  			//header("Location: ./index.html");
  	?>
    
  </body>
</html>

