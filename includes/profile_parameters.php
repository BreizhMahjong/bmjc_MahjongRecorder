<div class="alert alert-success collapse" role="alert" id="saveChangesSuccess">
	Les nouveaux paramètres ont été <a href="#" class="alert-link">enregistrés</a>. La page va être rafraîchie...
</div>
<div class="alert alert-danger collapse" role="alert" id="saveChangesFail"></div>
<form role="form" onsubmit="return false">
  <div class="form-group">
    <label for="inputDisplayName">Nom d'utilisateur</label><br/>
    <input id="inputDisplayName" type="text" class="form-control" value="<?php echo $_SESSION["user_displayName"]; ?>" required />
  </div>
  <button id="parametersSubmitButton" type="submit" class="btn btn-default">Enregistrer</button>
</form>
<br/><br/>
<img class="userLogo" src="<?php echo AVATAR_PATH_PREFIX . $_SESSION["user_id"] . AVATAR_PATH_SUFFIX; ?>"/>
<a href="http://breizhmahjong.fr/profil/<?php echo $_SESSION["user_id"]; ?>/?profiletab=main&um_action=edit">Changer d'avatar</a>