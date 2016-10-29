<?php

require_once("dao/abstractDao.php");
require_once("models/score.php");
require_once("bmjc_conf.php");


class ScoreDao extends AbstractDao {
  
  
  function __construct() {
    
    parent::__construct();
    
  }
  
  
  public function getById($id) {
    
    $sql = "SELECT * FROM " . TABLE_SCORES . " WHERE ID = ?";
    $params = array($id);
    $response = $this->sqlRequest($sql, $params);
    if(count($response) !== 1) {
      return null;
    }
    return $this->rowToScore($response[0]);
    
  }
  
  
  public function get($userId = null, $from = null, $to = null) {
    
    $sql = "SELECT scores.* ";
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
    $sql .= "ORDER BY games.game_date ASC, games.ID ASC, scores.rank ASC";
    $params = $this->toSqlParams($userId, $from, $to);
    $response = $this->sqlRequest($sql, $params);
    $scores = array();
    foreach($response as $row) {
      $scores[] = $this->rowToScore($row);
    }
    return $scores;
    
  }
  
  
  public function getByGameId($gameId) {
    
    $sql = "SELECT * FROM " . TABLE_SCORES . " WHERE game_id = ? ORDER BY rank ASC";
    $params = $this->toSqlParams($gameId);
    $response = $this->sqlRequest($sql, $params);
    $scores = array();
    foreach($response as $row) {
      $scores[] = $this->rowToScore($row);
    }
    return $scores;
    
  }
  
  
  private function rowToScore($row) {
    
    return new Score($row["ID"], $row["game_id"], $row["user_id"], $row["points"], $row["uma"], $row["score"], $row["rank"]);
    
  } 
  
  
}