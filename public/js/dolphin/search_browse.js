function fillSampleTable(){
  if($('#table_div_samples').length == 0){
		$.ajax({ type: "GET",
    url: BASE_PATH+"/public/ajax/search_browse.php",
		data: { p: 'getSearchSamples' },
    async: false,
			success : function(s)
			{
        console.log(s);
          $('#browse_sample_data_table').html(s);
          ngsTrackCopy();

			}
		});
  }
}





function ngsTrackCopy(){

  $(function() {
  	"use strict";

  	//The Calender
  	$("#calendar").datepicker();
  	$('input').on('ifChanged', function(event){
  		if (event.target.name.substring(0,6) == "common") {
  			var array = event.target.id.split("_");

  			if (array[1] == 'yes' && deseqList.indexOf(array[0]) == -1 && (array[0] == 'miRNA' || array[0] == 'tRNA')) {
  				var selects = document.getElementsByTagName("select");
  				for(var i = 0; i < selects.length; i++) {
  					if(selects[i].id.indexOf('select_5_') == 0) {
  						var opt = createElement('option', ['id', 'value'], [array[0], array[0]]);
  						opt.innerHTML = array[0];
  						selects[i].appendChild(opt);
  					}
  				}
  				deseqList.push(array[0]);
  			}else if (array[1] == 'no' && deseqList.indexOf(array[0]) > -1 ){
  				deseqList.splice(deseqList.indexOf(array[0]), 1);
  				var selects = document.getElementsByTagName("select");
  				for(var i = 0; i < selects.length; i++) {
  					if(selects[i].id.indexOf('select_5_') == 0) {
  						var children = selects[i].childNodes;
  						for (var y = 0; y < children.length; y++) {
  							if (children[y].id.split("_")[0] == array[0]) {
  								children[y].remove();
  							}
  						}
  					}
  				}
  			}
  		}
  	});

  	/*##### PAGE DETERMINER #####*/

  	var qvar = "";
  	var rvar = "";
  	var segment = "";
  	var theSearch = "";
  	var uid = "";
  	var gids = "";

  	if (phpGrab) {
  		var segment = phpGrab.theSegment;
  		var theSearch = phpGrab.theSearch;
  		uid = phpGrab.uid;
  		gids = phpGrab.gids;
  	}

  	//gids
  	if (gids == '') {
  		gids = -1;
  	}

  	//Details values
  	if (segment == "details") {
  		if (phpGrab.theField == "experiment_series") {
  			qvar = phpGrab.theValue;
  		}
  		else if (phpGrab.theField == "experiments") {
  			rvar = phpGrab.theValue;
  		}
  	}

  	//Browse values
  	else if (segment == "browse") {
  		qvar = phpGrab.theField;//field
  		rvar = unescape(phpGrab.theValue);//value
  	}

  	if (phpGrab.theField == "samples") {
  		reloadBasket();
  	}
  	//	If table generated page
  	if (phpGrab.theSegment == 'generated') {
  		//	Obtain table information (JSON format)
  		$.ajax({ type: "GET",
  			url: BASE_PATH +"/public/ajax/tablegenerator.php?",
  			data: { p: "getGeneratedTable"},
  			async: false,
  			success : function(s)
  			{
  				console.log(s);
  				table_params = s;
  			}
  		});
  		var json_obj;
  		//	If file does exist
  		if (table_params.file != '') {
  			//	Load JSON file
  			json_obj = undefined;
  			//	Obtain JSON data
  			$.ajax({ type: "GET",
  				url: BASE_PATH +"/public/ajax/tablegenerator.php",
  				data: { p: "getJsonFromFile", file: table_params.file},
  				async: false,
  				success : function(s)
  				{
  					json_obj = s;
  				}
  			});
  			//	If no information can be obtained
  			if (json_obj == undefined || json_obj == []) {
  				//	Generate JSON data manually
  				$.ajax({ type: "GET",
  					url: BASE_PATH +"/public/api/getsamplevals.php?" + table_params.parameters,
  					async: false,
  					success : function(s)
  					{
  						json_obj = JSON.parse(s);
  						generateStreamTable('generated', json_obj, phpGrab.theSegment, qvar, rvar, segment, theSearch, uid, gids);
  					}
  				});
  				//	Update table with new file information
  				//	Create both JSON and JSON2 files
  				$.ajax({ type: "GET",
  					url: BASE_PATH +"/public/ajax/tablegenerator.php",
  					data: { p: "updateTableFile", url: BASE_PATH +"/public/api/getsamplevals.php?" + table_params.parameters, id: table_params.id},
  					async: false,
  					success : function(s)
  					{
  						console.log(s);
  					}
  				});
  			}else{
  				//	Generate table with obtained JSON information from file
  				generateStreamTable('generated', json_obj, phpGrab.theSegment, qvar, rvar, segment, theSearch, uid, gids);
  			}
  		//	If file doesn't exist
  		}else{
  			//	Generate JSON data manually
  			$.ajax({ type: "GET",
  				url: BASE_PATH +"/public/api/getsamplevals.php?" + table_params.parameters,
  				async: false,
  				success : function(s)
  				{
  					json_obj = JSON.parse(s);
  					generateStreamTable('generated', json_obj, phpGrab.theSegment, qvar, rvar, segment, theSearch, uid, gids);
  				}
  			});
  			//	Update table with new file information
  				//	Create both JSON and JSON2 files
  			$.ajax({ type: "GET",
  				url: BASE_PATH +"/public/ajax/tablegenerator.php",
  				data: { p: "updateTableFile", url: BASE_PATH +"/public/api/getsamplevals.php?" + table_params.parameters, id: table_params.id},
  				async: false,
  				success : function(s)
  				{
  					console.log(s);
  				}
  			});
  		}

  		//	Plot variables contained within highchart_funcs.js
  		//	Dictionaries contained within report_funcs.js
  		var table_array = json_obj;
  		for( var j = 0; j < table_array.length; j++){
  			for( var i = 0; i < summary_dictionary.length; i++){
  				if (table_array[j][summary_dictionary[i]] != undefined) {
  					if (table_data[table_array[j]['Sample']] == undefined) {
  						table_data[table_array[j]['Sample']] = {};
  						if (summary_dictionary[i] != 'Sample') {
  							table_data[table_array[j]['Sample']][html_summary_dictionary[i]] = table_array[j][summary_dictionary[i]];
  						}
  					}else{
  						if (summary_dictionary[i] != 'Sample') {
  							table_data[table_array[j]['Sample']][html_summary_dictionary[i]] = table_array[j][summary_dictionary[i]];
  						}
  					}
  				}
  			}
  		}

  		//	Function within report_fincs.js
  		console.log("@@@@@@@  Highchart Logging Start @@@@@@@")
  		console.log(table_params);
  		console.log(table_array)

  		if (Object.keys(table_data).length > 0) {
  			summaryPlotSetup(table_data);
  			console.log(table_data);
  			createSummaryHighchart();
  			showHighchart('plots');
  		}

  		if (/RSeQC/.test(table_params.parameters) && /counts.tsv/.test(table_params.parameters)) {
  			rseqcPlotGen('rseqc', json_obj, 'generated_table')
  		}

  		//	Log data path
  		console.log(BASE_PATH +"/public/api/getsamplevals.php?" + table_params.parameters);

  	}else if (phpGrab.theSegment != 'report' && phpGrab.theSegment != 'table_viewer' && phpGrab.theSegment != "encode_submissions") {
  		var experiment_series_data = [];
  		var lane_data = [];
  		var sample_data = [];

  		/*##### SAMPLES TABLE #####*/
  		//var samplesTable = $('#jsontable_samples').dataTable();

  		var samplesType = "";
  		if (segment == 'selected') {
  			samplesType = "getSelectedSamples";
  			if (window.location.href.indexOf("/rerun/") == -1) {
  				theSearch = basket_info;
  			}
  			if (window.location.href.split("/").indexOf('tablecreator') > -1) {
  				qvar = "getTableSamples";
  			}
  		}else{
  			samplesType = "getSamples";
  		}
  		console.log(basket_info);
  		$.ajax({ type: "GET",
  			url: BASE_PATH+"/public/ajax/ngs_tables.php",
  			data: { p: samplesType, q: qvar, r: rvar, seg: segment, search: theSearch, uid: uid, gids: gids },
  			async: false,
  			success : function(s)
  			{
  				console.log(s);
  				sample_data = s;
  				var changeHTML = '';
  				var hrefSplit = window.location.href.split("/");
  				var typeLocSelected = $.inArray('selected', hrefSplit);
  				var typeLocRerun = $.inArray('rerun', hrefSplit);
  				var queryType = 'getSamples';
  				if (typeLocSelected > 0 || typeLocRerun > 0) {
  					theSearch = '';
  				}
  				var type = 'samples';
  				if (samplesType == 'getSamples' && segment == 'table_create') {
  					var samples_with_runs = [];
  					var objects_with_runs = [];
  					$.ajax({ type: "GET",
  						url: BASE_PATH+"/public/ajax/tablegenerator.php",
  						data: { p: "samplesWithRuns" },
  						async: false,
  						success : function(k)
  						{
  							for(var x = 0; x < k.length; x++){
  								samples_with_runs.push(k[x].sample_id);
  							}
  						}
  					});
  					console.log(samples_with_runs);
  					for(var z = 0; z < s.length; z++){
  						if (samples_with_runs.indexOf(s[z].id) > -1) {
  							objects_with_runs.push(s[z]);
  						}
  					}
  					console.log(objects_with_runs);
  					s = objects_with_runs;
  					queryType = 'table_create';
  				}
  				if (segment == 'selected') {
  					var runparams = $('#jsontable_samples_selected').dataTable();
  					runparams.fnClearTable();
  					for(var i = 0; i < s.length; i++){
  						var selection_bool = false;
  						if (window.location.href.indexOf("/rerun/") > -1 || window.location.href.indexOf("/selected/") > -1) {
  							selection_bool = true;
  						}
  						if (selection_bool) {
  							runparams.fnAddData([
  								s[i].id,
  								s[i].samplename,
  								s[i].organism,
  								s[i].molecule,
  								s[i].backup,
  								'<button id="sample_removal_'+s[i].id+'" class="btn btn-danger btn-xs pull-right" onclick="removeSampleSelection(\''+s[i].id+'\', this)"><i class=\"fa fa-times\"></i></button>'
  							]);
  							selected_samples.push(s[i].id);
  						}
  					}
  					samplesType = "getSamples";
  					$.ajax({ type: "GET",
  						url: BASE_PATH+"/public/ajax/ngs_tables.php",
  						data: { p: samplesType, q: qvar, r: rvar, seg: segment, search: theSearch, uid: uid, gids: gids },
  						async: false,
  						success : function(k)
  						{
  							generateStreamTable(type, k, queryType, qvar, rvar, segment, theSearch, uid, gids);
  							manageChecklistsBulk(selected_samples)
  						}
  					});
  				}else if (segment == "encode") {
  					generateStreamTable(type, s, queryType, qvar, rvar, segment, theSearch, uid, gids);
  					var basket = basket_info.split(",");
  					basket = basket.filter(function(e){return e});
  					manageChecklistsBulk(basket);
  				}else{
  					generateStreamTable(type, s, queryType, qvar, rvar, segment, theSearch, uid, gids);
  				}
  			}
  		});

  		// /*##### LANES TABLE #####*/
      //
  		// $.ajax({ type: "GET",
  		// 	url: BASE_PATH+"/public/ajax/ngs_tables.php",
  		// 	data: { p: "getLanes", q: qvar, r: rvar, seg: segment, search: theSearch, uid: uid, gids: gids },
  		// 	async: false,
  		// 	success : function(s)
  		// 	{
  		// 		lane_data = s;
  		// 		var type = 'lanes';
  		// 		var queryType = "getLanes";
  		// 		if (window.location.href.split("/").indexOf('search') > -1) {
  		// 			generateStreamTable(type, s, queryType, qvar, rvar, segment, theSearch, uid, gids);
  		// 		}
  		// 	}
  		// });
      //
  		// /*##### SERIES TABLE #####*/
  		// //var experiment_seriesTable = $('#jsontable_experiment_series').dataTable({responsive: true});
      //
  		// $.ajax({ type: "GET",
  		// 	url: BASE_PATH+"/public/ajax/ngs_tables.php",
  		// 	data: { p: "getExperimentSeries", q: qvar, r: rvar, seg: segment, search: theSearch, uid: uid, gids: gids },
  		// 	async: false,
  		// 	success : function(s)
  		// 	{
  		// 		experiment_series_data = s;
  		// 		var type = 'experiments';
  		// 		var queryType = "getExperimentSeries";
  		// 		if (window.location.href.split("/").indexOf('search') > -1) {
  		// 			generateStreamTable(type, s, queryType, qvar, rvar, segment, theSearch, uid, gids);
  		// 		}
  		// 	}
  		// });

  		if (segment == 'index' || segment == 'browse' || segment == 'details') {
  			console.log(experiment_series_data);
  			console.log(lane_data);
  			console.log(sample_data);
  			generateIDDictionary(experiment_series_data, lane_data, sample_data);
  			reloadBasket();
  		}
  	}
  	//Rerun Check
  	if (window.location.href.split("/").indexOf('selected') > -1 || window.location.href.split("/").indexOf('rerun') > -1) {
  		rerunLoad();
  	}
  });

}
