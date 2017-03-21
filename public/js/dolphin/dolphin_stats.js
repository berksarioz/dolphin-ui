/*
 * Author: Alper Kucukural
 * Date: 26 Nov 2014
 * Description:
 **/



$(function() {



    "use strict";
    /* Morris.js Charts */
     var responseTopDolphinUsers = '';
            $.ajax({ type: "GET",
                     url: BASE_PATH+"/public/ajax/statquerydb.php",
                     data: { p: "getTopUsers", type:"Dolphin" },
                     async: false,
                     success : function(text)
                     {
                         responseTopDolphinUsers = text;
                     }
            });

    /*Bar chart*/
    var barTop = new Morris.Bar({
        element: 'top-users-bar-chart',
        resize: true,
        stacked: true,
        data: responseTopDolphinUsers,
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
        var responseTopDolphinUsers = '';
            $.ajax({ type: "GET",
                     url: BASE_PATH+"/public/ajax/statquerydb.php",
                     data: { p: "getTopUsersTime", type:"Dolphin", start:start.format('YYYY-MM-DD'), end:end.format('YYYY-MM-DD') },
                     async: false,
                     success : function(text)
                     {
                         responseTopDolphinUsers = text;
                     }
            });
	barTop.setData(responseTopDolphinUsers);

    });


    //The Calender
    $("#calendar").datepicker();

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
        ykeys: ['countDolphin'],
        labels: ['Dolphin Runs'],
        hideHover: 'auto'
    });


    var $table_id_list = ['user', 'lab', 'service', 'job'];
    for(var i = 0; i < $table_id_list.length; i++){
        $('#dolphin_' + $table_id_list[i] + '_stream-head').parent().html(
            // '<button class="btn btn-primary btn-sm pull-right" data-toggle="collapse"'+
            // ' title="Collapse" data-target="#table_div_dolphin_' + $table_id_list[i] + '_stream"' +
            // 'style="margin-right: 5px;"><i class="fa fa-minus"></i></button>' +
            '<button id="daterange_' + $table_id_list[i] + '" class="btn ' +
            'btn-primary btn-sm pull-right" data-toggle="tooltip" title="Date' +
            ' range"><i class="fa fa-calendar"></i></button>');
    }
    var $second_vars_list = ['Users', 'Labs', 'Service', 'Job'];


    for(var index = 0; index < $table_id_list.length; index++){
        $.ajax({ type: "GET",
                         url: BASE_PATH+"/public/ajax/statquerydb.php",
                         data: { p: "get" + $second_vars_list[index] + "Time", type:"Dolphin" },
                         async: false,
                         success : function(s)
                         {
                            createStreamTable('dolphin_' + $table_id_list[index] + '_stream', s, "",
                              true, [10,20,50,100], 10, true, true);
                         }
                });
    }


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
                         data: { p: "getUsersTime", type:"Dolphin", start:start.format('YYYY-MM-DD'), end:end.format('YYYY-MM-DD') },
                         async: false,
                         success : function(s)
                         {
                            var save = $('#table_div_dolphin_user_stream table').detach();
                            $('#table_div_dolphin_user_stream').empty().append(save);
                            createStreamTable('dolphin_user_stream', s, "",
                              true, [10,20,50,100], 10, true, true);
                         }
                });

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
                         data: { p: "getLabsTime", type:"Dolphin", start:start.format('YYYY-MM-DD'), end:end.format('YYYY-MM-DD') },
                         async: false,
                         success : function(s)
                         {
                            var save = $('#table_div_dolphin_lab_stream table').detach();
                            $('#table_div_dolphin_lab_stream').empty().append(save);
                            createStreamTable('dolphin_lab_stream', s, "",
                              true, [10,20,50,100], 10, true, true);
                         }
                });

        });


         $('#daterange_service').daterangepicker(
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
                         data: { p: "getServiceTime", type:"Dolphin", start:start.format('YYYY-MM-DD'), end:end.format('YYYY-MM-DD') },
                         async: false,
                         success : function(s)
                         {
                            var save = $('#table_div_dolphin_service_stream table').detach();
                            $('#table_div_dolphin_service_stream').empty().append(save);
                            createStreamTable('dolphin_service_stream', s, "",
                              true, [10,20,50,100], 10, true, true);
                         }
                });

        });


         $('#daterange_job').daterangepicker(
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
                         data: { p: "getJobTime", type:"Dolphin", start:start.format('YYYY-MM-DD'), end:end.format('YYYY-MM-DD') },
                         async: false,
                         success : function(s)
                         {
                            var save = $('#table_div_dolphin_job_stream table').detach();
                            $('#table_div_dolphin_job_stream').empty().append(save);
                            createStreamTable('dolphin_job_stream', s, "",
                              true, [10,20,50,100], 10, true, true);
                         }
                });

        });





});
