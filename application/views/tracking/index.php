<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				Sample Tracking
			</h1>
		</section>
		<!-- Main content -->
		<section class="content">
			<div class="row">

	<?php echo $html->getSubmitBrowserButton()?>
				<div class="col-md-9">

					<?php if(!isset($_SESSION['ngs_samples'])){
						echo $html->getRespBoxTableStream("Samples", "samples", ["id","Sample Name","Title","Source","Organism","Molecule","Backup","Selected"], ["id","name","title","source","organism","molecule","backup","total_reads"]);
					}else if($_SESSION['ngs_samples'] == ''){
						echo $html->getRespBoxTableStream("Samples", "samples", ["id","Sample Name","Title","Source","Organism","Molecule","Backup","Selected"], ["id","name","title","source","organism","molecule","backup","total_reads"]);
					}else{

						echo $html->getRespBoxTableStream2("Samples", "samples", ["id","Sample Name", "Experiment Series", "Lane", "File Name", "Fastq Dir", "Amazon Bucket", "Backup Status", "Selected"],
																				["id","name", "experiment_series", "lane", "file_name", "fastq_dir", "amazon_bucket", "backup", "total_reads"]);
					}?>
				</div><!-- /.col (RIGHT) -->
			</div><!-- /.row -->
			<div class="box-body">
			<div class="box-group" id="accordion">
				<!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
				<?php echo $html->sendJScript("index", "", "", "", $uid, $gids); ?>

			</div>
			</div><!-- /.box-body -->
			<div>
				<?php echo $html->getDolphinBasket()?>
			</div>
		</section><!-- /.content -->
