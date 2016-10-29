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

function updateTable() {

  toggleLoading();
  if($("#table").children().length) {
    $("#table").DataTable().destroy();
  }
  $("#chart").empty();
  $("#table").empty();
  $.post(SERVER_URL_PREFIX + "requests_controller.php", {
	  "action" : "collectScores",
    "from"   : getSeasonStartDate(),
    "to"     : getSeasonEndDate()
	}, function(result) {
    toggleLoading();
    var json = $.parseJSON(result);
    if(json.table !== undefined) {
      $("#table").DataTable({
        "paging"    : false,
        "ordering"  : true,
        "info"      : false,
        "searching" : false,
        "columns"   : json.table.columns,
        "data"      : json.table.rows
      });
    }
    if(json.chart !== undefined) {
      $("#chart").highcharts(json.chart);
    }
  }).fail(function() {
    toggleLoading();
  }); 
  
  
}

$(document).ready(function() {
	
  
  $(".loadingImage").hide();
  $("#table").show();
    
  $("#seasonSelect").on("select2:select", function(e) {
      updateTable();
  });
  
  $.ajax({
      url     : "requests_controller.php",
      type    : "POST",
      data    : {"action" : "collectRankingDates"},
      success : function(result){
        $("#seasonSelect").select2({
            "data": $.parseJSON(result)
        });
        updateTable();
      }
  });
    
    
	
});