<?php

require_once("bmjc_conf.php");


abstract class AbstractDao {
  
  
  var $bdd;
  
  
  function __construct() {
    
    try {
      $this->bdd = new PDO(DB_TYPE . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USERNAME, DB_PASSWORD);
    }
    catch(Exception $e) {
      die('Erreur : '.$e->getMessage());
    }
    $this->filters = array();
    
  }
  
  
  protected function sqlRequest($sql, $params) {
     
    $request = $this->bdd->prepare($sql);
    $request->execute($params);
    return $request->fetchAll(PDO::FETCH_ASSOC);
    
  }
  
  
  protected function toSqlParams() {
    
    $params = array();
    foreach(func_get_args() as $n) {
      if(!is_null($n)) {
        $params[] = $n;
      }
    }
    return $params;
    
  }
  
  
  abstract protected function getById($id);
  
  
}