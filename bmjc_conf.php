<?php

setlocale (LC_TIME, 'fr_FR.utf8', 'fra'); 

define("HUMAN_DATE_FORMAT", "%a %d %b %Y");
define("THOUSAND_SEP", " ");
define("DECIMALS_SEP", ",");
define("MIN_GAME_PLAYED", 5);
define("STATS_RESULTS_LIMIT", 5);

define("AVATAR_PATH_PREFIX", "../wp-content/uploads/ultimatemember/");
define("AVATAR_PATH_SUFFIX", "/profile_photo-40.jpg");

// DB
define("DB_TYPE", "mysql");
define("DB_HOST", "127.0.0.1");
define("DB_NAME", "breizhmamod1");
define("DB_CHARSET", "utf8");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");

define("TABLE_GAMES", "bmjc_games");
define("TABLE_SCORES", "bmjc_scores");
define("TABLE_TURNAMENT", "bmjc_turnaments");
define("TABLE_USERS", "bmjc_users");
define("TABLE_ADMINS", "bmjc_admins");