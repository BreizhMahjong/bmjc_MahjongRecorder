function updateTable() {

  toggleLoading();
  if($("#table").children().length) {
    $("#table").DataTable().destroy();
  }
  $("#chart").empty();
  $("#table").empty();
  var stat = $("option:selected").val();
  $.post(SERVER_URL_PREFIX + "requests_controller.php", {
	  "action" : stat,
	}, function(result) {
    toggleLoading();
    var json = $.parseJSON(result);
    if(json.table !== undefined) {
      $("#table").DataTable({
        "paging"    : false,
        "ordering"  : false,
        "info"      : false,
        "searching" : false,
        "columns"   : json.table.columns,
        "data"      : json.table.rows,
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
  
  $("#table").show();
  $(".loadingImage").hide();
  $.ajax({
    url     : "requests_controller.php",
    type    : "POST",
    data    : {"action" : "collectStatsList"},
    success : function(result){
      $("#statSelect").select2({
        "data": $.parseJSON(result)
      });
      updateTable();
    }
  });
  
  $("#statSelect").on("select2:select", function(e) {
    updateTable();
  });
  
});