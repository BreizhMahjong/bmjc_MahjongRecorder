<?php

require_once("requests.php");
require_once("requests_stats.php");
require_once("requests_profile.php");

$action = $_POST["action"];
if($action === "collectUsers") {
  
	echo collectUsers();

} else if($action === "collectScores") {

	$from = isset($_POST["from"]) ? $_POST["from"] : null;
	$to = isset($_POST["to"]) ? $_POST["to"] : null;
  $userId = isset($_POST["userId"]) ? $_POST["userId"] : null;
	$turnamentId = isset($_POST["turnamentId"]) ? $_POST["turnamentId"] : null;
	echo collectScores($from, $to, $userId, $turnamentId);

} else if($action === "collectRankingDates") {
  $withYears = isset($_POST["withYears"]) ? $_POST["withYears"] : true;
  $withAll = isset($_POST["withAll"]) ? $_POST["withAll"] : false;
	echo collectRankingDates($withYears, $withAll);
  
} else if($action === "collectTurnaments") {
  
	echo collectTurnaments();

} else if($action === "collectGames") {

  $from = isset($_POST["from"]) ? $_POST["from"] : null;
	$to = isset($_POST["to"]) ? $_POST["to"] : null;
	$userId = isset($_POST["userId"]) ? $_POST["userId"] : null;
	echo json_encode(collectGames($from, $to, $userId));
  
} else if($action === "collectGame") {
  
  $gameId = isset($_POST["gameId"]) ? $_POST["gameId"] : null;
	echo json_encode(collectScoresOfGame($gameId));
  
} else if($action === "insertScores") {
	
  $date = isset($_POST["date"]) ? $_POST["date"] : null;
  $turnamentId = isset($_POST["turnamentId"]) ? intval($_POST["turnamentId"]) : null;
	$scores = isset($_POST["scores"]) ? json_decode($_POST["scores"], true) : null;
	echo insertScores($date, $scores, $turnamentId);
  
} else if($action === "removeGame") {
	
  $gameId = isset($_POST["gameId"]) ? $_POST["gameId"] : null;
	echo removeGame($gameId);
  
} else if($action === "signon") {
	
  $login = isset($_POST["login"]) ? $_POST["login"] : null;
	$pass = isset($_POST["pass"]) ? $_POST["pass"] : null;
	echo signon($login, $pass);
  
} else if($action === "signoff") {
	
  signoff();
  
} else if($action === "isAdmin") {
	
  echo isset($_SESSION["user_id"]) ? isAdmin($_SESSION["user_id"]) : 0;
  
} else if($action === "collectStatsList") {
  
  echo collectStatsList();
  
} else if($action === "getMyUserId") {
  
  echo isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;
  
} else if($action === "getMyUserDisplayName") {
  
  echo isset($_SESSION["user_displayName"]) ? $_SESSION["user_displayName"] : null;
  
} else if($action === "changeDisplayName") {
  
  $newDisplayName = isset($_POST["displayName"]) ? $_POST["displayName"] : null;
  echo changeDisplayName($newDisplayName);
  
} else if($action === "stat_highestPointsByGame") {
  
  echo collectHighestPointsByGame();
  
} else if($action === "stat_lowestPointsByGame") {
  
  echo collectLowestPointsByGame();
  
} else if($action === "stat_highestCumulatedResultBySeason") {
  
  echo collectHighestCumulatedResultBySeason();
  
} else if($action === "stat_highestCumulatedResultByYear") {
  
  echo collectHighestCumulatedResultByYear();
  
} else if($action === "stat_highestCumulatedResult") {
  
  echo collectHighestCumulatedResult();
  
} else if($action === "stat_highestMeans") {
  
  echo collectHighestMeans();
  
} else if($action === "profile_individualScores") {
  
  $from = isset($_POST["from"]) ? $_POST["from"] : null;
	$to = isset($_POST["to"]) ? $_POST["to"] : null;
	$userId = isset($_POST["userId"]) ? $_POST["userId"] : null;
  $result = json_encode(collectIndividualScores($from, $to, $userId));
  echo $result;

  
}