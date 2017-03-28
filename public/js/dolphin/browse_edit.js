/*
 *Author: Nicholas Merowsky
 *Date: 09 Apr 2015
 *Ascription:
 */

var element_highlighted;
var element_highlighted_table;
var element_highlighted_value;
var element_highlighted_uid;
var element_highlighted_id;
var element_highlighted_type;
var element_highlighted_onclick;

var element_parent_table = '';
var element_parent_table_id = '';
var element_parent_child = '';

var experimentPerms = [];
var lanePerms = [];
var samplePerms = [];

var normalized = ['facility', 'source', 'organism', 'molecule', 'lab', 'organization', 'genotype', 'library_type',
				  'instrument_model', 'treatment_manufacturer', 'donor', 'name', 'target'];
var fileDatabaseDict = ['ngs_dirs', 'ngs_temp_sample_files', 'ngs_temp_lane_files', 'ngs_fastq_files'];

var singlecheck_table = '';
var allcheck = { sample:[], donor:[], experiment:[], treatment:[], biosample:[], library:[], antibody:[], replicate:[] }
var allcheck_table = '';
var allcheck_bool = false;
var selected_ids = [];

function editBox(uid, id, type, table, element, parent_table, parent_table_id, parent_child){
	var havePermission = 0;
	console.log([uid, id, type, table, element]);
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/browse_edit.php",
		data: { p: 'checkPerms', id: id, uid: uid, table: table},
		async: false,
		success : function(r)
		{
			havePermission = r;
		}
	});
	
	if (havePermission == 1) {
		if (element_highlighted != null) {
			if (element_highlighted_value != ""){
				element_highlighted.innerHTML = element_highlighted_value;
			}else{
				element_highlighted.innerHTML = '<br>';
			}
			element_highlighted.onclick = element_highlighted_onclick;
			if (fileDatabaseDict.indexOf(table) > -1) {
				document.getElementById('submit_file_changes').remove();
				document.getElementById('cancel_file_changes').remove();
			}
		}
		element_highlighted = element;
		if (element.innerHTML != "<br>"){
			element_highlighted_value = element.innerHTML;
		}else{
			element_highlighted_value = '';
		}
		element_highlighted_uid = uid;
		element_highlighted_id = id;
		element_highlighted_type = type;
		element_highlighted_table = table;
		element_highlighted_onclick = element.onclick;
		element.innerHTML = '';
		if (parent_table != '') {
			element_parent_table = parent_table;
			element_parent_table_id = parent_table_id;
			element_parent_child = parent_child;
		}
		
		if (normalized.indexOf(type) > -1) {
			
			element.onclick = '';
			
			var masterDiv = document.createElement('div');
			masterDiv.setAttribute('class','combobox input-group');
			
			var input = document.createElement('input');
			input.setAttribute('type', 'text');
			input.setAttribute('name', 'comboboxfieldname');
			input.setAttribute('id','cb_identifier');
			input.setAttribute('class','cb_identifier');
			input.value = element_highlighted_value;
			
			var button = document.createElement('button');
			button.setAttribute('class', 'btn btn-default btn-group pull-right')
			
			var span = document.createElement('span');
			span.setAttribute('class','caret');
			button.appendChild(span);
			
			var div = document.createElement('div');
			div.setAttribute('class','dropdownlist');
			div.setAttribute('style','z-index: 999');
			
			$.ajax({ type: "GET",
				url: BASE_PATH+"/public/ajax/browse_edit.php",
				data: { p: 'getDropdownValues', type: type},
				async: false,
				success : function(r)
				{
					for(var x = 0; x < r.length; x++){
						var a = document.createElement('a');
						a.innerHTML = (r[x][type]);
						div.appendChild(a);
					}
				}
			});
			
			element.appendChild(masterDiv);
			masterDiv.appendChild(input);
			masterDiv.appendChild(button);
			masterDiv.appendChild(div);
			var no = new ComboBox('cb_identifier');
		}else{
			var submitButton;
			var cancelButton;
			var textarea = document.createElement('textarea');
			
			textarea.setAttribute('id', 'inputTextBox');
			textarea.setAttribute('type', 'text');
			textarea.setAttribute('class', 'form-control');
			textarea.setAttribute('rows', '5');
			element.setAttribute('value', element_highlighted_value);
			if (table == 'ngs_dirs' || table == 'ngs_fastq_files' || table == 'ngs_temp_sample_files' || table == 'ngs_temp_lane_files') {
				submitButton = createElement('input', ['id','class','onclick','value','type'],['submit_file_changes','btn btn-primary pull-right margin', 'submitChangesFiles()', 'Submit','button']);
				cancelButton = createElement('input', ['id','class','onclick','value','type'],['cancel_file_changes','btn btn-default pull-right margin', 'cancelChangesFiles()', 'Cancel','button']);
			}else{
				textarea.setAttribute('onkeydown', 'submitChanges(this, event)');
			}
			element.appendChild(textarea);
			if (submitButton != undefined) {
				element.parentNode.parentNode.parentNode.appendChild(cancelButton);
				element.parentNode.parentNode.parentNode.appendChild(submitButton);
			}
			textarea.innerHTML = element_highlighted_value;
			element.onclick = '';
			element_highlighted.onclick = '';
		}
	}
}

function fastlaneDropdown(type, table, html_id){
	var element = document.getElementById(html_id);
	element.innerHTML = '';
	
	var masterDiv = document.createElement('div');
	masterDiv.setAttribute('class','combobox input-group');
	
	var input = document.createElement('input');
	input.setAttribute('type', 'text');
	input.setAttribute('name', 'comboboxfieldname');
	input.setAttribute('id','cb_identifier');
	input.setAttribute('class','cb_identifier');
	
	var button = document.createElement('button');
	button.setAttribute('class', 'btn btn-default btn-group pull-right')
	
	var span = document.createElement('span');
	span.setAttribute('class','caret');
	button.appendChild(span);
	
	var div = document.createElement('div');
	div.setAttribute('class','dropdownlist');
	div.setAttribute('style','z-index: 999');
	
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/browse_edit.php",
		data: { p: 'getDropdownValuesPerms', type: type, table: table, gids: phpGrab.gids},
		async: false,
		success : function(r)
		{
			console.log(r)
			for(var x = 0; x < r.length; x++){
				var a = document.createElement('a');
				a.innerHTML = (r[x][type]);
				div.appendChild(a);
			}
		}
	});
	
	element.appendChild(masterDiv);
	masterDiv.appendChild(input);
	masterDiv.appendChild(button);
	masterDiv.appendChild(div);
	var no = new ComboBox('cb_identifier');
	console.log('test');
}

function cancelChangesFiles(){
	submitChanges('details_cancel', 27);
}

function submitChangesFiles(){
	$('#fileModal').modal({
		show: true
	});
	document.getElementById('confirmFileButton').setAttribute('onclick', 'submitChanges("dir_element", 13)');
}

function submitChanges(ele, event = event) {
	var successBool = false;
	if (ele == 'details_cancel') {
		element_highlighted.innerHTML = element_highlighted_value;
		element_highlighted.onclick = element_highlighted_onclick;
		document.getElementById('submit_file_changes').remove();
		document.getElementById('cancel_file_changes').remove();
		clearElementHighlighted();
	}else if((event.keyCode == 13) || event == 13) {
		if (ele == "dir_element") {
			ele = document.getElementById('inputTextBox');
		}
		if (element_parent_table != '' && element_highlighted_id == '<br>') {
			console.log('@@@@@ INSERT @@@@@')
			console.log(element_highlighted_type)
			console.log(element_highlighted_table)
			console.log(ele.value)
			console.log(element_parent_table)
			console.log(element_parent_table_id)
			console.log(element_parent_child)
			$.ajax({ type: "GET",
				url: BASE_PATH+"/public/ajax/browse_edit.php",
				data: { p: 'insertDatabase', type: element_highlighted_type, table: element_highlighted_table, value: ele.value, parent: element_parent_table, parent_id: element_parent_table_id, parent_child: element_parent_child},
				async: false,
				success : function(r)
				{
					console.log(r)
					if (r == 1) {
						successBool = true;
						element_parent_table = '';
						element_parent_table_id = '';
						element_parent_child = '';
					}
					updateEncodeSubmissions();
				}
			});
		}else{
			var updateType = 'updateDatabase';
			if (window.location.href.split("/").indexOf("encode") > -1 ){
				updateType = 'updateDatabaseEncode';
			}
			console.log('@@@@@ UPDATE @@@@@')
			console.log(element_highlighted_id)
			console.log(element_highlighted_type)
			console.log(element_highlighted_table)
			console.log(ele.value)
			console.log(element_parent_table)
			console.log(element_parent_table_id)
			console.log(element_parent_child)
			$.ajax({ type: "GET",
				url: BASE_PATH+"/public/ajax/browse_edit.php",
				data: { p: updateType, id: element_highlighted_id, type: element_highlighted_type, table: element_highlighted_table, value: ele.value, parent: element_parent_table, parent_id: element_parent_table_id, parent_child: element_parent_child},
				async: false,
				success : function(r)
				{
					console.log(r)
					if (r == 1) {
						successBool = true;
					}
					updateEncodeSubmissions();
				}
			});
		}
		if (successBool) {
			console.log("Success!")
			if (singlecheck_table != '') {
				updateSingleTable(singlecheck_table);
				singlecheck_table = '';
			}
			if (ele.value == '') {
				element_highlighted.innerHTML = '<br>';
			}else{
				element_highlighted.innerHTML = ele.value;
			}
			element_highlighted.onclick = element_highlighted_onclick;
			if (document.getElementById('submit_file_changes') != undefined) {
				document.getElementById('submit_file_changes').remove();
			}
			if (document.getElementById('cancel_file_changes') != undefined) {
				document.getElementById('cancel_file_changes').remove();
			}
			clearElementHighlighted();
		}
	}else if (event.type=="click") {
		ele = document.getElementById('inputTextBox');
		if (ele == null) {
			ele = document.getElementById('cb_identifier');
		}
		
		if (element_parent_table != '' && element_highlighted_id == '<br>') {
			$.ajax({ type: "GET",
				url: BASE_PATH+"/public/ajax/browse_edit.php",
				data: { p: 'insertDatabaseMulti', type: element_highlighted_type, table: element_highlighted_table, value: ele.value, parent: element_parent_table, parent_id: selected_ids.toString(), parent_child: element_parent_child},
				async: false,
				success : function(r)
				{
					console.log(r)
					if (r == 1) {
						successBool = true;
					}
					updateEncodeSubmissions();
				}
			});
		}else{
			var updateType = 'updateDatabaseMulti';
			if (window.location.href.split("/").indexOf("encode") > -1 ){
				updateType = 'updateDatabaseMultiEncode';
			}
			$.ajax({ type: "GET",
				url: BASE_PATH+"/public/ajax/browse_edit.php",
				data: { p: updateType, id: selected_ids.toString(), type: element_highlighted_type, table: element_highlighted_table, value: ele.value, parent: element_parent_table, parent_child: element_parent_child},
				async: false,
				success : function(r)
				{
					console.log(r)
					if (r == 1) {
						successBool = true;
					}
					updateEncodeSubmissions();
				},
				error: function(e)
				{
					console.log(e)
				}
			});
		}
		if (successBool) {
			console.log("Success!")
			element_highlighted.innerHTML = ele.value;
			element_highlighted.onclick = element_highlighted_onclick;
			if (document.getElementById('submit_file_changes') != undefined) {
				document.getElementById('submit_file_changes').remove();
			}
			if (document.getElementById('cancel_file_changes') != undefined) {
				document.getElementById('cancel_file_changes').remove();
			}
			clearElementHighlighted();
		}
	}else if(event.keyCode == 27) {
		if (element_highlighted_value == '') {
			element_highlighted_value = '<br>';
		}
		element_highlighted.innerHTML = element_highlighted_value;
		element_highlighted.onclick = element_highlighted_onclick;
		
		clearElementHighlighted();
	}
	allcheck_table = '';
	allcheck_bool = false;
}

function updateEncodeSubmissions(type){
	ajax_id = element_highlighted_id
	if (type == 'multi') {
		ajax_id = selected_ids.toString();
	}
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/browse_edit.php",
		data: { p: 'encodeSampleEdit', table: element_highlighted_table, field: element_highlighted_type, id:ajax_id, sample_id:element_parent_table_id  },
		async: false,
		success : function(r)
		{
			console.log(r)
		}
	});
	if (window.location.href.split("/").indexOf('encode') > -1) {
		var table = $('.nav-tabs .active').text().replace(/[\s\t\n\r]/g,'');
		updateSingleTable(table);
	}
}

function deleteButton(){
	$('#deleteModal').modal({
		show: true
	});
	var html = window.location.href.split("/");
	if (html.indexOf("details") == -1 && html.indexOf("browse") == -1) {
		if (checklist_samples.length == 0 && checklist_lanes.length == 0 && checklist_experiment_series.length == 0){
			document.getElementById('myModalLabel').innerHTML = 'Delete Error';
			document.getElementById('deleteLabel').innerHTML = 'You must make a selection to delete to continue.';
			
			document.getElementById('cancelDeleteButton').innerHTML = "OK";
			document.getElementById('confirmDeleteButton').setAttribute('style', 'display:none');
		}else if (checklist_experiment_series.length > 0) {
			document.getElementById('myModalLabel').innerHTML = 'Delete Experiment Series';
			document.getElementById('deleteLabel').innerHTML = 'Warning!  You have selected to remove an experiment series!';
			document.getElementById('deleteAreas').innerHTML = 'Are you sure you want to continue?<br>'+'Experiment series id(s): '+checklist_experiment_series.toString();
			
			document.getElementById('confirmDeleteButton').setAttribute('onclick', 'deletePermsModal()');
			document.getElementById('confirmDeleteButton').setAttribute('data-dismiss', '');
			document.getElementById('confirmDeleteButton').setAttribute('style', 'display:show');
		}else{
			deletePermsModal();
		}
	}else{
		document.getElementById('myModalLabel').innerHTML = 'Delete Error';
		document.getElementById('deleteLabel').innerHTML = 'Samples/Imports/Experiment Series may only be deleted on the main browser page.';
		
		document.getElementById('cancelDeleteButton').innerHTML = "OK";
		document.getElementById('confirmDeleteButton').setAttribute('style', 'display:none');
	}
}

function deletePermsModal(){
	experimentPerms = [];
	lanePerms = [];
	samplePerms = [];	

	$.ajax({ type: "GET",
			url: BASE_PATH+"/public/ajax/browse_edit.php",
			data: { p: 'getExperimentPermissions', experiments: checklist_experiment_series.toString() },
			async: false,
			success : function(s)
			{
				for(var x = 0; x < s.length; x++){
					experimentPerms.push(s[x].id);
				}
			}
	});
	
	$.ajax({ type: "GET",
			url: BASE_PATH+"/public/ajax/browse_edit.php",
			data: { p: 'getLanePermissions', lanes: checklist_lanes.toString() },
			async: false,
			success : function(s)
			{
				for(var x = 0; x < s.length; x++){
					lanePerms.push(s[x].id);
				}
			}
	});
	
	$.ajax({ type: "GET",
			url: BASE_PATH+"/public/ajax/browse_edit.php",
			data: { p: 'getSamplePermissions', samples: checklist_samples.toString() },
			async: false,
			success : function(s)
			{
				for(var x = 0; x < s.length; x++){
					samplePerms.push(s[x].id);
				}
			}
	});
	console.log(experimentPerms);
	var badExperiments = [];
	var badLanes = [];
	var badSamples = [];
	for (var q = 0; q < checklist_experiment_series.length; q++) {
		if (experimentPerms.indexOf(checklist_experiment_series[q].toString()) == -1) {
			badExperiments.push(checklist_experiment_series[q]);
		}
	}
	for (var q = 0; q < checklist_lanes.length; q++) {
		if (lanePerms.indexOf(checklist_lanes[q].toString()) == -1) {
			badLanes.push(checklist_lanes[q]);
		}
	}
	for (var q = 0; q < checklist_samples.length; q++) {
		if (samplePerms.indexOf(checklist_samples[q].toString()) == -1) {
			badSamples.push(checklist_samples[q]);
		}
	}
	
	document.getElementById('myModalLabel').innerHTML = 'Delete Selected';
	document.getElementById('deleteLabel').innerHTML ='You have permission to delete the following:';
	document.getElementById('deleteAreas').innerHTML = 'Experiment Series id(s): ' + experimentPerms.join(", ") + '<br>Imports id(s): '+ lanePerms.join(", ") + '<br>Samples id(s): ' + samplePerms.join(", ") +
		'<br><br>Experiment Series id(s) lacking permissions: ' + badExperiments.join(", ") + '<br>Import id(s) lacking permissions: ' + badLanes.join(", ") + '<br>Sample id(s) lacking permissions: ' + badSamples.join(", ") +
		'<br><br>If the Import or Sample you want to delete is not accessible, you do not have the correct permissions to remove them.'+
		'<br><br>Be Warned! Deleting Imports/Samples will remove data AND runs accross the system, make sure you have a back up of any of the information you might want to save before deleting.'+
		'<br><br>Data is not recoverable, please make sure you want to delete these.';
		
	document.getElementById('cancelDeleteButton').innerHTML = "Cancel";
	document.getElementById('confirmDeleteButton').setAttribute('style', 'display:show');
	document.getElementById('confirmDeleteButton').setAttribute('onclick', 'confirmDeletePressed()');
}

function confirmDeletePressed(){
		$.ajax({ type: "GET",
				url: BASE_PATH+"/public/ajax/browse_edit.php",
				data: { p: 'deleteSelected', samples: samplePerms.toString(), lanes: lanePerms.toString(), experiments: experimentPerms.toString() },
				async: false,
				success : function(s)
				{
					console.log(s);
					alert();
				}
		});
		
		experimentPerms = [];
		lanePerms = [];
		samplePerms = [];
		
		flushBasketInfo();
		
		location.href = BASE_PATH + '/search';
	}

function cancelDeletePressed(){ 
	experimentPerms = [];
	lanePerms = [];
	samplePerms = [];
}

function initialRunButton(type, id, button){
	$('#deleteModal').modal({
		show: true
	});
	document.getElementById('myModalLabel').innerHTML = 'Information';
	document.getElementById('deleteLabel').innerHTML ='Initial run has not completed yet.';
	document.getElementById('deleteAreas').innerHTML = 'If Import/Sample seems to be taking too long to complete, please contact an Admin at biocore@umassmed.edu';
	
	document.getElementById('cancelDeleteButton').innerHTML = "OK";
	document.getElementById('confirmDeleteButton').setAttribute('style', 'display:none');
}

function clearElementHighlighted(){
	element_highlighted = null;
	element_highlighted_table = null;
	element_highlighted_value = null;
	element_highlighted_id = null;
	element_highlighted_type = null;
	element_highlighted_onclick = null;
}

function resubmitAmazon(){
	$.ajax({ type: "GET",
			url: BASE_PATH+"/public/ajax/browse_edit.php",
			data: { p: 'amazon_reupload', samples: checklist_samples.toString() },
			async: false,
			success : function(s)
			{
				console.log(s);
				window.location.reload();
			}
	});
}

function recheckChecksum() {
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/browse_edit.php",
		data: { p: 'checksum_recheck', samples: checklist_samples.toString() },
		async: false,
		success : function(s)
		{
			console.log(s);
			window.location.reload();
		}
	});
}

function sendToEncode() {
	window.location.href = BASE_PATH+"/public/encode"
}

function sendToEncodeSubmissions() {
	window.location.href = BASE_PATH+"/public/encode/submissions"
}

function allCheckboxCheck(id, type){
	var index = allcheck[type].indexOf(id)
	//Remove
	if (index > -1) {
		allcheck[type].splice(index, 1)
	//Add
	}else{
		allcheck[type].push(id)
	}
}

function removeCombobox($combobox_id){
	console.log('+' + ($('#' + $combobox_id + '_combobox').val()) + '+');
	$('#' + $combobox_id + '_combobox').val('');
	$('#' + $combobox_id + '_div').hide();
}

function addNewFieldCombobox(){
	var $selected =$('#select_fields_combobox option:selected').val(); 
	$('#' + $selected + '_div').show();
}


function getEditableFields(){
	// fields that have regular naming in the database
	var $editable_fields = ['biosample_type', 'donor', 'flowcell', 'genotype', 
	  'instrument_model', 'library_type', 'molecule', 'organism', 'source',
	  'treatment_manufacturer'];
	return $editable_fields;
}

function getDirectlyEditableFields(){
	// fields that have regular naming in the database
	var $directly_editable_fields = ['barcode', 'description', 
		'avg_insert_size', 'read_length', 'concentration', 'time',
		'biological_replica', 'technical_replica', 'spike_ins', 'adapter',
        'notebook_ref', 'notes'];
	return $directly_editable_fields;
}

function updateSelectedSamples(){
	// fields that are connected via id such as organism_id in ngs_samples
	var $editable_fields_i = getEditableFields();
	// fields that are directly columns of ngs_samples
	var $directly_editable_fields = getDirectlyEditableFields();
	var $editable_fields = $editable_fields_i.concat($directly_editable_fields);

	var $table = 'ngs_samples';

	var $sample_list = [];
	$('.editMultipleSelected').each(function(i, obj) {
    	$sample_list.push($(this).attr('id'));
	});

	if($sample_list.length > 0) {
		for (i = 0; i < $editable_fields.length; i++) {
			var $new_value = $("#" + $editable_fields[i] + "_combobox").val();
			if($new_value) {
				console.log($new_value);
				var $type = $editable_fields[i];
				if($editable_fields_i.includes($type)) {
					$type += '_id';
				}
				$.ajax({ type: "POST",
						url: BASE_PATH+"/public/ajax/browse_edit.php",
						data: { p: 'postInsertDatabase', type: $type, table: $table, 
							value: $new_value, sample_ids: $sample_list.join()},
						async: false,
						complete : function(s)
						{
							console.log('type:' + $type + ' table: ' + $table + 
							' value: ' + $new_value + ' sample_ids: ' + $sample_list.join());
							console.log(s);
						}
				});
			}
		}
		location = "";
	}
}

function normalizeName($str){
	// underscore to space
	$str = $str.split("_").join(" ");
	// Capitalize
	$str = $str.replace(/\b\w/g, l => l.toUpperCase());
	return $str;
}

function editMultipleSamples(){
	var $editable_fields_i = getEditableFields();
	var $directly_editable_fields = getDirectlyEditableFields();
	var $editable_fields = $editable_fields_i.concat($directly_editable_fields);
	$editable_fields.sort();

	var combobox_list_string = '';
	var combobox_select_field_string ='<div id="select_fields_div"><div class="inner"><div class="combobox"><div class="ui-widget"><label>Fields To Add&ensp;</label><select id="select_fields_combobox"><option value="">Select one...</option></select></div></div></div></div>';
	$('#selectFieldsToModify').html(combobox_select_field_string);
	$('#select_fields_div').append('<div class="inner"><input type="button" class="btn btn-success" value="Add Field" onClick="addNewFieldCombobox()"/></div><br/><hr><br/>');

	for (i = 0; i < $editable_fields.length; i++) {
      combobox_list_string += '<div id="' + $editable_fields[i] + 
        '_div" style="display:none"><div class="inner"><div class="combobox"><div class="ui-widget"><label>Select ' +
        normalizeName($editable_fields[i]) + '&ensp;</label><select id="' + $editable_fields[i] +
        '_combobox"><option value="">Select one...</option></select></div></div></div>' +
        '<div class="inner"><input type="button" class="btn btn-danger btn-xs" value="X" onClick="removeCombobox(\'' +
        $editable_fields[i] + '\')"/></div>' + '</div>';

     $('#select_fields_combobox').append('<option id="' + $editable_fields[i] +
				  '" value="' + $editable_fields[i] + '">' + normalizeName($editable_fields[i]) +
					'</option>');
	}

	$('#editMultipleSamplesAdd').html(combobox_list_string);

	$('#editMultipleSamplesModal').modal({
		show: true
	});
	comboBoxScript();


	for (i = 0; i < $editable_fields.length; i++) {
		$( "#" + $editable_fields[i] + "_combobox" ).combobox();
		$( "#toggle" ).on( "click", function() {
		    $( "#" + $editable_fields[i] + "_combobox" ).toggle();
		});
	}
	$( "#select_fields_combobox" ).combobox();


	$('ul.ui-widget').css({'z-index' : 999999, 'position' : 'relative', 'max-width' : '25em'});
	$('.ui-icon-triangle-1-s').css({'z-index' : 9999999, 'position' : 'relative'});

	$('.ui-state-default').css({'background-color':'#fff'});
	$('.inner').css({'display': 'inline-block'});
	$('.btn-xs').css({'margin': '10px 20px'});


	for (i = 0; i < $editable_fields_i.length; i++) {
    	$type = $editable_fields_i[i];

    	$.ajax({ type: "GET",
			url: BASE_PATH+"/public/ajax/browse_edit.php",
			data: { p: 'getDropdownValuesWithID', type: $type},
			async: false,
			success : function(s)
			{
				for(var x = 0; x < s.length; x++){
	    			$( "#" + $type + "_combobox"  ).append('<option id="' + s[x]['id'] +
					  '" value="' + s[x]['id'] + '">' + s[x][$type] +
						'</option>');
				}
			}
		});
	}

	for (i = 0; i < $directly_editable_fields.length; i++) {
    	$type = $directly_editable_fields[i];

    	$.ajax({ type: "GET",
			url: BASE_PATH+"/public/ajax/browse_edit.php",
			data: { p: 'getDirectDropdownValues', type: $type},
			async: false,
			success : function(s)
			{
				for(var x = 0; x < s.length; x++){
	    			$( "#" + $type + "_combobox"  ).append('<option id="' + s[x][$type] +
					  '" value="' + s[x][$type] + '">' + s[x][$type] +
						'</option>');
				}
			}
		});
	}

	$('.ui-icon-triangle-1-s').replaceWith('<span class="caret" style="margin:8px"></span>');
	$('.ui-button-text').remove();
	$('.ui-button-icon-only').css({'background-color':'#eee'});

}

function loadAllFromSidebarBrowse(uid, gids, qvar, rvar, theSearch){
	var type = 'samples';
	var queryType = 'getSamples';
	var segment = 'browse';

	var e_save = $('#table_div_experiments table').detach();
	$('#table_div_experiments').empty().append(e_save);
	var i_save = $('#table_div_lanes table').detach();
	$('#table_div_lanes').empty().append(i_save);
	var s_save = $('#table_div_samples table').detach();
	$('#table_div_samples').empty().append(s_save);

	loadSamplesFromSidebarBrowse(type, queryType, rvar, qvar, segment, theSearch, uid, gids);
	loadImportsFromSidebarBrowse(rvar, qvar, segment, theSearch, uid, gids);
	loadExperimentsFromSidebarBrowse(rvar, qvar, segment, theSearch, uid, gids);

	// showAllFilteredTables();
}

function loadSamplesFromSidebarBrowse(type, queryType, rvar, qvar, segment, theSearch, uid, gids){

	$.ajax({ type: "GET",
	url: BASE_PATH+"/public/ajax/ngs_tables.php",
	data: { p: queryType, q: qvar, r: rvar, seg: segment, search: theSearch, uid: uid, gids: gids },
	async: false,
		success : function(s)
		{
			generateStreamTable(type, s, queryType, qvar, rvar, segment, theSearch, uid, gids);
		}
	});

}

function loadImportsFromSidebarBrowse(rvar, qvar, segment, theSearch, uid, gids){ 
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/ngs_tables.php",
		data: { p: "getLanes", q: qvar, r: rvar, seg: segment, search: theSearch, uid: uid, gids: gids },
		async: false,
		success : function(s)
		{
			lane_data = s;
			var type = 'lanes';
			var queryType = "getLanes";
			if (window.location.href.split("/").indexOf('search') > -1) {
				generateStreamTable(type, s, queryType, qvar, rvar, segment, theSearch, uid, gids);
			}
		}
	});
}

function loadExperimentsFromSidebarBrowse(rvar, qvar, segment, theSearch, uid, gids){ 

	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/ngs_tables.php",
		data: { p: "getExperimentSeries", q: qvar, r: rvar, seg: segment, search: theSearch, uid: uid, gids: gids },
		async: false,
		success : function(s)
		{
			experiment_series_data = s;
			var type = 'experiments';
			var queryType = "getExperimentSeries";
			if (window.location.href.split("/").indexOf('search') > -1) {
				generateStreamTable(type, s, queryType, qvar, rvar, segment, theSearch, uid, gids);
			}
		}
	});

}

function comboBoxScript(){    
	$( function() {
	  $.widget( "custom.combobox", {
	    _create: function() {
	      this.wrapper = $( "<span>" )
	        .addClass( "custom-combobox" )
	        .insertAfter( this.element );

	      this.element.hide();
	      this._createAutocomplete();
	      this._createShowAllButton();
	    },

	    _createAutocomplete: function() {
	      var selected = this.element.children( ":selected" ),
	        value = selected.val() ? selected.text() : "";

	      this.input = $( "<input>" )
	        .appendTo( this.wrapper )
	        .val( value )
	        .attr( "title", "" )
	        .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
	        .autocomplete({
	          delay: 0,
	          minLength: 0,
	          source: $.proxy( this, "_source" )
	        })
	        .tooltip({
	          classes: {
	            "ui-tooltip": "ui-state-highlight"
	          }
	        });

	      this._on( this.input, {
	        autocompleteselect: function( event, ui ) {
	          ui.item.option.selected = true;
	          this._trigger( "select", event, {
	            item: ui.item.option
	          });
	        },

	        autocompletechange: "_removeIfInvalid"
	      });
	    },

	    _createShowAllButton: function() {
	      var input = this.input,
	        wasOpen = false;

	      $( "<a>" )
	        .attr( "tabIndex", -1 )
	        .attr( "title", "Show All Items" )
	        .tooltip()
	        .appendTo( this.wrapper )
	        .button({
	          icons: {
	            primary: "ui-icon-triangle-1-s"
	          },
	          text: false
	        })
	        .removeClass( "ui-corner-all" )
	        .addClass( "custom-combobox-toggle ui-corner-right" )
	        .on( "mousedown", function() {
	          wasOpen = input.autocomplete( "widget" ).is( ":visible" );
	        })
	        .on( "click", function() {
	          input.trigger( "focus" );

	          // Close if already visible
	          if ( wasOpen ) {
	            return;
	          }

	          // Pass empty string as value to search for, displaying all results
	          input.autocomplete( "search", "" );
	        });
	    },

	    _source: function( request, response ) {
	      var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
	      response( this.element.children( "option" ).map(function() {
	        var text = $( this ).text();
	        if ( this.value && ( !request.term || matcher.test(text) ) )
	          return {
	            label: text,
	            value: text,
	            option: this
	          };
	      }) );
	    },

	    _removeIfInvalid: function( event, ui ) {

	      // Selected an item, nothing to do
	      if ( ui.item ) {
	        return;
	      }

	      // Search for a match (case-insensitive)
	      var value = this.input.val(),
	        valueLowerCase = value.toLowerCase(),
	        valid = false;
	      this.element.children( "option" ).each(function() {
	        if ( $( this ).text().toLowerCase() === valueLowerCase ) {
	          this.selected = valid = true;
	          return false;
	        }
	      });

	      // Found a match, nothing to do
	      if ( valid ) {
	        return;
	      }
	      



	      var current_div_id = this.input.parents(':eq(4)').attr('id');
	      var $value = this.input.val();
	      var $type = current_div_id.slice(0, -4);
		  var last_id = this.input.parents(':eq(1)').children('select').children('option').last().attr('id');
		  var new_id = parseInt(last_id) + 1;

	    if($type == 'select_fields'){
	    	var $temp_msg = " is not a valid option.";
	    } else {
	    	var $directly_editable_fields = getDirectlyEditableFields();
	    	var $temp_msg = " is now added to the database.";

	    	if($directly_editable_fields.includes($type)){
				var to_add = '<option id="' + $value + '" value="' + $value+ '">' + $value + '</option>';
	    		$temp_msg = " is added as a choice. Please type or select the choice if you would like to update.";
	    	} else {
		    	$.ajax({ type: "POST",
						url: BASE_PATH+"/public/ajax/browse_edit.php",
						data: { p: 'insertFromCombobox', type: $type, value: $value},
						async: false,
						complete : function(s)
						{
							console.log(s);
						}
				});
				var to_add = '<option id="' + new_id + '" value="' + new_id + '">' + $value + '</option>';
	    	}

		    this.input.parents(':eq(1)').children('select').append(to_add);
		    console.log("havuc");
	    }


	      // Remove invalid value
	      this.input
	        .val( "" )
	        .attr( "title", value +  $temp_msg)
	        .tooltip( "open" );
	      this.element.val( "" );
	      this._delay(function() {
	        this.input.tooltip( "close" ).attr( "title", "" );
	      }, 2500 );
	      this.input.autocomplete( "instance" ).term = "";
		  $('#' + $type + '_combobox option:selected').val(new_id + '');
	    },

	    _destroy: function() {
	      // this.wrapper.remove();
	      // this.element.show();
	    }
	  });


	} );

}
