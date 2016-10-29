<?php

require_once("requests_utils.php");


function collectIndividualScores($from, $to, $userId) {
  
  global $bdd;
  $matchArray = array($from, $to, $userId);
  $sql = "SELECT users.display_name, scores.score, games.game_date, games.ID
FROM " . TABLE_GAMES . " AS games, " . TABLE_SCORES . " AS scores, " . TABLE_USERS . " AS users
WHERE games.ID = scores.game_id
AND users.ID = scores.user_id
AND games.game_date >= ?
AND games.game_date < ?
AND scores.user_id = ?
ORDER BY game_date ASC, ID ASC";
	$request = $bdd->prepare($sql);
	$request->execute($matchArray);
	$response = $request->fetchAll();
  $chart = new Chart();
  $chart->legendEnabled = true;
  $serie1 = new ChartSerie("Score");
  $serie1->labelEnabled = false;
  $serie2 = new ChartSerie("Total");
  $serie2->type = "area";
  $serie2->labelEnabled = false;
  $chart->addSerie($serie1);
  $chart->addSerie($serie2);
  $sum = 0;
  for($i=0; $i<count($response); $i++) {
    $score = intval($response[$i]["score"]);
    $sum += $score;
    $serie1->addData(sqlDateToHumanDate($response[$i]["game_date"]) . " (#" . $response[$i]["ID"] . ")", $score);
    $serie2->addData(sqlDateToHumanDate($response[$i]["game_date"]) . " (#" . $response[$i]["ID"] . ")", $sum);
  }
  $chart->xAxis["tickInterval"] = ceil(count($response)/10);
  $result = array(
    "chart" => $chart->toFormattedChart(),
  );
  
  /*
  ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
  var_dump($result);
  */

  return $result;
  
}


?>