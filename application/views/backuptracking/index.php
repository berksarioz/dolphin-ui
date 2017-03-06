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
  <?php $title_list = ["id", "Backup Status", "Sample Name", "Experiment Series", "Lane",
      "File Name", "Fastq Dir", "Amazon Bucket",
      "Selected"];
      $fields_list = ["sample_id", "backup_status", "sample", "experiment", "lane", "file_name",
      "backup_dir", "amazon_bucket", "selected"]; ?>

<section class="content">
	<div class="row">
		<div class="col-md-12">
      <div align="right">
        <select>
          <option value=""></option>
          <option value="success">Green</option>
          <option value="warning">Orange</option>
          <option value="flickr">Flickr</option>
          <option value="primary">Blue</option>
          <option value="secondary">Grey</option>
          <option value="danger">Red</option>
        </select>
      </div>
  			<!-- general form elements -->
      <div class="nav-tabs-custom">
        <ul id="tabList" class="nav nav-tabs">
          <li class="active">
            <a href="#filtered" data-toggle="tab" aria-expanded="true">Filtered</a>
          </li>
          <li class>
            <a href="#all" data-toggle="tab" aria-expanded="true">All</a>
          </li>
          <li class>
            <a href="#amazon" data-toggle="tab" aria-expanded="true">Amazon</a>
          </li>
          <li class>
            <a href="#checksum" data-toggle="tab" aria-expanded="true">Checksum</a>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="filtered" style="overflow-x:auto;">
            <div id="all_tracking_data_table" class="margin">
              <?php echo $html->getRespBoxTableStreamNoExpand("Filtered Tracking",
                "generic_tracking", $title_list, $fields_list); ?>
            </div>
          </div>
          <div class="tab-pane" id="all">
            <div id="amazon_tracking_data_table" class="margin">
              <?php echo $html->getRespBoxTableStreamNoExpand("All Tracking",
                "tracking_all", $title_list, $fields_list); ?>
            </div>
          </div>
          <div class="tab-pane" id="amazon">
            <div id="amazon_tracking_data_table" class="margin">
              <?php echo $html->getRespBoxTableStreamNoExpand("Amazon Tracking",
                "tracking_amazon", $title_list, $fields_list); ?>
            </div>
          </div>
          <div class="tab-pane" id="checksum">
            <div id="amazon_tracking_data_table" class="margin">
              <?php echo $html->getRespBoxTableStreamNoExpand("Backup Tracking",
                "tracking_backup", $title_list, $fields_list); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</body>
</html>
