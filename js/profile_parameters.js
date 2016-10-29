$(document).ready(function() {

  $("#parametersSubmitButton").on("click", function(e) {
    $.post(SERVER_URL_PREFIX + "requests_controller.php", {
      "action" : "changeDisplayName",
      "displayName" : $("#inputDisplayName").val()
    }, function(result) {
      var $alert;
      if(result === "true") {
        $alert = $("#saveChangesSuccess");
      } else {
        $alert = $("#saveChangesFail").text(result);
      }
      $alert.slideDown();
      setTimeout(function() {
        $alert.slideUp();
        location.reload(true);
      }, 3000);
    });
  });

});