                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Galaxy Run Details
                        <small>Plot Samples</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li><a href="index.php"></a>Usage Reports</li>
                        <li class="active">Galaxy Runs</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="row">
                        <div class="col-md-6">
                            <!-- BAR CHART -->
                            <div class="box box-success">
                                <div class="box-header">
                                    <h3 class="box-title">Daily Galaxy Usage</h3>
                                </div>
                                <div class="box-body chart-responsive">
                                    <div class="chart" id="daily-bar-chart" style="height: 300px;"></div>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                             <?php $html->getBoxTable_stat("User", "Galaxy", "<th>Name</th><th>Lab</th><th>Count</th>"); ?>
                             <?php echo $html->getRespBoxTableStreamNoExpand("Galaxy User Table",
               		            "galaxy_user_stream", ["Name", "Lab", "Count"],
               								["name", "lab", "count"]); ?>

                             <?php $html->getBoxTable_stat("Tool", "Galaxy", "<th>Tool Name</th><th>Count</th>"); ?>
                             <?php echo $html->getRespBoxTableStreamNoExpand("Galaxy Tool Table",
                              "galaxy_tool_stream", ["Tool Name", "Count"],
                              ["tool_name", "count"]); ?>

                        </div><!-- /.col (LEFT) -->
                        <div class="col-md-6">
                            <!-- BAR CHART -->
                            <div class="box box-success">
                                <div class="box-header">
                                    <h3 class="box-title">Top 20 Galaxy Users</h3>
                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <button class="btn btn-primary btn-sm daterange pull-right" data-toggle="tooltip" title="Date range"><i
 class="fa fa-calendar"></i></button>
                                    </div><!-- /. tools -->
                                </div>
                                <div class="box-body chart-responsive">
                                    <div class="chart" id="top-users-bar-chart" style="height: 300px;"></div>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                            <?php $html->getBoxTable_stat("Lab", "Galaxy", "<th>Lab</th><th>Count</th>"); ?>
                            <?php echo $html->getRespBoxTableStreamNoExpand("Galaxy Lab Table",
                             "galaxy_lab_stream", ["Lab", "Count"],
                             ["lab", "count"]); ?>
                        </div><!-- /.col (RIGHT) -->
                    </div><!-- /.row -->

                </section><!-- /.content -->
