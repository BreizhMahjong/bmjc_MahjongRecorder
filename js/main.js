var SERVER_URL_PREFIX = "http://breizhmahjong.fr/bmjc/";

function loginEvent() {
	
	var login = $("#loginInput").val();
	var password = $("#passwordInput").val();
	$.post(SERVER_URL_PREFIX + "requests_controller.php", {
			"action": "signon",
			"login": login,
			"pass": password
		}, function(result) {
		if($.parseJSON(result).success === "true") {
			location.reload();
		} else {
			$("#loginError").text("Wrong login or password");
		}
	});
	
}

function logoutEvent() {
  
  $.post(SERVER_URL_PREFIX + "requests_controller.php", {
			"action": "signoff"
		}, function(result) {
			location.reload();
    });
  
}

function toggleLoading() {
  
  $("#table").toggle();
  $("#chart").toggle();
  $(".loadingImage").toggle();
  
}


$(document).ready(function() {
  
  $(".nav li.disabled a").click(function() {
    return false;
  });
  
  $("#loginButton").leanModal({ "top": 20, "overlay": 0.4, "closeButton": ".modal_close" });
  $("#logoutButton").on("click", function() {
    logoutEvent();
  });
   
  $(".userLogo").error(function() {
    var userLogo = $(this);
    userLogo.attr("src", "http://breizhmahjong.fr/wp-content/uploads/2015/11/bambou-1.png");
    userLogo.load();
  });
   
});