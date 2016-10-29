<?php

require_once("dao/abstractDao.php");
require_once("models/game.php");
require_once("bmjc_conf.php");


class GameDao extends AbstractDao {
  
  
  function __construct() {
    
    parent::__construct();
    
  }
  
  
  public function getById($id) {
    
    $sql = "SELECT * FROM " . TABLE_GAMES . " WHERE ID = ?";
    $params = array($id);
    $response = $this->sqlRequest($sql, $params);
    if(count($response) !== 1) {
      return null;
    }
    return $this->rowToGame($response[0]);
    
  }
  
  
  public function get($userId = null, $from = null, $to = null) {
    
    $sql = "SELECT games.* ";
    $sql .= "FROM " . TABLE_GAMES . " games, " . TABLE_SCORES . " scores ";
    $sql .= "WHERE scores.game_id = games.ID ";
    if(!is_null($userId)) {
      $sql .= "AND scores.user_id = ? ";
    }
    if(!is_null($from)) {
      $sql .= "AND games.game_date >= ? ";
    }
    if(!is_null($to)) {
      $sql .= "AND games.game_date < ? ";
    }
    $sql .= "ORDER BY games.game_date ASC, games.ID ASC";
    $params = $this->toSqlParams($userId, $from, $to);
    $response = $this->sqlRequest($sql, $params);
    $games = array();
    foreach($response as $row) {
      $games[] = $this->rowToGame($row);
    }
    return $games;
    
  }
  
  
  public function getByPlayerNumber($playerNumber, $userId = null, $from = null, $to = null) {
    
    $sql = "SELECT games.*, count(scores.ID) as players ";
    $sql .= "FROM " . TABLE_GAMES . " games, " . TABLE_SCORES . " scores ";
    $sql .= "WHERE scores.game_id = games.ID ";
    if(!is_null($userId)) {
      $sql .= "AND scores.user_id = ? ";
    }
    if(!is_null($from)) {
      $sql .= "AND games.game_date >= ? ";
    }
    if(!is_null($to)) {
      $sql .= "AND games.game_date < ? ";
    }
    $sql .= "GROUP BY games.ID ";
    $sql .= "HAVING players = ?";
    $sql .= "ORDER BY games.game_date ASC, games.ID ASC";
    $params = $this->toSqlParams($userId, $from, $to, $playerNumber);
    $response = $this->sqlRequest($sql, $params);
    $games = array();
    foreach($response as $row) {
      $games[] = $this->rowToGame($row);
    }
    return $games;
    
  }
  
  
  public function getByDemiHanChan($demiHanChan, $userId = null, $from = null, $to = null) {
    
    $sql = "SELECT games.*, SUM(scores.points) as total ";
    $sql .= "FROM " . TABLE_GAMES . " games, " . TABLE_SCORES . " scores ";
    $sql .= "WHERE scores.game_id = games.ID ";
    $sql .= "AND games.demi_han_chan = ? ";
    if(!is_null($userId)) {
      $sql .= "AND scores.user_id = ? ";
    }
    if(!is_null($from)) {
      $sql .= "AND games.game_date >= ? ";
    }
    if(!is_null($to)) {
      $sql .= "AND games.game_date < ? ";
    }
    $sql .= "GROUP BY games.ID ";
    $params = $this->toSqlParams($demiHanChan, $userId, $from, $to);
    $response = $this->sqlRequest($sql, $params);
    $games = array();
    foreach($response as $row) {
      $games[] = $this->rowToGame($row);
    }
    return $games;
    
  }
  
  
  public function getByDate($date, $userId = null) {
    
    $sql = "SELECT games.* ";
    $sql .= "FROM " . TABLE_GAMES . " games, " . TABLE_SCORES . " scores ";
    $sql .= "WHERE scores.game_id = games.ID ";
    if(!is_null($userId)) {
      $sql .= "AND scores.user_id = ? ";
    }
    $sql .= "AND games.game_date = ? ";
    $sql .= "GROUP BY games.ID ";
    $params = $this->toSqlParams($userId, $date);
    $response = $this->sqlRequest($sql, $params);
    $games = array();
    foreach($response as $row) {
      $games[] = $this->rowToGame($row);
    }
    return $games;
    
  }
  
  
  public function getByTurnamentId($turnamentId, $userId = null, $from = null, $to = null) {
    
    
    
  }
  
  private function rowToGame($row) {
    
    return new Game($row["ID"], $row["game_date"], $row["sender_id"], $row["initial_stack"], $row["demi_han_chan"], $row["turnament_id"]);
    
  } 
  
  
}