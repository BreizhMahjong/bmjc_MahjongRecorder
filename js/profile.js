var userId;

$(document).ready(function() {
  
  $.post(SERVER_URL_PREFIX + "requests_controller.php", {
      "action" : "getMyUserId",
    }, function(result) {
      userId = result;
      executeContentJs();
  });

});