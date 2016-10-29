<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Breizh Mahjong Recorder</title>
    
    <!-- Bootstrap -->
    <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="lib/DataTables/datatables.min.css"/>
    
    <!-- Select2 -->
    <link rel="stylesheet" type="text/css" href="lib/select2-4.0.2/css/select2.min.css"/>
    <link rel="stylesheet" type="text/css" href="lib/select2-4.0.2/css/select2-bootstrap.min.css"/>
    
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">

      <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
      <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
      <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
  </head>
  <body> 
	
<?php

  require_once("bmjc_conf.php");
  
  session_start();
  $connexionRequiredPages = array("newgame", "profile");
	if(isset($_GET["menu"])) {
		$menu = $_GET["menu"];
    if(in_array($menu, $connexionRequiredPages) && !(isset($_SESSION["user_id"]))) {
      $menu = "ranking";
    }
	} else {
		$menu = "ranking";
	}
?>	
	

	<div id="parent">
    <nav class="navbar navbar-default">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="http://breizhmahjong.fr/">
            <img src="images/logo_render_small.png"/>
          </a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <li <?php if(!isset($_SESSION["user_id"])) { echo "class=\"disabled\""; }?> <? if($menu == "newgame") { echo "class=\"active\""; } ?>><a href="?menu=newgame">Nouvelle Partie</a></li>
            <li <? if($menu == "ranking") { echo "class=\"active\""; } ?>><a href="?menu=ranking">Classements</a></li>
            <li <? if($menu == "stats") { echo "class=\"active\""; } ?>><a href="?menu=stats">Statistiques</a></li>
            <li <? if($menu == "historic") { echo "class=\"active\""; } ?>><a href="?menu=historic">Historique</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
<?php
  if(isset($_SESSION["user_id"])) {
?>
            <li><a href="?menu=profile" id="userLink"><img class="userLogo" src="<?php echo AVATAR_PATH_PREFIX . $_SESSION["user_id"] . AVATAR_PATH_SUFFIX; ?>"/> <strong id="userLogin"><?php echo $_SESSION["user_login"]; ?></strong></a></li>
            <li><button id="logoutButton" type="button" class="btn btn-default navbar-btn">
              <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Déconnexion
            </button></li>
<?php
  } else {
?>
            <li><button id="loginButton" href="#modal" type="button" class="btn btn-success navbar-btn">
              <span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> Connexion
            </button></li>
<?php
  }
?>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>
		
		<div id="content">
	<?php
    include("includes/" . $menu . ".php");
	?>		
		</div>
	</div>
	
	<hr/>
	<div id="footer">
		<p>Author : Pierric Willemet @ <a href="http://breizhmahjong.fr/">Breizh Mahjong</a></p>
	</div>
  
  <div id="modal" class="popupContainer">
    <header class="popupHeader">
      <span class="header_title">LOGIN</span>
      <span class="modal_close"><span class="glyphicon glyphicon-remove"></span></span>
    </header>
    <section class="popupBody">
      <form id="loginForm">
        <p id="loginError"></p>
        <label for="loginInput">Login : </label>
        <div class="input-group">
          <input id="loginInput" type="text" class="form-control" aria-describedby="basic-addon2"/>
        </div>
        <br/>
        <label for="passwordInput">Password : </label>
        <div class="input-group">
          <input id="passwordInput" type="password" class="form-control" aria-describedby="basic-addon2"/>
        </div>
        <br/>
        <button id="validateLoginButton" type="button" class="btn btn-primary" onclick="loginEvent()" aria-label="Left Align">
          Valider
        </button>
      </form>
    </section>
  </div>
	
  <script src="lib/jquery-1.12.3.min.js"></script>
  <script src="lib/jquery.leanModal.min.js"></script>
  <script src="lib/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="lib/DataTables/datatables.min.js"></script>
	<script type="text/javascript" src="lib/select2-4.0.2/js/select2.min.js"></script>
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="js/main.js"></script>
  <script src="js/<?php echo $menu; ?>.js"></script>
<?php
if(isset($submenu)) {
  echo "  <script src=\"js/" . $menu . "_" . $submenu . ".js\"></script>"; 
}
?>
  </body>
</html>