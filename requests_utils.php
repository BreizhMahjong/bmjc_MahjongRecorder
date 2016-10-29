<?php

require_once("bmjc_conf.php");
require_once("custom_functions.php");

define("WINTER_MIN", 1);
define("WINTER_MAX", 3);
define("SPRING_MIN", 4);
define("SPRING_MAX", 6);
define("SUMMER_MIN", 7);
define("SUMMER_MAX", 9);
define("AUTUMN_MIN", 10);
define("AUTUMN_MAX", 12);
define("DATE_FORMAT", "Y-m-d");
define("SQL_DATE_FORMAT", "Y-m-d H:i:s");

session_start();

$bdd = null;
try {
	$bdd = new PDO(DB_TYPE . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USERNAME, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
}
catch(Exception $e) {
	die('Erreur : '.$e->getMessage());
}

/**
 * Contient les informations à propos d'une saison de jeu
 * Une saison possède un nom, une date de début libre et une date de fin libre
 */
class Season {
  
  var $seasonStart;
  var $seasonEnd;
  var $name;
  
  /**
   * Constructeur
   */
  public function Season($name, $seasonStart, $seasonEnd) {
    
    $this->name = $name;
    $this->seasonStart = $seasonStart;
    $this->seasonEnd = $seasonEnd;
    
  }
  
  /**
   * Créer une saison qui commence le 1er Janvier de l'année $year et qui
   * qui finit le 1er Janvier exclu de l'année $year + 1
   *
   * @param int $year L'année à utiliser pour créer la saison
   * @return Season La saison créée
   */
  public static function fromYear($year) {
    
    $seasonStart = mktime(0, 0, 0, 1, 1, $year);
    $seasonEnd = mktime(0, 0, 0, 1, 1, $year+1);
    $name = "$year - Année";
    return new self($name, $seasonStart, $seasonEnd);
    
  }
  
  /**
   * Obtient la ou les années couvertes par la saison
   *
   * @return int|array La ou les années couvertes par la saison
   */
  public function getYear() {
    
    $oldestYear = intval(date("Y", $this->seasonStart));
    $newestYear = intval(date("Y", $this->seasonEnd));
    if($oldestYear === $newestYear) { return $oldestYear; }
    $years = array();
    for($i=$oldestYear; $i<=$newestYear; $i++) {
      $years[] = $i;
    }
    return $years;
    
  }
  
  /**
   * @return string La date de début de la saison formatée par DATE_FORMAT
   */
  public function getSeasonStart() {
    
    return date(DATE_FORMAT, $this->seasonStart);
    
  }
  
  /**
   * @return string La date de fin de la saison formatée par DATE_FORMAT
   */
  public function getSeasonEnd() {
    
    return date(DATE_FORMAT, $this->seasonEnd);
    
  }
  
}

/**
 * Enregistre les informations à propos d'une table de statistiques
 */
class Table {
  
  var $columns;
  var $rows;
  
  /**
   * Constructeur
   * @param array $columns Liste des noms des colonnes de la table
   */
  public function Table($columns) {
    
    $this->columns = $columns;
    $this->rows = array();
    
  }
  
  /**
   * Ajoute une ligne à la table
   * @param array $row La ligne à ajouter à la table
   */
  public function addRow($row) {
    
    $this->rows[] = $row;
    
  }
  
  /**
   * Transforme la table en tableau lisible par DataTables une fois transformé en json
   * @return array Le tableau lisible par DataTables une fois transformé en json
   */
  public function toFormattedTable() {
    
    $table = array(
      "rows" => $this->rows,
      "columns" => $this->toDataTableColumns($this->columns)
    );
    return $table;
    
  }
  
  /**
   * Transforme une liste en tableau lisible par DataTables quand transformé en json
   * @param array $columnList La liste à transformer
   * @return array Le tableau lisible par DataTables quand transformé en json
   */
  private function toDataTableColumns($columnList) {
    
    $dataTableColumns = array();
    foreach($this->columns as $column) {
      $dataTableColumn = array("sTitle" => $column);
      $dataTableColumns[] = $dataTableColumn;
    }
    return $dataTableColumns;
  
  }
  
}

/**
 * Enregistre les informations à propos d'une graphique de statistiques
 */
class Chart {
  
  var $series;
  var $colors = array('#f7a35c', '#f75b63');
  var $title = "";
  var $xAxis = array("type" => "category");
  var $legendEnabled = false;
  
  /**
   * Constructeur
   */
  public function Chart() {
    
    $this->series = array();
    
  }
  
  /**
   * Ajoute une série au graphique
   * @param ChartSerie $serie La série à ajouter au graphique
   */
  public function addSerie($serie) {
    
    $this->series[] = $serie;
    
  }
  
  /**
   * Transforme le graphique en tableau lisible par highcharts une fois transformé en json
   * @return array Le tableau lisible par highcharts une fois transformé en json
   */
  public function toFormattedChart() {
    
    $series = array();
    foreach($this->series as $serie) {
      $series[] = $serie->toFormattedSerie();
    }
    $chart = array(
      "colors" => $this->colors,
      "title" => array("text" => $this->title),
      "xAxis" => $this->xAxis,
      "legend" => array("enabled" => $this->legendEnabled),
      "series" => $series,
      "plotOptions" => array("column" => array("animation" => true))
    );
    return $chart;
    
  }
  
}

/**
 * Enregistre les informations à propore d'une série d'un grpahique de statistiques
 */
class ChartSerie {
  
  var $name;
  var $categories;
  var $data;
  var $type;
  var $labelEnabled;
  
  /**
   * Constructeur
   */
  public function ChartSerie($name) {
    
    $this->name = $name;
    $this->categories = array();
    $this->data = array();
    $this->type = "column";
    $this->labelEnabled = true;
    
  }
  
  /**
   * Ajoute une donnée à la série
   * @param string $category Le label de la donnée
   * @param int $data La valeur associée au label
   */
  public function addData($category, $data) {
    
    $this->categories[] = $category;
    $this->data[] = $data;
    
  }
  
  /**
   * Transforme la série en un tableau lisible par highcharts quand transformé en json
   * @return array Tableau lisible par highcharts quand transformé en json
   */
  public function toFormattedSerie() {
    
    $length = count($this->data);
    $data = array();
    for($i=0; $i<$length; $i++) {
      $data[] = array($this->categories[$i], $this->data[$i]);
    }
    $serie = array(
      "type" => $this->type,
      "name" => $this->name,
      "data" => $data,
      "dataLabels" => array("enabled" => $this->labelEnabled)
    );
    return $serie;
    
  }
  
}

/**
 * Obtenir toutes les saisons de jeu (hiver, printemps, été, automne) de la première partie
 * stockée dans la base de donnée à aujourd'hui.
 * @param boolean $includeYearsSeasons Inclure également les saisons sur l'année
 * @param boolean $includeAllCumulated Inclure également la saison du début des enregistrement à aujourd'hui (tout cumulé)
 * @return array Toutes les saisons de la première partie stockée en base à ajourd'hui
 */
function getAllSeasons($includeYearsSeasons, $includeAllCumulated) {
  
  global $bdd;
    $sql = "SELECT YEAR(game_date) as year, MONTH(game_date) as month
              FROM bmjc_games
              WHERE game_date <= NOW()
              ORDER BY game_date ASC
              LIMIT 1";
    $request = $bdd->query($sql);
    $response = $request->fetch();
    $oldestYear = intval($response["year"]);
    $oldestMonth = intval($response["month"]);
    $seasons = array();
    if($oldestYear === 0 || $oldestMonth === 0) { return $seasons; }
    $currentYear = intval(date("Y"));
    $currentMonth = intval(date("n"));
    for($i=$oldestYear; $i<=$currentYear; $i++) {
        if($includeYearsSeasons) {
            $seasonStart = mktime(0, 0, 0, 1, 1, $i);
            $seasonEnd = mktime(0 , 0, 0, 1, 1, $i+1);
            $seasonName = "$i - Année";
            $season = new Season($seasonName, $seasonStart, $seasonEnd);
            $seasons[] = $season;
        }
        $startingMonth = $i === $oldestYear ? $oldestMonth : 1;
        $endingMonth = $i === $currentYear ? $currentMonth : 12;
        for($j=$startingMonth; $j<=$endingMonth; $j+=3) {
            $rankingDate = array();
            if($j >= WINTER_MIN && $j <= WINTER_MAX) {
                $seasonName = "Hiver";
                $seasonStart = WINTER_MIN;
                $seasonEnd = WINTER_MAX;
            } else if ($j >= SPRING_MIN && $j <= SPRING_MAX) {
                $seasonName = "Printemps";
                $seasonStart = SPRING_MIN;
                $seasonEnd = SPRING_MAX;
            } else if ($j >= SUMMER_MIN && $j <= SUMMER_MAX) {
                $seasonName = "Été";
                $seasonStart = SUMMER_MIN;
                $seasonEnd = SUMMER_MAX;
            } else if ($j >= AUTUMN_MIN && $j <= AUTUMN_MAX) {
                $seasonName = "Automne";
                $seasonStart = AUTUMN_MIN;
                $seasonEnd = AUTUMN_MAX;
            }
            $seasonStart = mktime(0, 0, 0, $seasonStart, 1, $i);
            if($seasonEnd === 12) {
              $endMonth = 1;
              $endYear = $i + 1;
            } else {
              $endMonth = $seasonEnd + 1;
              $endYear = $i;
            }
            $seasonEnd = mktime(0 , 0, 0, $endMonth, 1, $endYear);
            $seasonName = "$i - $seasonName";
            $season = new Season($seasonName, $seasonStart, $seasonEnd);
            $seasons[] = $season;
        }
    }
    $seasons = array_reverse($seasons);
    if($includeAllCumulated) {
      $seasonStart = mktime(0, 0, 0, $oldestMonth, 1, $oldestYear);
      if($currentMonth === 12) {
        $endMonth = 1;
        $endYear = $currentYear + 1;
      } else {
        $endMonth = $currentMonth + 1;
        $endYear = $currentYear;
      }
      $seasonEnd = mktime(0 , 0, 0, $endMonth, 1, $endYear);
      $seasonName = "Toutes les parties";
      $season = new Season($seasonName, $seasonStart, $seasonEnd);
      array_unshift($seasons, $season);
      //$seasons[] = $season;
    }
    return $seasons;
  
}

/**
 * Logging d'un utilisateur
 * @param string loggin Le loggin de l'utilisateur
 * @param string pass le mot de passe de l'utilisateur
 * @return json Le résultat de la tentative de logging
 */
function signon($login, $pass) {
	
	global $bdd;
	$request = $bdd->prepare("SELECT user_pass, ID, display_name
		FROM " . TABLE_USERS . "
		WHERE user_login = ? ");
	$request->execute(array($login));
	$response = $request->fetch();
	$hash = $response["user_pass"];
  $userId = $response["ID"];
  $displayName = $response["display_name"];
	$success = checkPassword($pass, $hash);
	if($success) {
		$_SESSION["user_login"] = $login;
    $_SESSION["user_id"] = $userId;
    $_SESSION["user_displayName"] = $displayName;
		$output = '{"success": "true"}';
	} else {
		$output = '{"success": "false"}';
	}
	return $output;
	
}


function signoff() {
  
  unset($_SESSION["user_login"]);
  unset($_SESSION["user_id"]);
  unset($_SESSION["user_displayName"]);
  
}


/**
 * Transforme une date du format SQL vers le format HUMAN_DATE_FORMAT
 * @param string $date la date au format SQL
 * @return string la date au format HUMAN_DATE_FORMAT
 */
function sqlDateToHumanDate($date) {
  
  $dateTime = DateTime::createFromFormat(SQL_DATE_FORMAT, $date);
  $humanDate = strftime(HUMAN_DATE_FORMAT, $dateTime->getTimestamp());
  $humanDate = utf8_encode(ucfirst($humanDate));
  return $humanDate;
  //return $dateTime->format(HUMAN_DATE_FORMAT);
  
}


function sqlDateToTimestamp($date) {
  
  $dateTime = DateTime::createFromFormat(SQL_DATE_FORMAT, $date);
  return $dateTime->getTimestamp();
  
}


function isAdmin($userId) {
  
  global $bdd;
  $sql = "SELECT admin_level FROM " . TABLE_ADMINS . "
WHERE user_id = ?";
  $request = $bdd->prepare($sql);
  $request->execute(array($userId));
  $response = $request->fetch();
  return $response ? $response["admin_level"] : 0;
  
}


function formateNumber($number, $decimals = 0) {
  
  return number_format($number, $decimals, DECIMALS_SEP, THOUSAND_SEP);
  
}


function utf8ize($d) {
  if (is_array($d)) {
      foreach ($d as $k => $v) {
          $d[$k] = utf8ize($v);
      }
  } else if (is_string ($d)) {
      return utf8_encode($d);
  }
  return $d;
}