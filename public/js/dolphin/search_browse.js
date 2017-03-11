function fillSampleTabl(){
		$.ajax({ type: "GET",
    url: BASE_PATH+"/public/ajax/search_browse.php",
		data: { p: 'getSearchSamples' },
    async: false,
			success : function(s)
			{
        console.log(s);
				$('#browse_sample_data_table').html(s);
        $.getScript( "sessionget_funcs.js");
        $.getScript( "ngstrack_stts.js");
        $.getScript( "ngsget_funcs.js");
        $.getScript( "pipeline_gen_funcs.js");
			}
		});

}
