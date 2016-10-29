<?php

require_once("requests_utils.php");
require_once("sql_statements.php");


function cmpSqlRes1($a, $b) {
  
  $aa = $a["cumulatedResult"];
  $bb = $b["cumulatedResult"];
  return $aa - $bb;
  
}


function collectStatsList() {
  
  $result = array(
    array(
      "text" => "--- Score ---",
      "children" => array(
        array(
          "id" => "stat_highestPointsByGame",
          "text" => "Meilleurs scores en une partie"
        ),
        array(
          "id" => "stat_lowestPointsByGame",
          "text" => "Plus petits scores en une partie"
        ),
        array(
          "id" => "stat_highestCumulatedResultBySeason",
          "text" => "Plus grands résultats sur une saison"
        ),
        array(
          "id" => "stat_highestCumulatedResultByYear",
          "text" => "Plus grands résultats sur une année"
        ),
        array(
          "id" => "stat_highestCumulatedResult",
          "text" => "Plus grands résultats cumulés"
        ),
        array(
          "id" => "stat_highestMeans",
          "text" => "Meilleurs moyennes de points"
        )
      )
    )
  );
  
  return json_encode($result);
  
}


function collectHighestPointsByGame() {
  
  global $bdd;
  $sql = "SELECT users.display_name, scores.points, games.game_date
FROM " . TABLE_USERS . " AS users, " . TABLE_SCORES . " AS scores, " . TABLE_GAMES . " AS games
WHERE scores.game_id = games.ID
AND   scores.user_id = users.ID
ORDER BY scores.points DESC
LIMIT " . STATS_RESULTS_LIMIT;
  $request = $bdd->query($sql);
	$response = $request->fetchAll();
  $table = new Table(array("#", "Joueur", "Points", "Date"));
  $chart = new Chart();
  $serie = new ChartSerie("Points");
  $chart->addSerie($serie);
  for($i=0; $i<count($response); $i++) {
      $table->addRow(array(
          $i + 1,
          $response[$i]["display_name"],
          formateNumber(intval($response[$i]["points"])),
          sqlDateToHumanDate($response[$i]["game_date"]),
      ));
      $serie->addData($response[$i]["display_name"]. " " . sqlDateToHumanDate($response[$i]["game_date"]), intval($response[$i]["points"]));
  }
  $result = array(
    "table" => $table->toFormattedTable(),
    "chart" => $chart->toFormattedChart()
  );
  return json_encode($result);
  
}


function collectLowestPointsByGame() {
  
  global $bdd;
  $sql = "SELECT users.display_name, scores.points, games.game_date
FROM " . TABLE_USERS . " AS users, " . TABLE_SCORES . " AS scores, " . TABLE_GAMES . " AS games
WHERE scores.game_id = games.ID
AND   scores.user_id = users.ID
ORDER BY scores.points ASC
LIMIT " . STATS_RESULTS_LIMIT;
  $request = $bdd->query($sql);
	$response = $request->fetchAll();
  $table = new Table(array("#", "Joueur", "Points", "Date"));
  $chart = new Chart();
  $serie = new ChartSerie("Points");
  $chart->addSerie($serie);
  for($i=0; $i<count($response); $i++) {
      $table->addRow(array(
          $i + 1,
          $response[$i]["display_name"],
          formateNumber(intval($response[$i]["points"])),
          sqlDateToHumanDate($response[$i]["game_date"]),
      ));
      $serie->addData($response[$i]["display_name"]. " " . sqlDateToHumanDate($response[$i]["game_date"]), intval($response[$i]["points"]));
  }
  $result = array(
    "table" => $table->toFormattedTable(),
    "chart" => $chart->toFormattedChart()
  );
  return json_encode($result);
  
}


function collectHighestCumulatedResultBySeason() {
  
  global $bdd;
  $seasons = getAllSeasons(false);
  $results = array();
  foreach($seasons as $season) {
    $startDate = $season->getSeasonStart();
    $endDate = $season->getSeasonEnd();
    $sql = "SELECT users.display_name, SUM(scores.score) AS cumulatedResult
FROM " . TABLE_USERS . " users, " . TABLE_SCORES . " scores, " . TABLE_GAMES . " games
WHERE scores.user_id = users.ID
AND scores.game_id = games.ID
AND games.game_date >= ?
AND games.game_date < ?
GROUP BY scores.user_id
ORDER BY cumulatedResult DESC
LIMIT " . STATS_RESULTS_LIMIT;
    $request = $bdd->prepare($sql);
    $request->execute(array($startDate, $endDate));
    $response = $request->fetchAll();
    for($i=0; $i<count($response); $i++) {
        $results[] = array(
            "display_name" => $response[$i]["display_name"],
            "cumulatedResult" => $response[$i]["cumulatedResult"],
            "season" => $season->name,
        );
    }
  }
  usort($results, "cmpSqlRes1");
  $results = array_reverse($results);
  $table = new Table(array("#", "Joueur", "Résultat", "Saison"));
  $chart = new Chart();
  $serie = new ChartSerie("Resultat");
  $chart->addSerie($serie);
  for($i=0; $i<STATS_RESULTS_LIMIT; $i++) {
    $table->addRow(array(
      $i + 1,
      $results[$i]["display_name"],
      formateNumber(intval($results[$i]["cumulatedResult"])),
      $results[$i]["season"],
    ));
    $serie->addData($results[$i]["display_name"] . " " . $results[$i]["season"], intval($results[$i]["cumulatedResult"]));
  }
  $result = array(
    "table" => $table->toFormattedTable(),
    "chart" => $chart->toFormattedChart()
  );
  return json_encode($result);
  return json_encode($result);
  
}


function collectHighestCumulatedResultByYear() {
  
  global $bdd;
  $sql = "SELECT YEAR(game_date) AS oldestYear
FROM " . TABLE_GAMES . "
ORDER BY game_date ASC
LIMIT 1;";
  $request = $bdd->query($sql);
  $response = $request->fetch();
  $oldestYear = intval($response["oldestYear"]);
  $currentYear = intval(date("Y"));
  $years = array();
  for($i=$oldestYear; $i<=$currentYear; $i++) {
    $years[] = Season::fromYear($i);
  }
  $results = array();
  foreach($years as $season) {
    $startDate = $season->getSeasonStart();
    $endDate = $season->getSeasonEnd();
    $sql = "SELECT users.display_name, SUM(scores.score) AS cumulatedResult
FROM " . TABLE_USERS . " users, " . TABLE_SCORES . " scores, " . TABLE_GAMES . " games
WHERE scores.user_id = users.ID
AND scores.game_id = games.ID
AND games.game_date >= ?
AND games.game_date < ?
GROUP BY scores.user_id
ORDER BY cumulatedResult DESC
LIMIT " . STATS_RESULTS_LIMIT;
    $request = $bdd->prepare($sql);
    $request->execute(array($startDate, $endDate));
    $response = $request->fetchAll();
    for($i=0; $i<count($response); $i++) {
        $results[] = array(
            "display_name" => $response[$i]["display_name"],
            "cumulatedResult" => $response[$i]["cumulatedResult"],
            "season" => $season->name,
        );
    }
  }
  usort($results, "cmpSqlRes1");
  $results = array_reverse($results);
  $table = new Table(array("#", "Joueur", "Résultat", "Saison"));
  $chart = new Chart();
  $serie = new ChartSerie("Resultat");
  $chart->addSerie($serie);
  for($i=0; $i<STATS_RESULTS_LIMIT; $i++) {
    $table->addRow(array(
      $i + 1,
      $results[$i]["display_name"],
      formateNumber(intval($results[$i]["cumulatedResult"])),
      $results[$i]["season"],
    ));
    $serie->addData($results[$i]["display_name"] . " " . $results[$i]["season"], intval($results[$i]["cumulatedResult"]));
  }
  $result = array(
    "table" => $table->toFormattedTable(),
    "chart" => $chart->toFormattedChart()
  );
  return json_encode($result);
  
}


function collectHighestCumulatedResult() {
  
  global $bdd;
  $sql = "SELECT users.display_name, SUM(scores.score) AS cumulatedResult
FROM " . TABLE_USERS . " users, " . TABLE_SCORES . " scores
WHERE scores.user_id = users.ID
GROUP BY scores.user_id
ORDER BY cumulatedResult DESC
LIMIT " . STATS_RESULTS_LIMIT;
  $request = $bdd->query($sql);
	$response = $request->fetchAll();
  $table = new Table(array("#", "Joueur", "Résultat"));
  $chart = new Chart();
  $serie = new ChartSerie("Resultat");
  $chart->addSerie($serie);
  for($i=0; $i<count($response); $i++) {
    $table->addRow(array(
      $i + 1,
      $response[$i]["display_name"],
      formateNumber(intval($response[$i]["cumulatedResult"])),
    ));
    $serie->addData($response[$i]["display_name"], intval($response[$i]["cumulatedResult"]));
  }
  $result = array(
    "table" => $table->toFormattedTable(),
    "chart" => $chart->toFormattedChart()
  );
  return json_encode($result);
  
}


function collectHighestMeans() {
  
  global $bdd;
  $sql = "SELECT users.display_name, ROUND(AVG(scores.points)) AS mean
FROM " . TABLE_USERS . " users, " . TABLE_SCORES . " scores
WHERE scores.user_id = users.ID
GROUP BY users.ID
ORDER BY mean DESC
LIMIT " . STATS_RESULTS_LIMIT;
  $request = $bdd->query($sql);
	$response = $request->fetchAll();
  $table = new Table(array("#", "Joueur", "Moyenne"));
  $chart = new Chart();
  $serie = new ChartSerie("Moyenne");
  $chart->addSerie($serie);
  for($i=0; $i<count($response); $i++) {
    $table->addRow(array(
      $i + 1,
      $response[$i]["display_name"],
      formateNumber(intval($response[$i]["mean"])),
    ));
    $serie->addData($response[$i]["display_name"], intval($response[$i]["mean"]));
  }
  $result = array(
    "table" => $table->toFormattedTable(),
    "chart" => $chart->toFormattedChart()
  );
  return json_encode($result);
  
}

/* sélectionner les parties à 4 joueurs
SELECT bmjc_games.ID, COUNT(bmjc_scores.ID) as player_number
FROM bmjc_games, bmjc_scores
WHERE bmjc_scores.game_id = bmjc_games.ID
GROUP BY bmjc_games.ID
HAVING player_number = 4
*/

?>