<?php

require_once("requests.php");
require_once("requests_stats.php");
require_once("requests_profile.php");

$action = $_POST["action"];
if($action === "collectUsers") {
	echo collectUsers();
} else if($action === "collectScores") {
	$from = $_POST["from"];
	$to = $_POST["to"];
	$turnamentId = isset($_POST["turnamentId"]) ? $_POST["turnamentId"] : null;
	echo collectScores($from, $to, $turnamentId);
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
  $gameId = $_POST["gameId"];
	echo json_encode(collectScoresOfGame($gameId));
} else if($action === "insertScores") {
	$date = $_POST["date"];
  $turnamentId = intval($_POST["turnamentId"]);
	$scores = json_decode($_POST["scores"], true);
	echo insertScores($date, $scores, $turnamentId);
} else if($action === "removeGame") {
	$gameId = $_POST["gameId"];
	echo removeGame($gameId);
} else if($action === "signon") {
	$login = $_POST["login"];
	$pass = $_POST["pass"];
	echo signon($login, $pass);
} else if($action === "signoff") {
	signoff();
} else if($action === "isAdmin") {
	echo isset($_SESSION["user_id"]) ? isAdmin($_SESSION["user_id"]) : 0;
} else if($action === "collectStatsList") {
  echo collectStatsList();
} else if($action === "getMyUserId") {
  echo $_SESSION["user_id"];
} else if($action === "getMyUserDisplayName") {
  echo $_SESSION["user_displayName"];
} else if($action === "changeDisplayName") {
  $newDisplayName = $_POST["displayName"];
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
  $from = $_POST["from"];
	$to = $_POST["to"];
	$userId = $_POST["userId"];
  echo collectIndividualScores($from, $to, $userId);
}