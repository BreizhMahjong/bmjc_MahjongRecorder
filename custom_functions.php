<?php

function checkPassword($pass, $hash) {
  
  require_once("../wp-includes/class-phpass.php");
  $wpHasher = new PasswordHash(8, true);
	return $wpHasher->CheckPassword($pass, $hash);
  
}