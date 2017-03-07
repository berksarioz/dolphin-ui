
$('select').on('change', function() {
  $('.st_search').val('btn-' + this.value );
	$('.st_search').click();
	$('.st_search').keypress();
	$('.st_search').submit();
})

function getBrowsingDataExperiments(){
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/ngs_tables.php",
		data: { p: 'getExperimentSeries' },
		async: false,
		success : function(s)
		{
      for(var i = 0; i < s.length; i++ ){
        s[i].experiment_name = "<a href=\""+BASE_PATH+
          "/search/details/experiment_series/"+s[i].id+
          "\">"+s[i].experiment_name+"</a>";

        s[i].options = '<input type="checkbox" class="ngs_checkbox" name="' + s[i].id +
        '" id="experiment_checkbox_' + s[i].id +
        '" onclick="manageChecklists(this.name, \'experiment_checkbox\')">';
			}
			console.log("+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-");
			console.log(s);
			groupsStreamTable = createStreamTable('browse_experiment', s, "", true, [10,20,50,100], 20, true, true);
		}
	});
}

function getBrowsingDataImports(){
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/ngs_tables.php",
		data: { p: 'getLanesNew' },
		async: false,
		success : function(s)
		{
      for(var i = 0; i < s.length; i++ ){
        s[i].options = '<input type="checkbox" class="ngs_checkbox" name="' + s[i].id +
        '" id="lane_checkbox_' + s[i].id +
        '" onclick="manageChecklists(this.name, \'lane_checkbox\')">';
			}
			console.log("+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-");
			console.log(s);
			groupsStreamTable = createStreamTable('browse_import', s, "", true, [10,20,50,100], 20, true, true);
		}
	});
}


function getBrowsingDataSamples(){
  console.log("+++-+++-+++-+++-999999999++-+++-+++-");

	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/ngs_tables.php",
		data: { p: 'getSamplesNew'},
		async: false,
		success : function(s)
		{
      for(var i = 0; i < s.length; i++ ){
        s[i].options = '<input type="checkbox" class="ngs_checkbox" name="' + s[i].id +
        '" id="sample_checkbox_' + s[i].id +
        '" onclick="manageChecklists(this.name, \'sample_checkbox\')">';
			}
			console.log("+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-");
			console.log(s);
			groupsStreamTable = createStreamTable('browse_sample', s, "", true, [10,20,50,100], 20, true, true);
		}
	});
}


function replaceNulls(){
  $("td:contains('null')").html("");
}

function updateTable(){
  $('#jsontable_browse_sample').change(function() {
    replaceNulls();
  });
}


$(function() {
	"use strict";


	//	GROUPS
	getBrowsingDataExperiments();
	getBrowsingDataImports();
	getBrowsingDataSamples();
  replaceNulls();
  // updateTable();
});
