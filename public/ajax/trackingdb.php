<?php
//error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('report_errors','on');

require_once("../../config/config.php");
require_once("../../includes/dbfuncs.php");
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$query = new dbfuncs();
if (isset($_GET['p'])){$p = $_GET['p'];}

if ($p == 'getTrackingData')
{
	$data=$query->queryTable("SELECT ngs_samples.id AS sample_id,
		  ngs_samples.name AS sample, ngs_experiment_series.experiment_name
		  AS experiment, ngs_lanes.name AS lane,
			ngs_fastq_files.file_name, ngs_dirs.backup_dir,
			ngs_dirs.amazon_bucket
		FROM ngs_samples
		LEFT JOIN ngs_lanes ON ngs_samples.lane_id = ngs_lanes.id
		LEFT JOIN ngs_experiment_series
			ON ngs_samples.series_id = ngs_experiment_series.id
		LEFT JOIN ngs_fastq_files ON ngs_samples.id = ngs_fastq_files.sample_id
		LEFT JOIN ngs_dirs ON ngs_dirs.id = ngs_fastq_files.dir_id
		WHERE ngs_dirs.id=ngs_fastq_files.dir_id and ngs_dirs.amazon_bucket!=''
		and ngs_dirs.amazon_bucket != 'none' and
		(ngs_fastq_files.backup_checksum='' or isnull(ngs_fastq_files.backup_checksum)
		or DATE(ngs_fastq_files.date_modified) <= DATE(NOW() - INTERVAL 2 MONTH))"
    );
}
else if ($p == 'getTrackingDataAmazon')
{
	$data=$query->queryTable("SELECT ngs_samples.name AS sample,
			ngs_samples.id AS sample_id,
			ngs_lanes.name AS lane, ngs_experiment_series.experiment_name
			AS experiment, ngs_fastq_files.file_name, ngs_dirs.backup_dir,
			ngs_dirs.amazon_bucket
		FROM ngs_samples
		LEFT JOIN ngs_lanes ON ngs_samples.lane_id = ngs_lanes.id
		LEFT JOIN ngs_experiment_series
			ON ngs_samples.series_id = ngs_experiment_series.id
		LEFT JOIN ngs_fastq_files ON ngs_samples.id = ngs_fastq_files.sample_id
		LEFT JOIN ngs_dirs ON ngs_dirs.id = ngs_fastq_files.dir_id
		WHERE ngs_dirs.id=ngs_fastq_files.dir_id and ngs_dirs.amazon_bucket!=''
		and ngs_dirs.amazon_bucket != 'none'"
    );
}


if (!headers_sent()) {
   header('Cache-Control: no-cache, must-revalidate');
   header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
   header('Content-type: application/json');
   echo $data;
   exit;
}else{
   echo $data;
}
?>
