<?php
  $submenu = isset($_GET["submenu"]) ? $_GET["submenu"] : "stats";
?>

<ul class="nav nav-pills nav-stacked" id="profileSubmenu">
  <li role="presentation" <?php if($submenu === "stats") { echo "class=\"active\""; } ?>><a href="?menu=profile&submenu=stats">Statistiques</a></li>
  <li role="presentation" <?php if($submenu === "historic") { echo "class=\"active\""; } ?>><a href="?menu=profile&submenu=historic">Historique</a></li>
  <li role="presentation" <?php if($submenu === "parameters") { echo "class=\"active\""; } ?>><a href="?menu=profile&submenu=parameters">Paramètres</a></li>
</ul>
<div id="profileContent">
<?php
  include("profile_" . $submenu . ".php");
?>
</div>
