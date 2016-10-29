<?php

require_once("dao/abstractDao.php");
require_once("models/user.php");
require_once("bmjc_conf.php");


class UserDao extends AbstractDao {
  
  
  function __construct() {
    
    parent::__construct();
    
  }
  
  
  public function getById($id) {
    
    $sql = "SELECT * FROM " . TABLE_USERS . " WHERE ID = ?";
    $params = array($id);
    $response = $this->sqlRequest($sql, $params);
    if(count($response) !== 1) {
      return null;
    }
    return $this->rowToUser($response[0]);
    
  }
  
  
  public function getAll() {
    
    $sql = "SELECT * FROM " . TABLE_USERS;
    $params = $this->toSqlParams();
    $response = $this->sqlRequest($sql, $params);
    $users = array();
    foreach($response as $row) {
      $users[] = $this->rowToUser($row);
    }
    return $users;
    
  }
  
  
  public function getPlayersByDate($from = null, $to = null) {
    
    $sql = "SELECT * FROM " . TABLE_USERS . " users, " . TABLE_GAMES . " games, " . TABLE_SCORES . " scores ";
    $sql .= "WHERE users.ID = scores.user_id ";
    $sql .= "AND games.ID = scores.game_id ";
    if(!is_null($from)) {
      $sql .= "AND games.game_date >= ? ";
    }
    if(!is_null($to)) {
      $sql .= "AND games.game_date < ? ";
    }
    $params = $this->toSqlParams($from, $to);
    $response = $this->sqlRequest($sql, $params);
    $users = array();
    foreach($response as $row) {
      $users[] = $this->rowToUser($row);
    }
    return $users;
    
  }
  
  
  private function rowToUser($row) {
    
    return new User($row["ID"], $row["user_login"], $row["user_pass"], $row["display_name"]);
    
  } 
  
  
}