<?php


require_once("requests_utils.php");


function collectGames($from, $to, $userId) {
  
  global $bdd;
  if($userId === null) {
      $sql = "SELECT games.ID, games.game_date, users.display_name
FROM " . TABLE_GAMES . " games, " . TABLE_USERS  . " users
WHERE sender_id = users.ID";
      $params = array();
  } else {
    $sql = "SELECT games.ID, games.game_date, users.display_name
FROM " . TABLE_GAMES . " games, " . TABLE_USERS . " users, " . TABLE_SCORES . " scores
WHERE   games.ID = scores.game_id
AND     scores.user_id = ?
AND     users.ID = games.sender_id";
    $params = array($userId);
  }
  if($from !== null && strlen($from) > 0) {
    $sql .= " AND games.game_date >= ?";
    $params[] = $from;
  }
  if($to !== null && strlen($to) > 0) {
    $sql .= " AND games.game_date < ?";
    $params[] = $to;
  }
  $sql .= " ORDER BY games.game_date DESC, games.ID DESC";
  $res = array();
  $request = $bdd->prepare($sql);
  $request->execute($params);
  $response = $request->fetchAll();
  foreach($response as $entry) {
    $game = array();
    $game["date"] = sqlDateToHumanDate($entry["game_date"]);
    $game["id"] = intval($entry["ID"]);
    $game["sender"] = $entry["display_name"];
    $res[] = $game;
  }
  return $res;
  
}


function collectScoresOfGame($gameId) {
  
  global $bdd;
  $sql = "SELECT users.display_name, scores.rank, scores.points, scores.score
FROM " . TABLE_SCORES . " scores, " . TABLE_USERS . " users
WHERE game_id = ?
AND   users.ID = scores.user_id
ORDER BY scores.rank ASC";
  $request = $bdd->prepare($sql);
  $request->execute(array($gameId));
  $response = $request->fetchAll();
  $scores = array();
  foreach($response as $entry) {
    $score = array();
    $score[] = intval($entry["rank"]);
    $score[] = $entry["display_name"];
    $score[] = formateNumber(intval($entry["points"]));
    $score[] = formateNumber(intval($entry["score"]));
    $scores[] = $score;
  }
  return $scores;
  
}

function collectUsers() {
	
	global $bdd;
  $sql = "SELECT ID, display_name FROM " . TABLE_USERS;
	$request = $bdd->query($sql);
	$response = $request->fetchAll();
	$json = json_encode($response);
	return $json;
	
}


function collectTurnaments() {
  
  global $bdd;
  $turnaments = array();
  if(isset($_SESSION["user_id"])) {
    $sql = "SELECT ID, name FROM " . TABLE_TURNAMENTS . " WHERE administrator = ? AND open = 1";
    $request = $bdd->prepare($sql);
    $request->execute(array($_SESSION["user_id"]));
    $response = $request->fetchAll();
    $length = count($response);
    for($i=0; $i<$length; $i++) {
      $turnaments[] = array(
        "id" => $response[$i]["ID"],
        "text" => $response[$i]["name"]
      );
    }
  }
	$json = json_encode($turnaments);
	return $json;
  
}


function collectScores($from, $to, $userId = null, $turnamentId = null) {
	
	global $bdd;
  $matchArray = array($from, $to);
  $sql = "SELECT users.display_name, SUM(scores.score) as total, COUNT(games.ID) as playedGames
FROM " . TABLE_GAMES . " AS games, " . TABLE_SCORES . " AS scores, " . TABLE_USERS . " AS users
WHERE games.ID = scores.game_id
AND users.ID = scores.user_id
AND games.game_date >= ?
AND games.game_date < ?
AND games.turnament_id ";
  if(is_null($turnamentId)) {
    $sql .= "IS NULL";
  }
  else {
    $sql .= "= ?";
    $matchArray[] = $turnamentId;
  }
  if(!is_null($userId)) {
    $sql .= "
AND scores.user_id = ?";
    $matchArray[] = $userId;
  }
  $sql .= "
GROUP BY display_name";
  if(is_null($userId)) {
    $sql .= "
HAVING count(games.ID) >= " . MIN_GAME_PLAYED;
  }
  $sql .= "
ORDER BY total DESC";
	$request = $bdd->prepare($sql);
	$request->execute($matchArray);
	$response = $request->fetchAll();
  $table = new Table(array("#", "Joueur", "Résultat", "Parties"));
  $chart = new Chart();
  $serie = new ChartSerie("Points");
  $chart->addSerie($serie);
  for($i=0; $i<count($response); $i++) {
    $table->addRow(array(
        $i + 1,
        $response[$i]["display_name"],
        formateNumber(intval($response[$i]["total"])),
        intval($response[$i]["playedGames"]),
    ));
    $serie->addData($response[$i]["display_name"], intval($response[$i]["total"]));
  }
  $result = array(
    "table" => $table->toFormattedTable(),
    "chart" => $chart->toFormattedChart()
  );
  return json_encode($result);
  
	
}


function collectRankingDates($withYears, $withAll) {
  
    $seasons = getAllSeasons($withYears, $withAll);
    $rankingDates = array();
    foreach($seasons as $season) {
      $seasonStartDate = $season->getSeasonStart();
      $seasonEndDate = $season->getSeasonEnd();
      $id = "$seasonStartDate/$seasonEndDate";
      $text = $season->name;
      $rankingDate = array();
      $rankingDate["id"] = $id;
      $rankingDate["text"] = $text;
      $rankingDates[] = $rankingDate;
    }
    $json = json_encode($rankingDates);
    return $json;

}


function insertScores($date, $scores, $turnamentId) {
	
  global $bdd;
	if(isset($_SESSION["user_id"])) {
    if(turnamentId > 0) {
      $sql = "SELECT administrator FROM " . TABLE_TURNAMENTS . " WHERE ID = ? AND open = 1";
      $request = $bdd->prepare($sql);
      $request->execute(array($turnamentId));
      $response = $request->fetch();
      if(!$response || $response["administrator"] !== $_SESSION["user_id"]) {
        return "Vous n'êtes pas autorisé à ajouter une partie dans ce tournoi.";
      }
    }
		$gameId = insertNewGame($date, $turnamentId);
		if(!is_null($gameId) && $gameId > 0) {
			$success = true;
			foreach($scores as $score) {
				$success = insertNewScore($score, $gameId);
				if($success === false) {
					break;
				}
			}
			return success ? "true" : "Un score n'a pas pu être ajouté dans la base de données. <strong>Contactez un administrateur!<strong>";
		} else {
			return "La partie n'a pas pu être ajoutée dans la base de données.";
		}
	} else {
		return "<strong>Utilisateur non connecté!<strong>";
	}
	
}


function insertNewGame($date, $turnamentId) {
	
	global $bdd;
	if(is_null($date)) {
		$date = date(SQL_DATE_FORMAT);
	}
  if($turnamentId <= 0) { $turnamentId = null; }
  $sql = "INSERT INTO " . TABLE_GAMES . " (turnament_id, game_date, sender_id)
VALUES (?, ?, ?)";
	$request = $bdd->prepare($sql);
	if($request->execute(array($turnamentId, $date, $_SESSION["user_id"]))) {
		$lastId = $bdd->lastInsertId();
		return $lastId;
	} else {
		return null;
	}
	
}


function removeGame($gameId) {
  
  global $bdd;
	if(isset($_SESSION["user_id"])) {
    $sql = "DELETE FROM " . TABLE_GAMES . " WHERE bmjc_games.ID = ?";
    $request = $bdd->prepare($sql);
    if($request->execute(array($gameId))) {
      $sql = "DELETE FROM " . TABLE_SCORES . " WHERE game_id = ?";
      $request = $bdd->prepare($sql);
      if($request->execute(array($gameId))) {
        return "true";
      } else {
        return "Une erreur est survenue lors de la suppression des scores associés à cette partie.";
      }
    } else {
      return "La partie n'a pas pu être supprimée de la base de données.";
    }
	} else {
		return "<strong>Utilisateur non connecté!<strong>";
	}
  
}


function insertNewScore($score, $gameId) {
	
	global $bdd;
	$userId = $score["userId"];
    $points = $score["points"];
    $uma = $score["uma"];
	$scoreInt = $score["result"];
	$rank = $score["rank"];
  $sql = "INSERT INTO " . TABLE_SCORES . " (game_id, user_id, points, uma, score, rank)
VALUES (?, ?, ?, ?, ?, ?)";
	$request = $bdd->prepare($sql);
	return $request->execute(array($gameId, $userId, $points, $uma, $scoreInt, $rank));
	
}


function changeDisplayName($newDisplayName) {
  
  global $bdd;
	if(isset($_SESSION["user_id"])) {
    $sql = "UPDATE " . TABLE_USERS . "
SET display_name = ?
WHERE ID = ?";
    $request = $bdd->prepare($sql);
    if($request->execute(array($newDisplayName, $_SESSION["user_id"]))) {
      $_SESSION["user_displayName"] = $newDisplayName;
      return "true";
    } else {
      return "Une erreur est survenue, votre nom d'utilisateur n'a pas été modifié.";
    }
	} else {
		return "<strong>Utilisateur non connecté!<strong>";
	}
  
}