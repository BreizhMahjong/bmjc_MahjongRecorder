var loaded = [];
var isAdmin = false;

function toggleLoadingForGame(gameId) {
  
  $("#loadingImage" + gameId).toggle();
  $("#game" + gameId + "Table").toggle();
  
}

function loadGame(gameId) {
  
  var index = $.inArray(gameId, loaded);
  if(index < 0) {
    toggleLoadingForGame(gameId);
    loaded.push(gameId);
    $.post(SERVER_URL_PREFIX + "requests_controller.php", {
      "action" : "collectGame",
      "gameId" : gameId
    }, function(result) {
      var json = $.parseJSON(result);
      var i;
      $("#game" + gameId + "Table").DataTable({
        "paging"    : false,
        "ordering"  : false,
        "info"      : false,
        "searching" : false,
        "data"      : json,
        "dataSrc"   : ""
      });
      toggleLoadingForGame(gameId);
    }).fail(function() {
      // todo
    });
  } else {
    // todo (nothing to do ?)
  }
  
}


function removeGame(gameId) {
  
  if(!confirm("Êtes vous certain de vouloir supprimer la partie #" + gameId + " ?")) {
    return;
  }
  
  $.post(SERVER_URL_PREFIX + "requests_controller.php", {
      "action" : "removeGame",
      "gameId" : gameId
    }, function(result) {
      var $alert;
      if(result === "true") {
        $alert = $("#removeGameSuccess");
      } else {
        $alert = $("#removeGameFail").text(result);
      }
      $alert.slideDown();
      setTimeout(function() {
        $alert.slideUp();
        if(result === "true") {
          location.reload(true);
        }
      }, 3000);
    }).fail(function() {
      $alert = $("#removeGameFail").text("La requête n'a pas pu être transmise au serveur.");
    });
  
}


function updateTable() {

  toggleLoading();
  if($("#gameList").children().length) {
    $("#gameList").DataTable().destroy();
  }
  $("#gameList").empty();
  $.post(SERVER_URL_PREFIX + "requests_controller.php", {
	  "action" : "collectGames",
	}, function(result) {
    toggleLoading();
    var json = $.parseJSON(result);
    var i;
    $("#gameList").DataTable({
      "paging"    : true,
      "lengthChange" : false,
      "ordering"  : false,
      "info"      : false,
      "searching" : false,
      "data"      : json,
      "columns"   : [
        { "data" : "id" }
      ],
      "columnDefs": [
        {
          "render": function ( data, type, row ) {
              var res = "" +
  "<div id=\"game" + data + "\" class=\"panel panel-info\">" +
		"<div class=\"panel-heading\">" +
			"<a onclick=\"loadGame(" + data +")\" data-toggle=\"collapse\" data-parent=\"#game" + data + "\" href=\"#game" + data + "Collapse\"><strong>" + "#" + data + " " + row["date"] + "</strong> (Envoyée par : " + row["sender"] + ")</a>" +
		"</div>" +
		"<div id=\"game" + data + "Collapse\" class=\"panel-collapse collapse\">" +
			"<div class=\"panel-body\">" +
        "<table id=\"game" + data + "Table\" class=\"table\" style=\"width: 100%;\">" +
          "<thead>" +
            "<tr>" +
              "<th>#</th>" +
              "<th>Joueur</th>" +
              "<th>Points</th>" +
              "<th>Résultat</th>" +
            "</tr>" +
          "</thead>" +
          "<tbody></tbody>" +
        "</table>";
              if(isAdmin) {
                res += "<Button onclick=\"removeGame(" + data + ")\" class=\"btn btn-danger\">Supprimer</Button>";
              }
              res +=
        "<div id=\"loadingImage" + data + "\" class=\"loadingImage\">" +
          "<img src=\"images/rolling.gif\"/>" +
        "</div>" +
      "</div>" +
		"</div>" +
	"</div>";
              return res;
          },
          "targets": 0
        }
      ]
    });
  }).fail(function() {
    toggleLoading();
  }); 
  
  
}

$(document).ready(function() {
  
  $.post(SERVER_URL_PREFIX + "requests_controller.php", {
      "action" : "isAdmin"
    }, function(result) {
      isAdmin = result === "100";
      updateTable();
  });
  
  $("#gameList").show();
  $(".loadingImage").hide();

});