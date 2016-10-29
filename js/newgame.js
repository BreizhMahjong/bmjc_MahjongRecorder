
var users = [];
var AVATAR_DEFAULT_URL = "http://breizhmahjong.fr/wp-content/uploads/2015/11/bambou-1.png";
var AVATAR_URL_PREFIX = "http://breizhmahjong.fr/wp-content/uploads/ultimatemember/";
var AVATAR_URL_SUFFIX = "/profile_photo-40.jpg";
var FOUR_PLAYER_UMA = [+15000, +5000, -5000, -15000];
var FIVE_PLAYER_UMA = [+15000, +5000, 0, -5000, -15000];
var DEFAULT_INITIAL_STACK = 30000;
var loggedUser;

function User(userId, userDisplay, points, uma, result, rank) {
	
	this.userId = userId;
	this.userDisplay = userDisplay;
	this.points = points;
	this.uma = uma;
	this.result = result;
	this.rank = rank;
	
}

function loadUsers() {

	$.post(SERVER_URL_PREFIX + "requests_controller.php", {"action": "collectUsers"}, function(result) {
		var usersResult = $.parseJSON(result);
		var userId;
		$.each(usersResult, function(i, user) {
			userId = parseInt(user.ID);
			users.push(new User(userId, user.display_name, 0, 0, 0, 0));
		});
		computeUserSelects();
	});
	
}

function loadTurnaments() {

	$.post(SERVER_URL_PREFIX + "requests_controller.php", {"action": "collectTurnaments"}, function(result) {
		var turnamentsResult = result.length > 0 ? $.parseJSON(result) : "";
		$("#turnamentSelect").select2({
      "theme" :	"bootstrap",
      "data"  : turnamentsResult
    });
	});
	
}

function computeUserSelects() {
	
	var selectionableUsers = users.slice();
	var i;
	var $selectedUsers = $(".userSelect option:selected");
	if($selectedUsers.length > 0) {
		$(".userSelect option:selected").each(function(i, value) {
			for(i=0; i<selectionableUsers.length; i++) {
				if(selectionableUsers[i].userDisplay === $(value).text()) {
					selectionableUsers.splice(i, 1);
					break;
				}
			}
		});
	} else {
		selectionableUsers = users;
	}
	
	selectionableUsers = selectionableUsers.sort(sortByName);
	$(".userSelect option:not(:selected)").remove();
	$(".userSelect").append($("<option/>"));
	for(i=0; i<selectionableUsers.length; i++) {
		var $option = $("<option></option>").text(selectionableUsers[i].userDisplay).data("user", selectionableUsers[i]);
		$(".userSelect").append($option);
	}
	
}

function getRowByUserId(userId) {
	
	return $("#newGameTable tbody tr:not(#addFifthPlayerRow)").filter(function(i) {
			return $(".userSelect option:selected", this).data("user").userId === userId;
	});
	
}

function rankingTableToJson() {
	
	var users = [];
	var user;
	$("#newGameTable tbody tr:not(#addFifthPlayerRow)").each(function(i, tr) {
		var $tr = $(tr);
		user = $tr.find(".userSelect option:selected").data("user");
		user.points = parseInt($tr.find("input").val());
        user.uma = parseInt($tr.find(".umaCell").text());
        user.result = parseInt($tr.find(".resultCell").text());
		users.push(user);
	});
	return users;
	
} 

function computeScores() {
	
	var users = rankingTableToJson();
	var usersLength = users.length;
	if(usersLength < 4) {
		return;
	}
	users.sort(sortByPoints);
	var mods = usersLength === 4 ? FOUR_PLAYER_UMA : FIVE_PLAYER_UMA;
	var initialStack = parseInt($("#initialStackInput input").val());
	var i;
	var total = 0;
	for(i=0; i<usersLength; i++) {
		users[i].rank = i === 0 || users[i].points !== users[i-1].points ? i+1 : users[i-1].rank;
	}
	var usersByRank;
	var usersByRankLength;
	var modValues;
	var result;
	var j;
	for(i=1; i<=usersLength; i++) {
		usersByRank = [];
		for(j=0; j<usersLength; j++) {
			if(users[j].rank === i) {
				usersByRank.push(users[j]);
			}
		}
		usersByRankLength = usersByRank.length;
		if(usersByRankLength > 0) {
			modValues = 0;
			for(j=0; j<usersByRankLength; j++) {
				modValues += mods[j+i-1];
			}
			var result = usersByRank[0].points - initialStack + modValues / usersByRankLength;
            if($("#demiHanChanCheckBox").is(":checked")) {
                result /= 2;
            }
			for(j=0; j<usersByRankLength; j++) {			
				usersByRank[j].result = result;
				usersByRank[j].uma = modValues;
				getRowByUserId(usersByRank[j].userId).find(".umaCell").text(modValues);
				getRowByUserId(usersByRank[j].userId).find(".resultCell").text(result);
				total += result;
			}
		}
		
	}
	
	if(total === 0) {
		$("#resultValidity")
			.removeClass("glyphicon-remove")
			.addClass("glyphicon-ok")
			.css({
			"display": "inline",
			"color": "green"
		});
		$(".resultCell").css("color", "green");
		$("#saveToDb").css("display", "initial");
		for(i=0; i<usersLength; i++) {
			$tr = getRowByUserId(users[usersLength-1-i].userId)
			$tr.find(".rankCell").text(users[usersLength-1-i].rank)
			$tr.parent().prepend($tr);
		}
	} else {
		$("#resultValidity")
			.removeClass("glyphicon-ok")
			.addClass("glyphicon-remove")
			.css({
			"display": "inline",
			"color": "red"
		});
		$(".resultCell").css("color", "red");
		$("#saveToDb").css("display", "none");
		$(".rankCell").text("?");
		$("#scoreAnomaly").text(total > 0 ? "+" + total : total);
	}
		
}

function resetScores() {
	
	$("#resultValidity").css("display", "none");
	$("#saveToDb").css("display", "none");
	$(".rankCell").text("?");
	$(".resultCell").text("");
	$(".umaCell").text("");
	$("#scoreAnomaly").text("");
	
}

function resetPlayers() {
	
	$(".userSelect").select2().val("").trigger("change");
	computeUserSelects();
	
}

function resetTable() {
    
    resetPlayers();
    resetScores();
    
}

function sortByPoints(a, b) {
	
	return a.points < b.points ? 1 : a.points > b.points ? -1 : 0;
	
}

function sortByName(a, b) {
	
	return a.userDisplay.localeCompare(b.userDisplay);
	
}

function saveEvent() {
	
  var users = rankingTableToJson();
  var scores = JSON.stringify(users);
      var date = $("#newGameDate").length ?
          $("#newGameDate").val() :
          new Date().toISOString().slice(0, 10);
  var turnamentId = $("#turnamentSelect").val();
  $.post(SERVER_URL_PREFIX + "requests_controller.php", {
    "action"      : "insertScores",
    "scores"      : scores,
    "date"        : date,
    "turnamentId" : turnamentId
  }, function(result) {
    var $alert;
    if(result === "true") {
      $alert = $("#saveGameSuccess");
    } else {
      $alert = $("#saveGameFail").text(result);
    }
    $alert.slideDown();
    setTimeout(function() {
      $alert.slideUp();
    }, 3000);
    resetScores();
  });
	
}

function addFifthPlayer() {
	
	$("#addPlayerButton").hide();
    $("#removePlayerButton").show();
	var fifthRow = "<tr id=\"fifthPlayerRow\">\
					  <td class=\"rankCell\">?</td>\
					  <td><select class=\"userSelect\" style=\"width: 100%\" required></select></td>\
					  <td><input type=\"number\" class=\"form-control\" placeholder=\"30000\" step=\"100\" required/></td>\
				      <td class=\"umaCell\"></td>\
				      <td class=\"resultCell\"></td>\
					</tr>"
	$("#newGameTable tbody").append(fifthRow).append($("#addFifthPlayerRow"));
	$("#fifthPlayerRow .userSelect").select2({
		theme:	"bootstrap",
		placeholder: "Selectionnez un joueur",
		allowClear: true
	})
	  .on("select2:select", function (e) { computeUserSelects(); resetScores(); })
	  .on("select2:unselect", function (e) { computeUserSelects(); resetScores(); });
	$("#fifthPlayerRow input").change(function() { resetScores(); });
	computeUserSelects();
	resetScores();
	
}

function removeFifthPlayer() {
	
	$("#removePlayerButton").hide();
    $("#addPlayerButton").show();
	$("#fifthPlayerRow").remove();
	computeUserSelects();
	resetScores();
	
}

$(document).ready(function() {
  
  $("#removePlayerButton").hide().on("click", function(e) {
    removeFifthPlayer(); 
  });
  $("#saveToDb").css("display", "none");
	
  $.post(SERVER_URL_PREFIX + "requests_controller.php", {
    "action"      : "getMyUserDisplayName"
  }, function(result) {
    $(".userSelect").select2({
      theme:	"bootstrap",
      placeholder: "Selectionnez un joueur",
      allowClear: true,
      templateResult: function(data, container) {
        if(data.text.toLowerCase() === result.toLowerCase()) {
          $(container).css("backgroundColor", "#edf1ff");
        }
        return data.text;
      }
    });
    $(".userSelect").on("select2:select", function (e) { computeUserSelects(); resetScores(); })
      .on("select2:unselect", function (e) { computeUserSelects(); resetScores(); });
  });
	
	$("#newGameTable tbody input:not(#demiHanChanCheckBox)").change(function() { resetScores(); });
	$("#addPlayerButton").on("click", function(e) {
		addFifthPlayer();
	}); 
  $("#demiHanChanCheckBox").on("click", function(e) {
    var proceed = true;
    $(".resultCell").each(function(i, value) {
      if($(value).text() === "") { proceed = false; }  
    });
    if(!proceed) { return; }
    computeScores(); 
  });
  $("#initialStackInput input").val(DEFAULT_INITIAL_STACK);

	loadUsers();
  loadTurnaments();
	
});