/*
 * Author: Alper Kucukural
 * Date: 26 Nov 2014
 * Description:
 **/

$(function() {
    "use strict";

    /* Morris.js Charts */
     var responseTopGalaxyUsers = '';
            $.ajax({ type: "GET",
                     url: BASE_PATH+"/public/ajax/statquerydb.php",
                     data: { p: "getTopUsers", type:"Galaxy" },
                     async: false,
                     success : function(text)
                     {
                         responseTopGalaxyUsers = text;
                     }
            });

    /*Bar chart*/
    var barTop = new Morris.Bar({
        element: 'top-users-bar-chart',
        resize: true,
        stacked: true,
        data: responseTopGalaxyUsers,
        barColors: ['#00a65a'],
        xkey: 'name',
        ykeys: ['count'],
        labels: ['# of runs'],
        hideHover: 'auto'
    });

    $('.daterange').daterangepicker(
            {
                ranges: {
                    'Today': [moment().subtract('days', 1), moment()],
                    'Yesterday': [moment().subtract('days', 2), moment().subtract('days', 1)],
                    'Last 7 Days': [moment().subtract('days', 6), moment()],
                    'Last 30 Days': [moment().subtract('days', 29), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
					'This Year': [moment().startOf('year'), moment().endOf('year')],
					'All Time': [moment('2012-01-01'), moment()],
                },
                startDate: moment('2012-01-01'),
                endDate: moment()
            },
    function(start, end) {
        var responseTopGalaxyUsers = '';
            $.ajax({ type: "GET",
                     url: BASE_PATH+"/public/ajax/statquerydb.php",
                     data: { p: "getTopUsersTime", type:"Galaxy", start:start.format('YYYY-MM-DD'), end:end.format('YYYY-MM-DD') },
                     async: false,
                     success : function(text)
                     {
                         responseTopGalaxyUsers = text;
                     }
            });
        barTop.setData(responseTopGalaxyUsers);

    });

    /* Morris.js Charts */
     var responseDaily = '';
            $.ajax({ type: "GET",
                     url: BASE_PATH+"/public/ajax/statquerydb.php",
                     data: { p: "getDailyRuns" },
                     async: false,
                     success : function(text)
                     {
                         responseDaily = text;
                     }
            });

    /*Bar chart*/
    var bar = new Morris.Bar({
        element: 'daily-bar-chart',
        resize: true,
        data: responseDaily,
        barColors: ['#00a65a'],
        xkey: 'day',
        ykeys: ['countTotal'],
        labels: ['Galaxy Runs'],
        hideHover: 'auto'
    });


    var $table_id_list = ['user', 'tool', 'lab'];
    for(var i = 0; i < $table_id_list.length; i++){
        $('#galaxy_' + $table_id_list[i] + '_stream-head').parent().html(
            '<button id="daterange_' + $table_id_list[i] + '" class="btn ' +
            'btn-primary btn-sm pull-right" data-toggle="tooltip" title="Date' +
            ' range"><i class="fa fa-calendar"></i></button>');
    }


        $.ajax({ type: "GET",
                     url: BASE_PATH+"/public/ajax/statquerydb.php",
                     data: { p: "getUsersTime", type:"Galaxy"},
                     async: false,
                     success : function(s)
                     {
                        createStreamTable('galaxy_user_stream', s, "",
                          true, [10,20,50,100], 10, true, true);
                     }
            });

     $('#daterange_user').daterangepicker(
            {
                ranges: {
                    'Today': [moment().subtract('days', 1), moment()],
                    'Yesterday': [moment().subtract('days', 2), moment().subtract('days', 1)],
                    'Last 7 Days': [moment().subtract('days', 6), moment()],
                    'Last 30 Days': [moment().subtract('days', 29), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
					'This Year': [moment().startOf('year'), moment().endOf('year')],
					'All Time': [moment('2012-01-01'), moment()],
                },
                startDate: moment('2012-01-01'),
                endDate: moment()
            },
            function(start, end) {
            $.ajax({ type: "GET",
                     url: BASE_PATH+"/public/ajax/statquerydb.php",
                     data: { p: "getUsersTime", type:"Galaxy", start:start.format('YYYY-MM-DD'), end:end.format('YYYY-MM-DD') },
                     async: false,
                     success : function(s)
                     {
                        for(var i = 0; i < s.length; i++) {
                            var save = $('#table_div_galaxy_user_stream table').detach();
                            $('#table_div_galaxy_user_stream').empty().append(save);
                            createStreamTable('galaxy_user_stream', s, "",
                              true, [10,20,50,100], 10, true, true);
                        } // End For
                     }
            });

    });


                $.ajax({ type: "GET",
                     url: BASE_PATH+"/public/ajax/statquerydb.php",
                     data: { p: "getLabsTime", type:"Galaxy" },
                     async: false,
                     success : function(s)
                     {
                        createStreamTable('galaxy_lab_stream', s, "",
                          true, [10,20,50,100], 10, true, true);
                     }
            });
    $('#daterange_lab').daterangepicker(
            {
                ranges: {
                    'Today': [moment().subtract('days', 1), moment()],
                    'Yesterday': [moment().subtract('days', 2), moment().subtract('days', 1)],
                    'Last 7 Days': [moment().subtract('days', 6), moment()],
                    'Last 30 Days': [moment().subtract('days', 29), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
					'This Year': [moment().startOf('year'), moment().endOf('year')],
					'All Time': [moment('2012-01-01'), moment()],
                },
                startDate: moment('2012-01-01'),
                endDate: moment()
            },
            function(start, end) {
            $.ajax({ type: "GET",
                     url: BASE_PATH+"/public/ajax/statquerydb.php",
                     data: { p: "getLabsTime", type:"Galaxy", start:start.format('YYYY-MM-DD'), end:end.format('YYYY-MM-DD') },
                     async: false,
                     success : function(s)
                     {
                        for(var i = 0; i < s.length; i++) {
                            var save = $('#table_div_galaxy_lab_stream table').detach();
                            $('#table_div_galaxy_lab_stream').empty().append(save);
                            createStreamTable('galaxy_lab_stream', s, "",
                              true, [10,20,50,100], 10, true, true);
                        } // End For
                     }
            });

    });


    $.ajax({ type: "GET",
                     url: BASE_PATH+"/public/ajax/statquerydb.php",
                     data: { p: "getToolTime", type:"Galaxy"},
                     async: false,
                     success : function(s)
                     {
                        createStreamTable('galaxy_tool_stream', s, "",
                          true, [10,20,50,100], 10, true, true);                     }
            });
    $('#daterange_tool').daterangepicker(
            {
                ranges: {
                    'Today': [moment().subtract('days', 1), moment()],
                    'Yesterday': [moment().subtract('days', 2), moment().subtract('days', 1)],
                    'Last 7 Days': [moment().subtract('days', 6), moment()],
                    'Last 30 Days': [moment().subtract('days', 29), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
					'This Year': [moment().startOf('year'), moment().endOf('year')],
					'All Time': [moment('2012-01-01'), moment()],
                },
                startDate: moment('2012-01-01'),
                endDate: moment()
            },
    function(start, end) {
            $.ajax({ type: "GET",
                     url: BASE_PATH+"/public/ajax/statquerydb.php",
                     data: { p: "getToolTime", type:"Galaxy", start:start.format('YYYY-MM-DD'), end:end.format('YYYY-MM-DD') },
                     async: false,
                     success : function(s)
                     {
                        for(var i = 0; i < s.length; i++) {
                            var save = $('#table_div_galaxy_tool_stream table').detach();
                            $('#table_div_galaxy_tool_stream').empty().append(save);
                            createStreamTable('galaxy_tool_stream', s, "",
                              true, [10,20,50,100], 10, true, true);
                        } // End For
                     }
            });
    });

});
