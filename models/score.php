<?php

class Score {
  
  var $id;
  var $gameId;
  var $userId;
  var $points;
  var $uma;
  var $result;
  var $rank;
  
  
  function __construct($id, $gameId, $userId, $points, $uma, $result, $rank)  {
    
    $this->id = $id;
    $this->gameId = $gameId;
    $this->userId = $userId;
    $this->points = $points;    
    $this->uma = $uma;    
    $this->result = $result;    
    $this->rank = $rank;    
    
  }
  
  
}