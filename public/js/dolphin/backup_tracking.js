function getTrackingDataGeneric(){
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/trackingdb.php",
		data: { p: 'getTrackingData' },
		async: false,
		success : function(s)
		{
			var new_json_array = [];
			var uid = s[0].u_id;
			for(var i = 0; i < s.length; i++ ){
				s[i].options = '<div class="btn-group pull-right">' +
				'<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Options ' +
				'<span class="fa fa-caret-down"></span>' +
				'</button>' +
				'<ul class="dropdown-menu" role="menu">' +
				'<li><a href="#" onclick="viewGroupMembers(\''+s[i].id+'\')">View Group Members</a></li>';
					s[i].options += '</div>';
				delete s[i].u_id;
				delete s[i].owner_id;
			}
			groupsStreamTable = createStreamTable('generic_tracking', s, "", true, [20,50], 20, true, true);
		}
	});
}


$(function() {
	"use strict";


	//	GROUPS
	getTrackingDataGeneric();

});
