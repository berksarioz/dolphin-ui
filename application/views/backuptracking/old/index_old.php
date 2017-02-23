<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				Sample Tracking
			</h1>
		</section>
		<!-- Main content -->
		<section class="content">
			<div class="row">
				<select>
				  <option value="both">Both</option>
				  <option value="backup_checksum">Backup Checksum</option>
				  <option value="amazon">Amazon</option>
				</select>
	<?php echo $html->getDataSelectionForTracking()?>
				<div class="col-md-12">

					<?php

						echo $html->getRespBoxTableStream2("Samples", "samples", ["id","Sample Name", "Experiment Series", "Lane", "File Name", "Fastq Dir", "Amazon Bucket", "Backup Status", "Selected"],
																				["id","name", "experiment_series", "lane", "file_name", "fastq_dir", "amazon_bucket", "backup", "total_reads"]);
					?>
				</div><!-- /.col (RIGHT) -->
			</div><!-- /.row -->
			<div class="box-body">
			<div class="box-group" id="accordion">
				<!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
				<?php echo $html->sendJScript("index", "", "", "", $uid, $gids); ?>

			</div>
			</div><!-- /.box-body -->
			<div>
				<?php //echo $html->getDolphinBasket()?>
			</div>
		</section><!-- /.content -->
