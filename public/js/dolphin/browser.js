
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
			console.log("+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-");
			console.log(s);
			groupsStreamTable = createStreamTable('browse_experiment', s, "", true, [10,20,50,100], 20, true, true);
		}
	});
}

function getBrowsingDataImports(){
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/ngs_tables.php",
		data: { p: 'getLanes' },
		async: false,
		success : function(s)
		{
			console.log("+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-");
			console.log(s);
			groupsStreamTable = createStreamTable('browse_import', s, "", true, [10,20,50,100], 20, true, true);
		}
	});
}


function getBrowsingDataSamples(){
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/ngs_tables.php",
		data: { p: 'getSamples', q: '' },
		async: false,
		success : function(s)
		{
			console.log("+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-+++-");
			console.log(s);
			groupsStreamTable = createStreamTable('browse_sample', s, "", true, [10,20,50,100], 20, true, true);
		}
	});
}



$(function() {
	"use strict";


	//	GROUPS
	getBrowsingDataExperiments();
	getBrowsingDataImports();
	getBrowsingDataSamples();
});
