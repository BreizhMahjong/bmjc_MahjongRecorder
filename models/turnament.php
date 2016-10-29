<?php

class Turnament {
  
  var id;
  var name;
  var administrator;
  var open;
  
  
  function __construct($id, $name, $administrator, $open)  {
    
    $this->id = $id;
    $this->name = $name;
    $this->administrator = $administrator;
    $this->open = $open;    
    
  }
  
  
}