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
  <?php
  $experiment_title_list = ["id","Series Name","Summary","Design", "Lab","Organization","Grant", "Selected"];
  $experiment_field_list = ["id","experiment_name","summary","design", "lab","organization","grant", ""];
  $import_title_list = ["id","Import Name","Facility","Total Reads","Total Samples", "Cost", "Phix Requested", "Phix in Lane", "Notes", "Selected"];
  $import_field_list = ["id","name","facility", "total_reads", "total_samples", "cost", "phix_requested", "phix_in_lane", "notes", ""];
  $sample_title_list = ["id","Sample Name","Title","Source","Organism","Molecule", "Barcode", "Backup", "Description", "Avg Insert Size", "Read Length",
                         "Concentration", "Time", "Biological Replica", "Technical Replica", "Spike-ins", "Adapter",
                         "Notebook Ref", "Notes", "Genotype", "Library Type", "Biosample Type", "Instrument Model", "Treatment Manufacturer","Selected"];
  $sample_field_list = ["id","name","title","source","organism","molecule","backup","total_reads", "barcode", "description", "avg_insert_size", "read_length",
                         "concentration", "time", "biological_replica", "technical_replica", "spike_ins", "adapter",
                         "notebook_ref", "notes", "genotype", "library_type", "biosample_type", "instrument_model", "treatment_manufacturer"];
 ?>

<section class="content">
	<div class="row">
		<div class="col-md-12">
  			<!-- general form elements -->
      <div class="nav-tabs-custom">
        <ul id="tabList" class="nav nav-tabs">
          <li class="active">
            <a href="#browse_experiments" data-toggle="tab" aria-expanded="true">Experiments</a>
          </li>
          <li class>
            <a href="#browse_imports" data-toggle="tab" aria-expanded="true">Imports</a>
          </li>
          <li class>
            <a href="#browse_samples" data-toggle="tab" aria-expanded="true">Samples</a>
          </li>
          <li class>
            <a href="#browse_more" data-toggle="tab" aria-expanded="true">More</a>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="browse_experiments">
            <div id="browse_experiment_data_table" class="margin">
              <?php echo $html->getRespBoxTableStreamNoExpand("Experiments",
                "browse_experiment", $experiment_title_list, $experiment_field_list); ?>
            </div>
          </div>
          <div class="tab-pane" id="browse_imports">
            <div id="browse_import_data_table" class="margin">
              <?php echo $html->getRespBoxTableStreamNoExpand("Imports",
                "browse_import", $import_title_list, $import_field_list); ?>
            </div>
          </div>
          <div class="tab-pane" id="browse_samples">
            <div id="browse_sample_data_table" class="margin">
              <?php echo $html->getRespBoxTableStreamNoExpand("Samples",
                "browse_sample", $sample_title_list, $sample_field_list); ?>
            </div>
          </div>
          <div class="tab-pane" id="browse_more">

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</body>
</html>
