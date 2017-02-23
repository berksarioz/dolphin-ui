    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="utf-8">
    <style type=”text/css”>
    td {
      max-width: 150px !important;
      word-wrap: break-word !important;
    }
    </style>
    </head>
    <body>
      <div class="tab-content">
        <div class="tab-pane active" id="all_tracking_data_pane" style="overflow-x:auto;">
          <div id="all_tracking_data_table" class="margin">
            <?php echo $html->getRespBoxTableStreamNoExpand("All Tracking", "generic_tracking",
            ["id","Sample Name", "Experiment Series", "Lane",
                "File Name", "Fastq Dir", "Amazon Bucket", "Backup Status",
                "Selected"],
                ["sample_id", "sample", "experiment", "lane", "file_name",
                "backup_dir", "amazon_bucket", "backup_status", "selected"]); ?>
          </div>
        </div>
        <div class="tab-pane" id="amazon_tracking_data_pane">
          <div id="amazon_tracking_data_table" class="margin">
          </div>
        </div>
      </div>

    </body>
    </html>
