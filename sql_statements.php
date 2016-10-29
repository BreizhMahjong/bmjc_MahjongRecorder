<?php

require_once("bmjc_conf.php");


// NEW GAME
define("SQL_COLLECT_USERS", "SELECT ID,user_login,display_name FROM wp_users");

define("SQL_COLLECT_TURNAMENTS", "SELECT ID, name FROM bmjc_turnaments WHERE administrator = ? AND open = 1");

define("SQL_GET_ADMINISRATOR_OF_TURNAMENT", "SELECT administrator FROM bmjc_turnaments WHERE ID = ? AND open = 1");

define("SQL_INSERT_NEW_GAME", "INSERT INTO bmjc_games (turnament_id, game_date, sender_id)
VALUES (?, ?, ?)");

define("SQL_INSERT_NEW_SCORE", "INSERT INTO bmjc_scores (game_id, user_id, points, uma, score, rank)
VALUES (?, ?, ?, ?, ?, ?)");
    


// RANKING
define("SQL_COLLECT_SCORES_OFF_TURNAMENT",
"SELECT users.display_name, SUM(scores.score) as total, COUNT(games.ID) as playedGames
FROM bmjc_games AS games, bmjc_scores AS scores, wp_users AS users
WHERE games.ID = scores.game_id
AND users.ID = scores.user_id
AND games.game_date >= ?
AND games.game_date < ?
AND games.turnament_id IS NULL
GROUP BY display_name
HAVING count(games.ID) >= " . MIN_GAME_PLAYED . "
ORDER BY total DESC");

define("SQL_COLLECT_SCORES_ON_TURNAMENT",
"SELECT users.display_name, SUM(scores.score) as total, COUNT(games.ID) as playedGames
FROM bmjc_games AS games, bmjc_scores AS scores, wp_users AS users
WHERE games.ID = scores.game_id
AND users.ID = scores.user_id
AND games.game_date >= ?
AND games.game_date < ?
AND games.turnament_id = ?
GROUP BY display_name
HAVING count(games.ID) >= " . MIN_GAME_PLAYED . "
ORDER BY total DESC");


// PUBLIC STATS
define("SQL_HIGHEST_POINTS_BY_GAME",
"SELECT users.display_name, scores.points, games.game_date
FROM wp_users AS users, bmjc_scores AS scores, bmjc_games AS games
WHERE scores.game_id = games.ID
AND   scores.user_id = users.ID
ORDER BY scores.points DESC
LIMIT " . STATS_RESULTS_LIMIT);

define("SQL_LOWEST_POINTS_BY_GAME",
"SELECT users.display_name, scores.points, games.game_date
FROM wp_users AS users, bmjc_scores AS scores, bmjc_games AS games
WHERE scores.game_id = games.ID
AND   scores.user_id = users.ID
ORDER BY scores.points ASC
LIMIT " . STATS_RESULTS_LIMIT);

define("SQL_HIGHEST_CUMULATED_RESULT",
"SELECT display_name, SUM(score) AS cumulatedResult
FROM wp_users, bmjc_scores
WHERE bmjc_scores.user_id = wp_users.ID
GROUP BY bmjc_scores.user_id
ORDER BY cumulatedResult DESC
LIMIT " . STATS_RESULTS_LIMIT);

define("SQL_HIGHEST_CUMULATED_RESULT_BETWEEN_DATES", "SELECT display_name, SUM(score) AS cumulatedResult
FROM wp_users, bmjc_scores, bmjc_games
WHERE bmjc_scores.user_id = wp_users.ID
AND bmjc_scores.game_id = bmjc_games.ID
AND bmjc_games.game_date >= ?
AND bmjc_games.game_date < ?
GROUP BY bmjc_scores.user_id
ORDER BY cumulatedResult DESC
LIMIT " . STATS_RESULTS_LIMIT);


// PERSONNAL STATS


?>