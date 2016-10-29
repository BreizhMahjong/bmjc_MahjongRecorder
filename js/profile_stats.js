function getSeasonStartDate() {
    
    if ($("#seasonSelect").val() !== null) {
        var startDate = $("#seasonSelect").val().split("/")[0];
        return startDate;
    } else {
        return null;
    }
    
}

function getSeasonEndDate() {
    
    if ($("#seasonSelect").val() !== null) {
        var endDate = $("#seasonSelect").val().split("/")[1];
        return endDate;
    } else {
        return null;
    }
    
}

function updateChart() {

  toggleLoading();
  
  $("#chart").empty();
  $.post(SERVER_URL_PREFIX + "requests_controller.php", {
	  "action" : "profile_individualScores",
    "from"   : getSeasonStartDate(),
    "to"     : getSeasonEndDate(),
    "userId" : userId
	}, function(result) {
    toggleLoading();
    var json = $.parseJSON(result);
    $("#chart").highcharts(json.chart);
  }).fail(function() {
    toggleLoading();
  }); 
  
  
}


function updateTable() {
  
    // todo
  
}


function updateStats() {
  
  updateChart();
  updateTable();
  
}


function executeContentJs() {
  
  $("#gameList").show();
  $(".loadingImage").hide();
  
  $("#seasonSelect").on("select2:select", function(e) {
      updateStats();
  });
  
  $.ajax({
      url     : "requests_controller.php",
      type    : "POST",
      data    : {
        "action" : "collectRankingDates",
        "withAll" : 1
      },
      success : function(result){
        $("#seasonSelect").select2({
            "data": $.parseJSON(result)
        });
        updateStats();
      }
  });
 
}