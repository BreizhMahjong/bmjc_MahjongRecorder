<?php

class User {
  
  var $id;
  var $login;
  var $pass;
  var $display;
  
  
  function __construct($id, $login, $pass, $display)  {
    
    $this->id = $id;
    $this->login = $login;
    $this->pass = $pass;
    $this->display = $display;
    
  }
  
  
}