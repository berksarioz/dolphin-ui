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

     $.ajax({ type: "GET",
                     url: BASE_PATH+"/public/ajax/statquerydb.php",
                     data: { p: "getUsersTime", type:"Dolphin" },
                     async: false,
                     success : function(s)
                     {
                       var all_objects = [];
                        for(var i = 0; i < s.length; i++) {
                        all_objects.push({name:s[i].name, lab:s[i].lab,
                          count:s[i].count });
                        } // End For
                        createStreamTable('dolphin_user_stream', all_objects, "",
                          true, [10,20,50,100], 10, true, true);
                     }
            });

     $('.daterange_User').daterangepicker(
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
                        for(var i = 0; i < s.length; i++) {
                        } // End For
                     }
            });

    });



     $.ajax({ type: "GET",
                     url: BASE_PATH+"/public/ajax/statquerydb.php",
                     data: { p: "getLabsTime", type:"Dolphin" },
                     async: false,
                     success : function(s)
                     {
                       var all_objects = [];
                        for(var i = 0; i < s.length; i++) {
                        all_objects.push({lab:s[i].lab,
                          count:s[i].count });
                        } // End For
                        createStreamTable('dolphin_lab_stream', all_objects, "",
                          true, [10,20,50,100], 10, true, true);
                     }
            });

    $('.daterange_Lab').daterangepicker(
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
                        for(var i = 0; i < s.length; i++) {
                        } // End For
                     }
            });

    });

	/*##### SERVICE TABLE #####*/


    $.ajax({ type: "GET",
                     url: BASE_PATH+"/public/ajax/adminstatquerydb.php",
                     data: { p: "getServiceTime", type:"Dolphin" },
                     async: false,
                     success : function(s)
                     {
						console.log(s);
                        var all_objects = [];
                        for(var i = 0; i < s.length; i++) {
                        all_objects.push({servicename:s[i].servicename,
                          count:s[i].count });
                        } // End For
                        createStreamTable('dolphin_service_stream', all_objects, "",
                          true, [10,20,50,100], 10, true, true);
                     }
            });

    $('.daterange_Service').daterangepicker(
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
                     url: BASE_PATH+"/public/ajax/adminstatquerydb.php",
                     data: { p: "getServiceTime", type:"Dolphin", start:start.format('YYYY-MM-DD'), end:end.format('YYYY-MM-DD') },
                     async: false,
                     success : function(s)
                     {
						console.log(s);
                        for(var i = 0; i < s.length; i++) {
                        } // End For
                     }
            });
    });


    /*##### JOB TABLE #####*/


    $.ajax({ type: "GET",
                     url: BASE_PATH+"/public/ajax/statquerydb.php",
                     data: { p: "getJobTime", type:"Dolphin" },
                     async: false,
                     success : function(s)
                     {
                       var all_objects = [];
                        for(var i = 0; i < s.length; i++) {
                        all_objects.push({servicename:s[i].servicename,
                          count:s[i].count });
                        } // End For
                        createStreamTable('dolphin_job_stream', all_objects, "",
                          true, [10,20,50,100], 10, true, true);                     }
            });

    $('.daterange_Job').daterangepicker(
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
                        for(var i = 0; i < s.length; i++) {
                        } // End For
                     }
            });

    });


});
