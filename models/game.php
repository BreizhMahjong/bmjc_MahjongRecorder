<?php

class Game {
  
  var $id;
  var $turnamentId;
  var $gameDate;
  var $senderId;
  var $initialStack;
  var $demiHanChan;
  var $scores;
  
  
  function __construct($id, $gameDate, $senderId, $initialStack, $demiHanChan, $scores, $turnamentId = null)  {
    
    $this->id = $id;
    $this->turnamentId = $turnamentId;
    $this->gameDate = $gameDate;
    $this->senderId = $senderId;    
    $this->initialStack = $initialStack;    
    $this->demiHanChan = $demiHanChan;
    $this->scores = $scores;
    
  }
  
}